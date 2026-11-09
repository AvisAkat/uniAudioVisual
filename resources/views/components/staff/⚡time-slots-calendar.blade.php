<?php

use App\Models\Booking;
use App\Models\Equipment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    private const STATUSES = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'];

    public string $selectedDate = '';

    public string $calendarMonth = '';

    public string $equipmentFilter = 'all';

    public string $statusFilter = 'All';

    public string $search = '';

    public ?int $viewingBookingId = null;

    public bool $showBookingModal = false;

    public function mount(): void
    {
        $this->authorizeStaff();
        $this->selectedDate = today()->toDateString();
        $this->calendarMonth = today()->startOfMonth()->toDateString();
    }

    public function equipmentOptions()
    {
        $this->authorizeStaff();

        return Equipment::query()->orderBy('name')->get();
    }

    public function calendarDays(): array
    {
        $month = $this->validCalendarMonth();
        $start = $month->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $month->endOfMonth()->endOfWeek(Carbon::MONDAY);
        $dates = [];

        for ($date = $start; $date->lte($end); $date = $date->addDay()) {
            $dates[] = $date;
        }

        return $dates;
    }

    public function calendarBookingCounts(): array
    {
        $this->authorizeStaff();

        $dates = $this->calendarDays;

        return Booking::query()
            ->whereBetween('booking_date', [$dates[0]->toDateString(), $dates[array_key_last($dates)]->toDateString()])
            ->when($this->equipmentFilter !== 'all', fn (Builder $query) => $query->where('equipment_id', (int) $this->equipmentFilter))
            ->selectRaw('booking_date, COUNT(*) as booking_count')
            ->groupBy('booking_date')
            ->get()
            ->mapWithKeys(fn (Booking $booking): array => [
                Carbon::parse($booking->booking_date)->toDateString() => (int) $booking->booking_count,
            ])
            ->all();
    }

    public function selectedDaySummary(): object
    {
        $this->authorizeStaff();

        return Booking::query()
            ->whereDate('booking_date', $this->validSelectedDate()->toDateString())
            ->when($this->equipmentFilter !== 'all', fn (Builder $query) => $query->where('equipment_id', (int) $this->equipmentFilter))
            ->selectRaw("
                COUNT(*) as booking_count,
                SUM(CASE WHEN status IN ('Pending', 'Approved') THEN quantity ELSE 0 END) as active_units,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved_count
            ")
            ->first();
    }

    public function slotAvailability(): array
    {
        $this->authorizeStaff();

        $slots = $this->bookingSlots();
        $equipment = $this->equipmentOptions;
        $activeBookings = Booking::query()
            ->whereDate('booking_date', $this->validSelectedDate()->toDateString())
            ->whereIn('status', ['Pending', 'Approved'])
            ->when($this->equipmentFilter !== 'all', fn (Builder $query) => $query->where('equipment_id', (int) $this->equipmentFilter))
            ->selectRaw('equipment_id, start_time, end_time, SUM(quantity) as reserved_quantity')
            ->groupBy('equipment_id', 'start_time', 'end_time')
            ->get()
            ->keyBy(fn (Booking $booking): string => $booking->equipment_id . '|' . $this->timeKey($booking->start_time) . '|' . $this->timeKey($booking->end_time));

        $result = [];

        foreach ($slots as $slotKey => $slot) {
            $slotEquipment = $equipment
                ->when($this->equipmentFilter !== 'all', fn ($items) => $items->where('id', (int) $this->equipmentFilter))
                ->map(function (Equipment $item) use ($activeBookings, $slot): array {
                    $bookingKey = $item->id . '|' . $slot['start'] . '|' . $slot['end'];
                    $reserved = (int) ($activeBookings->get($bookingKey)?->reserved_quantity ?? 0);
                    $bookableQuantity = $item->condition === 'Good'
                        ? min($item->total_quantity, $item->available_quantity)
                        : 0;

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'condition' => $item->condition,
                        'total' => $item->total_quantity,
                        'reserved' => $reserved,
                        'available' => max(0, $bookableQuantity - $reserved),
                    ];
                })
                ->values();

            $result[$slotKey] = [
                'label' => $slot['label'],
                'reserved' => $slotEquipment->sum('reserved'),
                'available' => $slotEquipment->sum('available'),
                'equipment' => $slotEquipment,
            ];
        }

        return $result;
    }

    public function dayBookings(): LengthAwarePaginator
    {
        $this->authorizeStaff();

        return $this->bookingQuery()
            ->orderBy('start_time')
            ->orderBy('equipment_id')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function selectedBooking(): ?Booking
    {
        $this->authorizeStaff();

        return $this->viewingBookingId === null
            ? null
            : Booking::query()->with(['user', 'equipment'])->find($this->viewingBookingId);
    }

    public function bookingSlots(): array
    {
        return [
            'slot-1' => ['label' => '7:30 AM – 10:30 AM', 'start' => '07:30:00', 'end' => '10:30:00'],
            'slot-2' => ['label' => '11:00 AM – 2:00 PM', 'start' => '11:00:00', 'end' => '14:00:00'],
            'slot-3' => ['label' => '2:15 PM – 5:15 PM', 'start' => '14:15:00', 'end' => '17:15:00'],
        ];
    }

    public function updatedSelectedDate(): void
    {
        $this->authorizeStaff();
        $this->validateOnly('selectedDate', ['selectedDate' => ['required', 'date']]);
        $this->calendarMonth = CarbonImmutable::parse($this->selectedDate)->startOfMonth()->toDateString();
        $this->resetPage();
    }

    public function updatedCalendarMonth(): void
    {
        $this->authorizeStaff();
        $this->validateOnly('calendarMonth', ['calendarMonth' => ['required', 'date']]);
        $this->calendarMonth = CarbonImmutable::parse($this->calendarMonth)->startOfMonth()->toDateString();
    }

    public function updatedEquipmentFilter(): void
    {
        $this->authorizeStaff();

        if ($this->equipmentFilter !== 'all') {
            validator(
                ['equipmentFilter' => $this->equipmentFilter],
                ['equipmentFilter' => ['required', 'integer', Rule::exists('equipment', 'id')]]
            )->validate();
        }

        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->authorizeStaff();

        if ($this->statusFilter !== 'All' && ! in_array($this->statusFilter, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'statusFilter' => 'Select a valid booking status.',
            ]);
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->authorizeStaff();
        $this->validateOnly('search', ['search' => ['nullable', 'string', 'max:255']]);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->authorizeStaff();
        $this->reset('equipmentFilter', 'statusFilter', 'search');
        $this->resetPage();
        $this->resetValidation();
    }

    public function selectDate(string $date): void
    {
        $this->authorizeStaff();
        validator(['date' => $date], ['date' => ['required', 'date']])->validate();

        $selected = CarbonImmutable::parse($date);
        $this->selectedDate = $selected->toDateString();
        $this->calendarMonth = $selected->startOfMonth()->toDateString();
        $this->resetPage();
        $this->resetValidation('selectedDate');
    }

    public function moveMonth(int $direction): void
    {
        $this->authorizeStaff();

        if (! in_array($direction, [-1, 1], true)) {
            throw ValidationException::withMessages([
                'calendarMonth' => 'Choose a valid calendar direction.',
            ]);
        }

        $this->calendarMonth = $this->validCalendarMonth()
            ->addMonths($direction)
            ->startOfMonth()
            ->toDateString();
    }

    public function goToToday(): void
    {
        $this->authorizeStaff();
        $this->selectedDate = today()->toDateString();
        $this->calendarMonth = today()->startOfMonth()->toDateString();
        $this->resetPage();
        $this->resetValidation();
    }

    public function viewBooking(int $bookingId): void
    {
        $this->authorizeStaff();

        $booking = Booking::query()->findOrFail($bookingId);
        abort_unless($booking->booking_date->toDateString() === $this->validSelectedDate()->toDateString(), 404);
        abort_unless($this->equipmentFilter === 'all' || $booking->equipment_id === (int) $this->equipmentFilter, 404);

        $this->viewingBookingId = $booking->id;
        $this->showBookingModal = true;
    }

    public function closeBookingModal(): void
    {
        $this->authorizeStaff();
        $this->showBookingModal = false;
        $this->viewingBookingId = null;
        unset($this->selectedBooking);
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-' . $booking->booking_date->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    public function statusClass(string $status): string
    {
        return 'status-' . strtolower($status);
    }

    public function formattedTime(string $time): string
    {
        return Carbon::parse($time)->format('g:i A');
    }

    private function bookingQuery(): Builder
    {
        $this->authorizeStaff();

        $term = trim($this->search);

        return Booking::query()
            ->with(['user', 'equipment'])
            ->whereDate('booking_date', $this->validSelectedDate()->toDateString())
            ->when($this->equipmentFilter !== 'all', fn (Builder $query) => $query->where('equipment_id', (int) $this->equipmentFilter))
            ->when($this->statusFilter !== 'All', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($term !== '', function (Builder $query) use ($term): void {
                $likeTerm = '%' . $term . '%';
                preg_match('/(?:^|-)0*(\d+)$/i', $term, $referenceMatch);

                $query->where(function (Builder $query) use ($likeTerm, $referenceMatch): void {
                    $query->whereHas('user', fn (Builder $userQuery) => $userQuery
                        ->where('name', 'like', $likeTerm)
                        ->orWhere('university_id', 'like', $likeTerm))
                        ->orWhereHas('equipment', fn (Builder $equipmentQuery) => $equipmentQuery
                            ->where('name', 'like', $likeTerm)
                            ->orWhere('asset_code', 'like', $likeTerm)
                            ->orWhere('category', 'like', $likeTerm))
                        ->orWhere('purpose', 'like', $likeTerm)
                        ->orWhere('status', 'like', $likeTerm);

                    if ($referenceMatch !== []) {
                        $query->orWhereKey((int) $referenceMatch[1]);
                    }
                });
            });
    }

    private function validSelectedDate(): CarbonImmutable
    {
        try {
            validator(['date' => $this->selectedDate], ['date' => ['required', 'date']])->validate();
        } catch (ValidationException) {
            return today()->toImmutable();
        }

        return CarbonImmutable::parse($this->selectedDate);
    }

    private function validCalendarMonth(): CarbonImmutable
    {
        try {
            validator(['date' => $this->calendarMonth], ['date' => ['required', 'date']])->validate();
        } catch (ValidationException) {
            return today()->startOfMonth()->toImmutable();
        }

        return CarbonImmutable::parse($this->calendarMonth);
    }

    private function timeKey(string $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()?->role === 'staff', 403);
    }
};
?>

<div wire:keydown.escape.window="closeBookingModal">
    <section class="app-view is-entering" id="view-slots" aria-label="Time slot management">
        <div id="slots-content">
            <div class="section-heading">
                <div>
                    <h2>Reservation calendar</h2>
                    <p>Navigate dates, inspect occupied sessions and identify remaining equipment capacity.</p>
                </div>
                <div class="section-actions">
                    <div class="control-group">
                        <label for="slot-date">Selected date</label>
                        <input id="slot-date" type="date" wire:model.live="selectedDate">
                        @error('selectedDate') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="control-group">
                        <label for="slot-equipment">Equipment</label>
                        <select id="slot-equipment" wire:model.live="equipmentFilter">
                            <option value="all">All equipment</option>
                            @foreach ($this->equipmentOptions as $equipment)
                                <option wire:key="calendar-equipment-{{ $equipment->id }}" value="{{ $equipment->id }}">
                                    {{ $equipment->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipmentFilter') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="calendar-layout">
                <section class="card calendar-card" aria-label="Monthly booking calendar">
                    <div class="calendar-header">
                        <strong>{{ $this->validCalendarMonth()->format('F Y') }}</strong>
                        <div>
                            <button class="icon-button" type="button" wire:click="moveMonth(-1)" aria-label="Previous month">
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                            </button>
                            <button class="icon-button" type="button" wire:click="moveMonth(1)" aria-label="Next month">
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-weekdays" aria-hidden="true">
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                            <span wire:key="calendar-weekday-{{ $weekday }}">{{ $weekday }}</span>
                        @endforeach
                    </div>
                    <div class="calendar-grid">
                        @foreach ($this->calendarDays as $date)
                            @php($dayKey = $date->toDateString())
                            @php($bookingCount = $this->calendarBookingCounts[$dayKey] ?? 0)
                            <button
                                class="calendar-day {{ $date->format('Y-m') !== $this->validCalendarMonth()->format('Y-m') ? 'outside' : '' }} {{ $dayKey === today()->toDateString() ? 'is-today' : '' }} {{ $dayKey === $this->validSelectedDate()->toDateString() ? 'is-selected' : '' }} {{ $bookingCount > 0 ? 'has-bookings' : '' }}"
                                type="button"
                                wire:key="calendar-day-{{ $dayKey }}"
                                wire:click="selectDate('{{ $dayKey }}')"
                                aria-label="{{ $date->format('j M Y') }}: {{ $bookingCount }} booking(s)"
                                aria-pressed="{{ $dayKey === $this->validSelectedDate()->toDateString() ? 'true' : 'false' }}"
                                title="{{ $bookingCount }} booking(s)"
                            >{{ $date->day }}</button>
                        @endforeach
                    </div>
                    <div class="calendar-legend">
                        <button class="button button-outline" type="button" wire:click="goToToday">Go to today</button>
                        <span><i class="legend-dot"></i> Selected / today</span>
                        <span><i class="legend-dot has-bookings"></i> Has bookings</span>
                    </div>
                </section>

                <div>
                    <div class="availability-summary">
                        <div class="mini-stat">
                            <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span>
                            <div><strong>{{ (int) ($this->selectedDaySummary->booking_count ?? 0) }}</strong><small>Booking record(s) on selected date</small></div>
                        </div>
                        <div class="mini-stat">
                            <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v9l9 5 9-5V8M12 13v9"></path></svg></span>
                            <div><strong>{{ (int) ($this->selectedDaySummary->active_units ?? 0) }}</strong><small>Units in pending or approved bookings</small></div>
                        </div>
                        <div class="mini-stat">
                            <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                            <div><strong>{{ (int) ($this->selectedDaySummary->pending_count ?? 0) }} / {{ (int) ($this->selectedDaySummary->approved_count ?? 0) }}</strong><small>Pending / approved requests</small></div>
                        </div>
                    </div>

                    <div class="section-heading">
                        <div>
                            <h2>{{ $this->validSelectedDate()->format('l, j M Y') }}</h2>
                            <p>{{ $equipmentFilter === 'all' ? 'All equipment' : ($this->equipmentOptions->firstWhere('id', (int) $equipmentFilter)?->name ?? 'Selected equipment') }} · pending and approved reservations count against capacity.</p>
                        </div>
                    </div>

                    <div class="slot-grid">
                        @foreach ($this->slotAvailability as $slotKey => $slot)
                            <article class="slot-card {{ $slot['available'] === 0 ? 'is-unavailable' : '' }}" wire:key="calendar-slot-{{ $slotKey }}-{{ $selectedDate }}-{{ $equipmentFilter }}">
                                <div class="slot-card-top">
                                    <div class="slot-time">
                                        <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                                        <strong>{{ $slot['label'] }}</strong>
                                    </div>
                                    <span class="status-badge {{ $slot['available'] > 0 ? 'status-available' : 'status-unavailable' }}">
                                        {{ $slot['available'] > 0 ? 'Capacity available' : 'No capacity' }}
                                    </span>
                                </div>
                                <div class="slot-stock">
                                    <strong>{{ $slot['available'] }} <small>available</small></strong>
                                    <span>{{ $slot['reserved'] }} active reserved unit(s)</span>
                                </div>
                                <div class="slot-equipment-list">
                                    @forelse ($slot['equipment'] as $item)
                                        <div class="slot-card-footer" wire:key="slot-equipment-{{ $slotKey }}-{{ $item['id'] }}">
                                            <span>{{ $item['name'] }}</span>
                                            <span>{{ $item['reserved'] }} reserved · {{ $item['available'] }} available</span>
                                        </div>
                                    @empty
                                        <div class="slot-card-footer"><span>No equipment in inventory</span></div>
                                    @endforelse
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="section-heading">
                <div>
                    <h2>Selected date reservation records</h2>
                    <p>Pending, approved and historical requests for this date.</p>
                </div>
            </div>
            <div class="filter-bar">
                <div class="search-control">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg></span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search reference, student or equipment" aria-label="Search daily bookings">
                    @error('search') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="control-group">
                    <label for="calendar-status">Status</label>
                    <select id="calendar-status" wire:model.live="statusFilter">
                        <option value="All">All statuses</option>
                        @foreach (['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'] as $status)
                            <option wire:key="calendar-status-{{ $status }}" value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('statusFilter') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button class="button button-outline" type="button" wire:click="resetFilters">Reset filters</button>
            </div>
            <div class="results-meta">
                <span>{{ $this->dayBookings->total() }} matching booking(s)</span>
                <span>{{ $this->selectedDaySummary->pending_count ?? 0 }} pending · {{ $this->selectedDaySummary->approved_count ?? 0 }} approved</span>
            </div>
            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Equipment</th>
                                <th>Quantity Reserved</th>
                                <th>Student</th>
                                <th>Booking Status</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->dayBookings as $booking)
                                <tr wire:key="calendar-booking-{{ $booking->id }}">
                                    <td data-label="Date">{{ $booking->booking_date->format('j M Y') }}</td>
                                    <td data-label="Time slot">{{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}</td>
                                    <td data-label="Equipment"><span class="cell-primary">{{ $booking->equipment?->name ?? 'Equipment unavailable' }}</span><span class="cell-secondary">{{ $booking->equipment?->category ?? '—' }}</span></td>
                                    <td data-label="Quantity Reserved">{{ $booking->quantity }}</td>
                                    <td data-label="Student"><span class="cell-primary">{{ $booking->user?->name ?? 'Unknown student' }}</span><span class="cell-secondary">{{ $booking->user?->university_id ?? '—' }}</span></td>
                                    <td data-label="Booking Status"><span class="status-badge {{ $this->statusClass($booking->status) }}">{{ $booking->status }}</span></td>
                                    <td data-label="Reference"><button class="reference-button" type="button" wire:click="viewBooking({{ $booking->id }})">{{ $this->bookingReference($booking) }}</button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state compact">
                                            <h3>No bookings for this date</h3>
                                            <p>Choose another date or adjust the equipment, status, or search filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($this->dayBookings->hasPages())
                <div class="pagination-wrap">{{ $this->dayBookings->links() }}</div>
            @endif
        </div>
    </section>

    @if ($showBookingModal && $this->selectedBooking)
        @php($booking = $this->selectedBooking)
        <div class="modal-backdrop" wire:click.self="closeBookingModal">
            <div class="modal modal-large" role="dialog" aria-modal="true" aria-labelledby="calendar-booking-title">
                <div class="modal-header">
                    <div class="modal-heading">
                        <span class="modal-heading-icon">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path></svg>
                        </span>
                        <div><h2 id="calendar-booking-title">Booking details</h2><p>{{ $this->bookingReference($booking) }}</p></div>
                    </div>
                    <button class="icon-button modal-close" type="button" wire:click="closeBookingModal" aria-label="Close dialog">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="detail-reference">
                        <code>{{ $this->bookingReference($booking) }}</code>
                        <span class="status-badge {{ $this->statusClass($booking->status) }}">{{ $booking->status }}</span>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Student</label><p>{{ $booking->user?->name ?? 'Unknown student' }}<br><span class="muted">{{ $booking->user?->university_id ?? '—' }}</span></p></div>
                        <div class="detail-item"><label>Submitted</label><p>{{ $booking->created_at?->format('j M Y, H:i') ?? '—' }}</p></div>
                        <div class="detail-item"><label>Equipment</label><p>{{ $booking->equipment?->name ?? 'Equipment unavailable' }}<br><span class="muted">{{ $booking->equipment?->category ?? '—' }}</span></p></div>
                        <div class="detail-item"><label>Quantity</label><p>{{ $booking->quantity }} unit(s)</p></div>
                        <div class="detail-item"><label>Booking date</label><p>{{ $booking->booking_date->format('l, j M Y') }}</p></div>
                        <div class="detail-item"><label>Time slot</label><p>{{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}</p></div>
                        <div class="detail-item full"><label>Purpose</label><p>{{ $booking->purpose ?: 'No purpose provided.' }}</p></div>
                        <div class="detail-item full"><label>Additional notes</label><p>{{ $booking->additional_notes ?: 'No additional notes.' }}</p></div>
                    </div>
                    @if ($booking->rejection_reason)
                        <div class="rejection-box"><strong>Rejection reason</strong><p>{{ $booking->rejection_reason }}</p></div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="button button-outline" type="button" wire:click="closeBookingModal">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
