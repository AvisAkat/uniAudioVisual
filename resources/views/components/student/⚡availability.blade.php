<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $equipmentId = null;

    public string $availabilityDate = '';

    public bool $showBookingModal = false;

    public string $bookingSlot = 'slot-1';

    public int $bookingQuantity = 1;

    public string $bookingPurpose = '';

    public string $bookingNotes = '';

    public function mount(): void
    {
        $this->availabilityDate = now()->toDateString();
        $this->equipmentId = Equipment::query()->orderBy('name')->value('id');
    }

    #[Computed]
    public function equipmentOptions(): Collection
    {
        return Equipment::query()->orderBy('name')->get();
    }

    #[Computed]
    public function selectedEquipment(): ?Equipment
    {
        return $this->equipmentId === null
            ? null
            : Equipment::query()->find($this->equipmentId);
    }

    public function availabilitySlots(): array
    {
        return [
            'slot-1' => ['label' => '7:30 AM – 10:30 AM', 'start' => '07:30:00', 'end' => '10:30:00'],
            'slot-2' => ['label' => '11:00 AM – 2:00 PM', 'start' => '11:00:00', 'end' => '14:00:00'],
            'slot-3' => ['label' => '2:15 PM – 5:15 PM', 'start' => '14:15:00', 'end' => '17:15:00'],
        ];
    }

    #[Computed]
    public function slotAvailability(): array
    {
        $equipment = $this->selectedEquipment;
        $slots = $this->availabilitySlots();

        if ($equipment === null || ! $this->isValidAvailabilityDate()) {
            return array_fill_keys(array_keys($slots), [
                'total' => 0,
                'reserved' => 0,
                'available' => 0,
                'percentage' => 0,
            ]);
        }

        $reservedBySlot = Booking::query()
            ->where('equipment_id', $equipment->id)
            ->whereDate('booking_date', $this->availabilityDate)
            ->whereIn('status', ['Pending', 'Approved'])
            ->selectRaw('start_time, end_time, SUM(quantity) as reserved_quantity')
            ->groupBy('start_time', 'end_time')
            ->get()
            ->keyBy(fn (Booking $booking): string => Carbon::parse($booking->start_time)->format('H:i:s').'|'.Carbon::parse($booking->end_time)->format('H:i:s'));

        $bookableTotal = $equipment->condition === 'Good'
            ? min($equipment->total_quantity, $equipment->available_quantity)
            : 0;

        $availability = [];

        foreach ($slots as $slotKey => $slot) {
            $reserved = (int) ($reservedBySlot->get($slot['start'].'|'.$slot['end'])?->reserved_quantity ?? 0);
            $available = max(0, $bookableTotal - $reserved);

            $availability[$slotKey] = [
                'total' => $equipment->total_quantity,
                'reserved' => $reserved,
                'available' => $available,
                'percentage' => $equipment->total_quantity > 0
                    ? (int) round(($available / $equipment->total_quantity) * 100)
                    : 0,
            ];
        }

        return $availability;
    }

    #[Computed]
    public function availabilitySummary(): array
    {
        $availability = $this->slotAvailability;
        $bookableSlots = 0;

        foreach ($availability as $slotKey => $slotAvailability) {
            $slotStart = $this->availabilitySlots()[$slotKey]['start'];
            $isFutureSlot = $this->availabilityDate > now()->toDateString()
                || ($this->availabilityDate === now()->toDateString() && $slotStart > now()->format('H:i:s'));

            if ($slotAvailability['available'] > 0 && $isFutureSlot) {
                $bookableSlots++;
            }
        }

        return [
            'slots' => $bookableSlots,
            'available' => array_sum(array_column($availability, 'available')),
            'reserved' => array_sum(array_column($availability, 'reserved')),
        ];
    }

    #[Computed]
    public function weekDates(): array
    {
        $selectedDate = $this->isValidAvailabilityDate()
            ? Carbon::parse($this->availabilityDate)
            : now()->startOfDay();
        $weekStart = $selectedDate->copy()->startOfWeek(\Carbon\Constants\UnitValue::MONDAY);

        return collect(range(0, 6))
            ->map(fn (int $offset): Carbon => $weekStart->copy()->addDays($offset))
            ->all();
    }

    public function selectDate(string $date): void
    {
        $this->validateDate($date);
        $this->availabilityDate = $date;
        unset($this->slotAvailability, $this->availabilitySummary);
    }

    public function moveWeek(int $direction): void
    {
        if (! in_array($direction, [-1, 1], true)) {
            return;
        }

        $currentDate = $this->isValidAvailabilityDate()
            ? Carbon::parse($this->availabilityDate)
            : now()->startOfDay();
        $targetDate = $currentDate->copy()->addWeeks($direction);

        if ($targetDate->lt(now()->startOfWeek(\Carbon\Constants\UnitValue::MONDAY))) {
            $targetDate = now()->startOfDay();
        }

        $this->availabilityDate = $targetDate->toDateString();
        unset($this->slotAvailability, $this->availabilitySummary);
    }

    public function goToNextAvailableDay(): void
    {
        $this->authorizeStudent();
        $equipment = $this->selectedEquipment;

        if ($equipment === null) {
            return;
        }

        $selectedDate = $this->isValidAvailabilityDate()
            ? Carbon::parse($this->availabilityDate)
            : now()->startOfDay();
        $startDate = max(
            now()->toDateString(),
            $selectedDate->copy()->addDay()->toDateString()
        );
        $endDate = Carbon::parse($startDate)->addDays(90)->toDateString();
        $bookableTotal = $equipment->condition === 'Good'
            ? min($equipment->total_quantity, $equipment->available_quantity)
            : 0;

        if ($bookableTotal < 1) {
            $this->addError('equipmentId', 'This equipment is not currently bookable.');

            return;
        }

        $reservedByDayAndSlot = Booking::query()
            ->where('equipment_id', $equipment->id)
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('status', ['Pending', 'Approved'])
            ->selectRaw('booking_date, start_time, SUM(quantity) as reserved_quantity')
            ->groupBy('booking_date', 'start_time')
            ->get()
            ->groupBy(fn (Booking $booking): string => Carbon::parse($booking->booking_date)->toDateString());

        for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
            $dayBookings = $reservedByDayAndSlot->get($date->toDateString(), collect());

            foreach ($this->availabilitySlots() as $slot) {
                $reserved = (int) ($dayBookings
                    ->first(fn (Booking $booking): bool => Carbon::parse($booking->start_time)->format('H:i:s') === $slot['start']
                        && Carbon::parse($booking->end_time)->format('H:i:s') === $slot['end'])
                    ?->reserved_quantity ?? 0);

                if ($bookableTotal > $reserved) {
                    $this->availabilityDate = $date->toDateString();
                    unset($this->slotAvailability, $this->availabilitySummary);

                    return;
                }
            }
        }

        $this->addError('availabilityDate', 'No availability was found in the next 90 days.');
    }

    public function openBooking(int $equipmentId, string $date, string $slot): void
    {
        $this->authorizeStudent();
        $this->validateDate($date);

        if (! array_key_exists($slot, $this->availabilitySlots())) {
            abort(404);
        }

        Equipment::query()->findOrFail($equipmentId);
        $this->equipmentId = $equipmentId;
        $this->availabilityDate = $date;
        $this->bookingSlot = $slot;
        $this->bookingQuantity = 1;
        $this->bookingPurpose = '';
        $this->bookingNotes = '';
        $this->resetValidation();
        $this->showBookingModal = true;
        unset($this->selectedEquipment, $this->slotAvailability, $this->availabilitySummary);
    }

    public function closeBookingModal(): void
    {
        $this->showBookingModal = false;
        $this->bookingQuantity = 1;
        $this->bookingPurpose = '';
        $this->bookingNotes = '';
        $this->resetValidation();
    }

    public function createBooking(): void
    {
        $this->authorizeStudent();

        $validated = $this->validate([
            'equipmentId' => ['required', 'integer', Rule::exists('equipment', 'id')],
            'availabilityDate' => ['required', 'date', 'after_or_equal:today'],
            'bookingSlot' => ['required', Rule::in(array_keys($this->availabilitySlots()))],
            'bookingQuantity' => ['required', 'integer', 'min:1'],
            'bookingPurpose' => ['required', 'string', 'min:4', 'max:2000'],
            'bookingNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        $slot = $this->availabilitySlots()[$validated['bookingSlot']];

        if ($validated['availabilityDate'] === now()->toDateString() && $slot['start'] <= now()->format('H:i:s')) {
            $this->addError('bookingSlot', 'Choose a time slot that has not started yet.');

            return;
        }

        $errorMessage = DB::transaction(function () use ($validated, $slot): ?string {
            $equipment = Equipment::query()->lockForUpdate()->findOrFail($validated['equipmentId']);

            if ($equipment->condition !== 'Good') {
                return 'This equipment is not currently available for booking.';
            }

            $reserved = Booking::query()
                ->where('equipment_id', $equipment->id)
                ->whereDate('booking_date', $validated['availabilityDate'])
                ->where('start_time', $slot['start'])
                ->where('end_time', $slot['end'])
                ->whereIn('status', ['Pending', 'Approved'])
                ->sum('quantity');
            $bookableTotal = min($equipment->total_quantity, $equipment->available_quantity);
            $available = max(0, $bookableTotal - (int) $reserved);

            if ($validated['bookingQuantity'] > $available) {
                return "Only {$available} unit(s) are available for this date and slot.";
            }

            $booking = Booking::query()->create([
                'user_id' => auth()->id(),
                'equipment_id' => $equipment->id,
                'booking_date' => $validated['availabilityDate'],
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'quantity' => $validated['bookingQuantity'],
                'purpose' => $validated['bookingPurpose'],
                'additional_notes' => $validated['bookingNotes'] ?: null,
                'status' => 'Pending',
            ]);

            ActivityLog::record(
                'Booking submitted',
                'Booking',
                "Submitted a request for {$booking->quantity} × {$equipment->name} on {$validated['availabilityDate']} ({$slot['label']}).",
                $booking,
            );

            Notification::sendToRoles(
                ['staff', 'admin'],
                'New booking request',
                "A request for {$booking->quantity} × {$equipment->name} on {$validated['availabilityDate']} needs review.",
                'info',
                $booking,
            );

            return null;
        });

        if ($errorMessage !== null) {
            $this->addError('bookingQuantity', $errorMessage);
            unset($this->slotAvailability, $this->availabilitySummary);

            return;
        }

        $this->closeBookingModal();
        unset($this->slotAvailability, $this->availabilitySummary);
        $this->dispatch(
            'toast',
            title: 'Booking request submitted',
            message: 'Your equipment request is pending staff review.',
            type: 'success'
        );
    }

    private function validateDate(string $date): void
    {
        validator(
            ['date' => $date],
            ['date' => ['required', 'date', 'after_or_equal:today']]
        )->validate();
    }

    private function isValidAvailabilityDate(): bool
    {
        try {
            validator(
                ['date' => $this->availabilityDate],
                ['date' => ['required', 'date']]
            )->validate();

            return true;
        } catch (\Illuminate\Validation\ValidationException) {
            return false;
        }
    }

    private function authorizeStudent(): void
    {
        abort_unless(auth()->user()?->role === 'student', 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-availability" aria-label="Equipment availability">
        <div id="availability-content">
            <div class="section-heading">
                <div>
                    <h2>Plan around real capacity</h2>
                    <p>Select equipment and a date. Each slot shows active reservations against usable stock.</p>
                </div>
                <div class="section-actions">
                    <a class="button button-outline" href="{{ route('student.browse_equipment') }}">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="13" rx="2"></rect>
                            <path d="M8 21h8M12 17v4M7 8h10"></path>
                        </svg>
                        Equipment catalogue
                    </a>
                </div>
            </div>

            <div class="filter-bar">
                <div class="control-group" style="flex: 1">
                    <label for="availability-equipment">Equipment to check</label>
                    <select id="availability-equipment" wire:model.live="equipmentId">
                        @forelse ($this->equipmentOptions as $equipment)
                            <option value="{{ $equipment->id }}">{{ $equipment->name }} — {{ $equipment->category }}</option>
                        @empty
                            <option value="">No equipment available</option>
                        @endforelse
                    </select>
                    @error('equipmentId') <small class="field-error">{{ $message }}</small> @enderror
                </div>
                <div class="control-group">
                    <label for="availability-date">Preferred date</label>
                    <input
                        id="availability-date"
                        type="date"
                        min="{{ now()->toDateString() }}"
                        wire:model.live="availabilityDate"
                    >
                    @error('availabilityDate') <small class="field-error">{{ $message }}</small> @enderror
                </div>
                <button class="button button-outline" type="button" wire:click="goToNextAvailableDay">
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                    </svg>
                    Next available day
                </button>
            </div>

            <div class="date-strip">
                <button
                    class="date-strip-nav"
                    type="button"
                    wire:click="moveWeek(-1)"
                    aria-label="Previous week"
                    @disabled($this->weekDates[0]->lte(now()->startOfWeek(Carbon::MONDAY)))
                >
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                </button>
                @foreach ($this->weekDates as $date)
                    <button
                        class="date-strip-day {{ $date->toDateString() === $availabilityDate ? 'is-selected' : '' }}"
                        type="button"
                        wire:key="availability-date-{{ $date->toDateString() }}"
                        wire:click="selectDate('{{ $date->toDateString() }}')"
                        @disabled($date->lt(now()->startOfDay()))
                        aria-pressed="{{ $date->toDateString() === $availabilityDate ? 'true' : 'false' }}"
                    >
                        <span>{{ $date->format('D') }}</span>
                        <strong>{{ $date->format('j') }}</strong>
                        <small>{{ $date->format('M') }}</small>
                    </button>
                @endforeach
                <button
                    class="date-strip-nav"
                    type="button"
                    wire:click="moveWeek(1)"
                    aria-label="Next week"
                >
                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </div>

            <div class="availability-summary">
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span>
                    <div><strong>{{ $this->availabilitySummary['slots'] }}</strong><small>Bookable slots on selected date</small></div>
                </div>
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v9l9 5 9-5V8M12 13v9"></path></svg></span>
                    <div><strong>{{ $this->availabilitySummary['available'] }}</strong><small>Total slot-unit availability</small></div>
                </div>
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                    <div><strong>{{ $this->availabilitySummary['reserved'] }}</strong><small>Reserved slot-units across the day</small></div>
                </div>
            </div>

            @if ($this->selectedEquipment)
                <div class="section-heading">
                    <div>
                        <h2>{{ Carbon::parse($availabilityDate)->format('l, j M Y') }}</h2>
                        <p>{{ $this->selectedEquipment->name }} · {{ $this->selectedEquipment->total_quantity }} total units · {{ $this->selectedEquipment->condition }}</p>
                    </div>
                </div>

                <div class="slot-grid">
                    @foreach ($this->availabilitySlots() as $slotKey => $slot)
                        @php
                            $stock = $this->slotAvailability[$slotKey];
                            $isPastDate = $availabilityDate < now()->toDateString();
                            $slotHasStarted = $availabilityDate === now()->toDateString() && $slot['start'] <= now()->format('H:i:s');
                            $canBookSlot = $stock['available'] > 0 && $this->selectedEquipment->condition === 'Good' && ! $isPastDate && ! $slotHasStarted;
                            $slotStatus = $isPastDate ? 'Past' : ($canBookSlot ? 'Available' : 'Unavailable');
                        @endphp
                        <article class="slot-card {{ $stock['available'] === 0 ? 'is-unavailable' : '' }}" wire:key="availability-{{ $slotKey }}-{{ $availabilityDate }}">
                            <div class="slot-card-top">
                                <div class="slot-time">
                                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                                    <strong>{{ $slot['label'] }}</strong>
                                </div>
                                <span class="status-badge {{ $canBookSlot ? 'status-available' : 'status-unavailable' }}">
                                    {{ $slotStatus }}
                                </span>
                            </div>
                            <div class="slot-stock">
                                <strong>{{ $stock['available'] }}<small> / {{ $stock['total'] }}</small></strong>
                                <span>{{ $stock['reserved'] }} unit(s) reserved</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width: {{ $stock['percentage'] }}%"></div>
                            </div>
                            <div class="slot-card-footer">
                                <span>{{ $isPastDate ? 'Past date' : ($slotHasStarted ? 'This slot has started' : ($canBookSlot ? 'Ready to request' : 'No units available')) }}</span>
                                <button
                                    class="button button-small button-primary"
                                    type="button"
                                    wire:click="openBooking({{ $this->selectedEquipment->id }}, '{{ $availabilityDate }}', '{{ $slotKey }}')"
                                    wire:loading.attr="disabled"
                                    @disabled(! $canBookSlot)
                                >Book slot</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <h3>No equipment to display</h3>
                    <p>Add equipment to the inventory before checking availability.</p>
                </div>
            @endif
        </div>
    </section>

    @if ($showBookingModal)
        @php
            $bookingStock = $this->slotAvailability[$bookingSlot] ?? ['total' => 0, 'reserved' => 0, 'available' => 0];
            $selectedSlot = $this->availabilitySlots()[$bookingSlot];
        @endphp
        <div class="modal-backdrop" wire:click.self="closeBookingModal" role="presentation">
            <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <form wire:submit="createBooking">
                    <div class="modal-header">
                        <div class="modal-title-group">
                            <span class="modal-icon"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 3v4M16 3v4M4 10h16"></path></svg></span>
                            <div>
                                <h2 id="modal-title">Book equipment</h2>
                                <p>Request a quantity for the selected date and time slot.</p>
                            </div>
                        </div>
                        <button class="icon-button modal-close" type="button" wire:click="closeBookingModal" aria-label="Close dialog">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="field-group">
                            <label for="booking-equipment">Equipment type <span class="required">*</span></label>
                            <select id="booking-equipment" wire:model.live="equipmentId" required>
                                @foreach ($this->equipmentOptions as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }} — {{ $equipment->category }}</option>
                                @endforeach
                            </select>
                            @error('equipmentId') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-grid two-columns">
                            <div class="field-group">
                                <label for="booking-date">Booking date <span class="required">*</span></label>
                                <input id="booking-date" type="date" min="{{ now()->toDateString() }}" wire:model.live="availabilityDate" required>
                                @error('availabilityDate') <small class="field-error">{{ $message }}</small> @enderror
                            </div>
                            <div class="field-group">
                                <label for="booking-slot">Available time slot <span class="required">*</span></label>
                                <select id="booking-slot" wire:model.live="bookingSlot" required>
                                    @foreach ($this->availabilitySlots() as $slotId => $slot)
                                        <option value="{{ $slotId }}">{{ $slot['label'] }} — {{ $this->slotAvailability[$slotId]['available'] }} available</option>
                                    @endforeach
                                </select>
                                @error('bookingSlot') <small class="field-error">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="availability-panel">
                            <div><small>Total stock</small><strong>{{ $bookingStock['total'] }}</strong></div>
                            <div><small>Already reserved</small><strong>{{ $bookingStock['reserved'] }}</strong></div>
                            <div><small>Available to you</small><strong>{{ $bookingStock['available'] }}</strong></div>
                        </div>
                        <div class="availability-message">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-6"></path></svg>
                            <span>{{ $bookingStock['available'] }} unit(s) available on {{ Carbon::parse($availabilityDate)->format('j M Y') }} during {{ $selectedSlot['label'] }}.</span>
                        </div>
                        <div class="field-group">
                            <label for="booking-quantity">Quantity required <span class="required">*</span></label>
                            <input id="booking-quantity" type="number" min="1" max="{{ max(1, $bookingStock['available']) }}" step="1" wire:model="bookingQuantity" required>
                            @error('bookingQuantity') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="field-group">
                            <label for="booking-purpose">Purpose of booking <span class="required">*</span></label>
                            <input id="booking-purpose" wire:model="bookingPurpose" required minlength="4" maxlength="2000" placeholder="e.g. Group presentation">
                            @error('bookingPurpose') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="field-group">
                            <label for="booking-notes">Additional notes <span class="muted-label">(optional)</span></label>
                            <textarea id="booking-notes" wire:model="bookingNotes" maxlength="2000" placeholder="Room number, accessories or setup details"></textarea>
                            @error('bookingNotes') <small class="field-error">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-note">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v6M12 7h.01"></path></svg>
                            <span>Pending requests temporarily reserve stock and require staff approval.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="button button-outline" type="button" wire:click="closeBookingModal">Cancel</button>
                        <button class="button button-primary" type="submit" wire:loading.attr="disabled" @disabled($bookingStock['available'] < 1)>
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg>
                            Submit booking request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
