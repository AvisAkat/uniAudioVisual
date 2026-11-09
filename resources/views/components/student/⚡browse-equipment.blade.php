<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public string $categoryFilter = 'All';

    public bool $showBookingModal = false;

    public ?int $bookingEquipmentId = null;

    public string $bookingDate = '';

    public string $bookingSlot = 'slot-1';

    public int $bookingQuantity = 1;

    public string $bookingPurpose = '';

    public string $bookingNotes = '';

    public function mount(): void
    {
        $schedule = $this->defaultBookingSchedule();
        $this->bookingDate = $schedule['date'];
        $this->bookingSlot = $schedule['slot'];
    }

    #[Computed]
    public function equipmentItems(): Collection
    {
        return Equipment::query()
            ->when($this->search !== '', function (Builder $query): void {
                $searchTerm = '%'.$this->search.'%';

                $query->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('name', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhere('asset_code', 'like', $searchTerm);
                });
            })
            ->when($this->categoryFilter !== 'All', fn (Builder $query) => $query->where('category', $this->categoryFilter))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function categoryCounts(): array
    {
        return Equipment::query()
            ->select('category')
            ->selectRaw('COUNT(*) as equipment_count')
            ->groupBy('category')
            ->orderBy('category')
            ->pluck('equipment_count', 'category')
            ->all();
    }

    public function selectCategory(string $category): void
    {
        $allowedCategories = array_merge(['All'], array_keys($this->categoryCounts));

        if (! in_array($category, $allowedCategories, true)) {
            throw ValidationException::withMessages([
                'categoryFilter' => 'Select a valid equipment category.',
            ]);
        }

        $this->categoryFilter = $category;
        unset($this->equipmentItems);
    }

    #[Computed]
    public function bookingEquipmentOptions(): Collection
    {
        return Equipment::query()
            ->orderBy('name')
            ->get();
    }

    public function bookingSlots(): array
    {
        return [
            'slot-1' => ['label' => '7:30 AM – 10:30 AM', 'start' => '07:30:00', 'end' => '10:30:00'],
            'slot-2' => ['label' => '11:00 AM – 2:00 PM', 'start' => '11:00:00', 'end' => '14:00:00'],
            'slot-3' => ['label' => '2:15 PM – 5:15 PM', 'start' => '14:15:00', 'end' => '17:15:00'],
        ];
    }

    #[Computed]
    public function equipmentToBook(): ?Equipment
    {
        return $this->bookingEquipmentId === null
            ? null
            : Equipment::query()->find($this->bookingEquipmentId);
    }

    #[Computed]
    public function bookingAvailability(): array
    {
        $equipment = $this->equipmentToBook;
        $slot = $this->bookingSlots()[$this->bookingSlot] ?? null;

        if ($equipment === null || $slot === null || $this->bookingDate === '') {
            return ['total' => 0, 'reserved' => 0, 'available' => 0];
        }

        $reserved = Booking::query()
            ->where('equipment_id', $equipment->id)
            ->whereDate('booking_date', $this->bookingDate)
            ->where('start_time', $slot['start'])
            ->where('end_time', $slot['end'])
            ->whereIn('status', ['Pending', 'Approved'])
            ->sum('quantity');

        $total = $equipment->total_quantity;
        $bookableQuantity = $equipment->condition === 'Good'
            ? min($total, $equipment->available_quantity)
            : 0;

        return [
            'total' => $total,
            'reserved' => (int) $reserved,
            'available' => max(0, $bookableQuantity - (int) $reserved),
        ];
    }

    public function openBooking(int $equipmentId): void
    {
        $this->authorizeStudent();

        $equipment = Equipment::query()->findOrFail($equipmentId);

        $this->resetBookingForm();
        $this->bookingEquipmentId = $equipment->id;
        $this->showBookingModal = true;
    }

    public function closeBookingModal(): void
    {
        $this->showBookingModal = false;
        $this->resetBookingForm();
    }

    public function createBooking(): void
    {
        $this->authorizeStudent();

        $validated = $this->validate([
            'bookingEquipmentId' => ['required', 'integer', Rule::exists('equipment', 'id')],
            'bookingDate' => ['required', 'date', 'after_or_equal:today'],
            'bookingSlot' => ['required', Rule::in(array_keys($this->bookingSlots()))],
            'bookingQuantity' => ['required', 'integer', 'min:1'],
            'bookingPurpose' => ['required', 'string', 'min:4', 'max:2000'],
            'bookingNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        $slot = $this->bookingSlots()[$validated['bookingSlot']];

        if ($validated['bookingDate'] === now()->toDateString() && $slot['start'] <= now()->format('H:i:s')) {
            $this->addError('bookingSlot', 'Choose a time slot that has not started yet.');

            return;
        }

        $errorMessage = DB::transaction(function () use ($validated, $slot): ?string {
            $equipment = Equipment::query()
                ->lockForUpdate()
                ->findOrFail($validated['bookingEquipmentId']);

            if ($equipment->condition !== 'Good') {
                return 'This equipment is not currently available for booking.';
            }

            $reserved = Booking::query()
                ->where('equipment_id', $equipment->id)
                ->whereDate('booking_date', $validated['bookingDate'])
                ->where('start_time', $slot['start'])
                ->where('end_time', $slot['end'])
                ->whereIn('status', ['Pending', 'Approved'])
                ->sum('quantity');
            $bookableQuantity = $equipment->condition === 'Good'
                ? min($equipment->total_quantity, $equipment->available_quantity)
                : 0;
            $available = max(0, $bookableQuantity - (int) $reserved);

            if ($validated['bookingQuantity'] > $available) {
                return "Only {$available} unit(s) are available for this date and time slot.";
            }

            $booking = Booking::query()->create([
                'user_id' => auth()->id(),
                'equipment_id' => $equipment->id,
                'booking_date' => $validated['bookingDate'],
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
                "Submitted a request for {$booking->quantity} × {$equipment->name} on {$validated['bookingDate']} ({$slot['label']}).",
                $booking,
            );

            Notification::sendToRoles(
                ['staff', 'admin'],
                'New booking request',
                "A request for {$booking->quantity} × {$equipment->name} on {$validated['bookingDate']} needs review.",
                'info',
                $booking,
            );

            return null;
        });

        if ($errorMessage !== null) {
            $this->addError('bookingQuantity', $errorMessage);

            return;
        }

        $this->closeBookingModal();
        unset($this->bookingAvailability);

        $this->dispatch(
            'toast',
            title: 'Booking request submitted',
            message: 'Your equipment request is pending staff review.',
            type: 'success'
        );
    }

    private function resetBookingForm(): void
    {
        $schedule = $this->defaultBookingSchedule();

        $this->resetValidation();
        $this->bookingEquipmentId = null;
        $this->bookingDate = $schedule['date'];
        $this->bookingSlot = $schedule['slot'];
        $this->bookingQuantity = 1;
        $this->bookingPurpose = '';
        $this->bookingNotes = '';
        unset($this->equipmentToBook, $this->bookingAvailability, $this->bookingEquipmentOptions);
    }

    /**
     * @return array{date: string, slot: string}
     */
    private function defaultBookingSchedule(): array
    {
        $now = now();

        foreach ($this->bookingSlots() as $slotValue => $slot) {
            if ($slot['start'] > $now->format('H:i:s')) {
                return ['date' => $now->toDateString(), 'slot' => $slotValue];
            }
        }

        return ['date' => $now->addDay()->toDateString(), 'slot' => 'slot-1'];
    }

    private function authorizeStudent(): void
    {
        abort_unless(auth()->user()?->role === 'student', 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-equipment" aria-label="Browse equipment">
        <div id="equipment-content">
            <div class="section-heading">
                <div>
                    <h2>Equipment catalogue</h2>
                    <p>Choose a date and one of the three time slots when requesting equipment.</p>
                </div>
                <div class="section-actions">
                    <a href="{{ route('student.check_availability') }}">
                        <button class="button button-outline" type="button" data-view="availability">
                            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg> Compare all slots
                        </button>
                    </a>
                </div>
            </div>
            <div class="filter-bar">
                <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg></span><input id="equipment-search" type="search" value=""
                        placeholder="Search equipment name or feature" aria-label="Search equipment"
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="control-group"><label for="equipment-date">Availability date</label><input
                        id="equipment-date" type="date" min="2026-11-08" value="2026-11-11"></div>
                <div class="control-group"><label for="equipment-slot">Time slot</label><select id="equipment-slot">
                        @foreach ($this->bookingSlots() as $slotValue => $slot)
                            <option value="{{ $slotValue }}">{{ $slot['label'] }}</option>
                        @endforeach
                    </select></div>

            </div>
            <div class="category-tabs" aria-label="Equipment categories">
                <button class="category-tab {{ $categoryFilter === 'All' ? 'is-active' : '' }}" type="button"
                    wire:click="selectCategory('All')">All
                    Equipment<span>{{ array_sum($this->categoryCounts) }}</span></button>
                @foreach ($this->categoryCounts as $category => $count)
                    <button class="category-tab {{ $categoryFilter === $category ? 'is-active' : '' }}" type="button"
                        wire:key="equipment-category-{{ $loop->index }}"
                        wire:click="selectCategory(@js($category))">{{ $category }}<span>{{ $count }}</span></button>
                @endforeach
            </div>
            <div class="results-meta">
                <span>Showing {{ $this->equipmentItems->count() }} of {{ array_sum($this->categoryCounts) }} equipment
                    type(s)</span>
                <span>Availability reflects current inventory stock</span>
            </div>
            <div class="equipment-grid">
                @forelse ($this->equipmentItems as $equipment)
                    @php
                        $visualClass = match ($equipment->category) {
                            'Projectors' => 'projector',
                            'HDMI Cables' => 'cable',
                            'Extension Boards' => 'power',
                            'Audio Systems' => 'audio',
                            default => '',
                        };
                        $isAvailable = $equipment->available_quantity > 0 && $equipment->condition === 'Good';
                    @endphp
                    <article class="equipment-card" wire:key="student-equipment-{{ $equipment->id }}">
                        <div class="equipment-visual {{ $visualClass }}">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                @switch($equipment->category)
                                @case('Projectors')
                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                    <circle cx="15" cy="12.5" r="2.5"></circle>
                                    <path d="M7 11h3M7 14h2M6 18v2M18 18v2M6 7V5h5v2"></path>
                                    @break
                                @case('HDMI Cables')
                                    <path d="M8 3v5M5 3v5M4 8h5v3a3 3 0 0 1-3 3H5a3 3 0 0 0-3 3v4M16 21v-5M19 21v-5M15 16h5v-3a3 3 0 0 1 3-3h0a3 3 0 0 0 3-3V3" transform="translate(-2 0) scale(.95)"></path>
                                    @break
                                @case('Extension Boards')
                                    <rect x="2" y="8" width="20" height="8" rx="2"></rect>
                                    <path d="M6 11v2M10 11v2M14 11v2M18 11v2M12 8V5a3 3 0 0 1 3-3h2"></path>
                                    @break
                                @case('Audio Systems')
                                    <rect x="5" y="2" width="14" height="20" rx="3"></rect>
                                    <circle cx="12" cy="15" r="4"></circle>
                                    <circle cx="12" cy="7" r="2"></circle>
                                    <path d="M9 22h6"></path>
                                    @break
                                @default
                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                    <path d="M7 11h10M7 14h6M6 18v2M18 18v2"></path>
                            @endswitch
                            </svg>
                            <span class="visual-category">{{ $equipment->category }}</span>
                            <span class="status-badge {{ $isAvailable ? 'status-available' : 'status-unavailable' }}">
                                {{ $isAvailable ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                        <div class="equipment-card-body">
                            <h3>{{ $equipment->name }}</h3>
                            <p class="equipment-description">{{ $equipment->description }}</p>
                            <div class="condition-line"><span>Condition</span><span
                                    class="condition-badge status-{{ \Illuminate\Support\Str::slug($equipment->condition) }}">{{ $equipment->condition }}</span>
                            </div>
                            <div class="stock-row">
                                <div class="stock-item"><small>Total
                                        quantity</small><strong>{{ $equipment->total_quantity }}
                                        <span>units</span></strong></div>
                                <div class="stock-item {{ $isAvailable ? 'available' : '' }}"><small>Available
                                        now</small><strong>{{ $equipment->available_quantity }} <span>of
                                            {{ $equipment->total_quantity }}</span></strong></div>
                            </div>
                            <button class="button button-primary" type="button"
                                wire:click="openBooking({{ $equipment->id }})" @disabled(!$isAvailable)>
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book Equipment
                            </button>
                        </div>
                    </article>
                @empty
                    <p class="empty-state">No equipment matches your search.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Book Equipment Modal --}}
    @if ($showBookingModal && $this->equipmentToBook)
        <div class="modal-backdrop" id="booking-modal-backdrop">
            <div class="modal modal-large" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <div id="modal-content">
                    <form id="booking-form" wire:submit="createBooking">
                        <div class="modal-header">
                            <div class="modal-heading"><span class="modal-heading-icon "><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                        <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01">
                                        </path>
                                    </svg></span>
                                <div>
                                    <h2 id="modal-title">Book equipment</h2>
                                    <p>Choose a date and time to see the exact quantity available.</p>
                                </div>
                            </div><button class="icon-button modal-close" type="button" wire:click="closeBookingModal"
                                aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M18 6 6 18M6 6l12 12"></path>
                                </svg></button>
                        </div>
                        <div class="modal-body">
                            <div class="field-group"><label for="booking-equipment">Equipment type <span
                                        class="required">*</span></label><select id="booking-equipment"
                                    wire:model.live="bookingEquipmentId" required>
                                    @foreach ($this->bookingEquipmentOptions as $equipmentOption)
                                        <option value="{{ $equipmentOption->id }}">{{ $equipmentOption->name }} —
                                            {{ $equipmentOption->category }}</option>
                                    @endforeach
                                </select>
                                @error('bookingEquipmentId') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-grid two-columns">
                                <div class="field-group"><label for="booking-date">Booking date <span
                                            class="required">*</span></label><input id="booking-date"
                                        wire:model.live="bookingDate" type="date" min="{{ now()->toDateString() }}"
                                        required>
                                    @error('bookingDate') <span class="field-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="field-group"><label for="booking-slot">Available time slot <span
                                            class="required">*</span></label><select id="booking-slot"
                                        wire:model.live="bookingSlot" required>
                                        @foreach ($this->bookingSlots() as $slotValue => $slot)
                                            <option value="{{ $slotValue }}">{{ $slot['label'] }}</option>
                                        @endforeach
                                    </select></div>
                                @error('bookingSlot') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="availability-panel">
                                <div><small>Total stock</small><strong>{{ $this->bookingAvailability['total'] }}</strong>
                                </div>
                                <div><small>Already
                                        reserved</small><strong>{{ $this->bookingAvailability['reserved'] }}</strong></div>
                                <div><small>Available to
                                        you</small><strong>{{ $this->bookingAvailability['available'] }}</strong></div>
                            </div>
                            <div
                                class="availability-message {{ $this->bookingAvailability['available'] > 0 ? '' : 'is-error' }}">
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                    @if ($this->bookingAvailability['available'] > 0)
                                        <path d="m8 12 3 3 5-6"></path>
                                    @else
                                        <path d="M12 8v5m0 4h.01"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                    @endif
                                </svg>
                                <span>{{ $this->bookingAvailability['available'] }} unit(s) available for
                                    {{ $this->bookingDate }} during
                                    {{ $this->bookingSlots()[$this->bookingSlot]['label'] ?? '' }}.</span>
                            </div>
                            <div class="field-group"><label for="booking-quantity">Quantity required <span
                                        class="required">*</span></label><input id="booking-quantity"
                                    wire:model="bookingQuantity" type="number" min="1" step="1" required
                                    max="{{ $this->bookingAvailability['available'] }}">
                                @error('bookingQuantity') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="field-group"><label for="booking-purpose">Purpose of booking <span
                                        class="required">*</span></label><input id="booking-purpose"
                                    wire:model="bookingPurpose" required minlength="4"
                                    placeholder="e.g. Database Systems group presentation">
                                @error('bookingPurpose') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="field-group"><label for="booking-notes">Additional notes <span
                                        class="muted-label">(optional)</span></label><textarea id="booking-notes"
                                    wire:model="bookingNotes"
                                    placeholder="Room number, accessories or setup details staff should know"></textarea>
                                @error('bookingNotes') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-note"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 11v6M12 7h.01"></path>
                                </svg><span>Pending requests temporarily reserve stock. Requests expire automatically after
                                    the selected slot if staff have not reviewed them.</span></div>
                        </div>
                        <div class="modal-footer"><button class="button button-outline" type="button"
                                wire:click="closeBookingModal">Cancel</button><button class="button button-primary"
                                id="submit-booking" type="submit" wire:loading.attr="disabled"
                                @disabled($this->bookingAvailability['available'] < 1)><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg> Submit booking request</button></div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>