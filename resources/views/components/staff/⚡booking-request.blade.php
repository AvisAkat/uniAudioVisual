<?php

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    private const STATUSES = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'];

    private const SCOPES = ['all', 'upcoming', 'today', 'past'];

    private const SORTS = ['newest', 'oldest', 'date-asc', 'date-desc', 'student', 'status'];

    public string $search = '';

    public string $statusFilter = 'All';

    public string $scheduleFilter = 'all';

    public string $dateFilter = '';

    public string $sortBy = 'newest';

    public ?int $viewingBookingId = null;

    public bool $showBookingModal = false;

    public bool $showDecisionModal = false;

    public bool $showReturnModal = false;

    public string $decision = 'approve';

    public string $rejectionReason = '';

    public function mount(): void
    {
        $this->authorizeStaff();
    }

    #[Computed]
    public function bookingItems()
    {
        return $this->bookingQuery()->paginate(15);
    }

    #[Computed]
    public function bookingCounts(): object
    {
        $this->authorizeStaff();

        return Booking::query()
            ->selectRaw("
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
            ")
            ->first();
    }

    #[Computed]
    public function selectedBooking(): ?Booking
    {
        $this->authorizeStaff();

        if ($this->viewingBookingId === null) {
            return null;
        }

        return Booking::query()
            ->with(['user', 'equipment'])
            ->find($this->viewingBookingId);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        if ($this->statusFilter !== 'All' && ! in_array($this->statusFilter, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'statusFilter' => 'Select a valid booking status.',
            ]);
        }

        $this->resetPage();
    }

    public function updatedScheduleFilter(): void
    {
        if (! in_array($this->scheduleFilter, self::SCOPES, true)) {
            throw ValidationException::withMessages([
                'scheduleFilter' => 'Select a valid booking schedule.',
            ]);
        }

        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->validateOnly('dateFilter', [
            'dateFilter' => ['nullable', 'date'],
        ]);

        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        if (! in_array($this->sortBy, self::SORTS, true)) {
            throw ValidationException::withMessages([
                'sortBy' => 'Select a valid booking sort order.',
            ]);
        }

        $this->resetPage();
    }

    public function showPendingBookings(): void
    {
        $this->authorizeStaff();
        $this->search = '';
        $this->statusFilter = 'Pending';
        $this->scheduleFilter = 'all';
        $this->dateFilter = '';
        $this->sortBy = 'newest';
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->authorizeStaff();
        $this->reset('search', 'statusFilter', 'scheduleFilter', 'dateFilter', 'sortBy');
        $this->resetPage();
    }

    public function viewBooking(int $bookingId): void
    {
        $this->authorizeStaff();
        Booking::query()->findOrFail($bookingId);

        $this->viewingBookingId = $bookingId;
        $this->showBookingModal = true;
    }

    public function closeBookingModal(): void
    {
        $this->showBookingModal = false;
        $this->viewingBookingId = null;
        unset($this->selectedBooking);
    }

    public function openDecisionModal(int $bookingId, string $decision): void
    {
        $this->authorizeStaff();

        if (! in_array($decision, ['approve', 'reject'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Choose whether to approve or reject this request.',
            ]);
        }

        $booking = Booking::query()->findOrFail($bookingId);

        if ($booking->status !== 'Pending') {
            $this->dispatch('toast', title: 'Request already processed', message: 'Only pending booking requests can be approved or rejected.', type: 'error');

            return;
        }

        $this->resetValidation();
        $this->decision = $decision;
        $this->rejectionReason = '';
        $this->viewingBookingId = $bookingId;
        $this->showBookingModal = false;
        $this->showDecisionModal = true;
    }

    public function closeDecisionModal(): void
    {
        $this->showDecisionModal = false;
        $this->viewingBookingId = null;
        $this->rejectionReason = '';
        $this->resetValidation();
        unset($this->selectedBooking);
    }

    public function closeActiveModal(): void
    {
        if ($this->showDecisionModal) {
            $this->closeDecisionModal();

            return;
        }

        if ($this->showReturnModal) {
            $this->closeReturnModal();

            return;
        }

        $this->closeBookingModal();
    }

    public function openReturnModal(int $bookingId): void
    {
        $this->authorizeStaff();

        $booking = Booking::query()->findOrFail($bookingId);

        if ($booking->status !== 'Approved') {
            $this->dispatch('toast', title: 'Booking not returnable', message: 'Only approved bookings can be marked as returned.', type: 'error');

            return;
        }

        $this->viewingBookingId = $booking->id;
        $this->showBookingModal = false;
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->viewingBookingId = null;
        unset($this->selectedBooking);
    }

    public function confirmDecision(): void
    {
        $this->authorizeStaff();

        $validated = $this->validate([
            'viewingBookingId' => ['required', 'integer', Rule::exists('bookings', 'id')],
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'rejectionReason' => $this->decision === 'reject'
                ? ['required', 'string', 'min:5', 'max:2000']
                : ['nullable', 'string', 'max:2000'],
        ], [
            'rejectionReason.required' => 'Provide a reason for rejecting this booking.',
            'rejectionReason.min' => 'The rejection reason must be at least 5 characters.',
        ]);

        $result = DB::transaction(function () use ($validated): array {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($validated['viewingBookingId']);

            if ($booking->status !== 'Pending') {
                return ['error' => 'This booking is no longer pending. Refresh the list before trying again.'];
            }

            if ($validated['decision'] === 'reject') {
                $booking->update([
                    'status' => 'Rejected',
                    'rejection_reason' => $validated['rejectionReason'],
                ]);

                return ['status' => 'Rejected'];
            }

            $equipment = Equipment::query()
                ->lockForUpdate()
                ->findOrFail($booking->equipment_id);

            if ($booking->booking_date->toDateString() < today()->toDateString()
                || ($booking->booking_date->isToday() && $booking->start_time <= now()->format('H:i:s'))) {
                return ['error' => 'This booking time has already passed and cannot be approved.'];
            }

            if ($equipment->condition !== 'Good') {
                return ['error' => 'This equipment is not currently in bookable condition.'];
            }

            $reservedQuantity = Booking::query()
                ->where('equipment_id', $equipment->id)
                ->whereDate('booking_date', $booking->booking_date)
                ->where('start_time', $booking->start_time)
                ->where('end_time', $booking->end_time)
                ->whereIn('status', ['Pending', 'Approved'])
                ->where('id', '<>', $booking->id)
                ->sum('quantity');

            $bookableQuantity = min($equipment->total_quantity, $equipment->available_quantity);
            $availableQuantity = max(0, $bookableQuantity - (int) $reservedQuantity);

            if ($booking->quantity > $availableQuantity) {
                return ['error' => "Only {$availableQuantity} unit(s) are available for this date and time slot."];
            }

            $booking->update([
                'status' => 'Approved',
                'rejection_reason' => null,
            ]);

            return ['status' => 'Approved'];
        });

        if (isset($result['error'])) {
            $this->addError('decision', $result['error']);

            return;
        }

        $wasApproved = $result['status'] === 'Approved';
        $this->showDecisionModal = false;
        $this->viewingBookingId = null;
        $this->rejectionReason = '';
        unset($this->bookingItems, $this->bookingCounts, $this->selectedBooking);

        $this->dispatch(
            'toast',
            title: $wasApproved ? 'Booking approved' : 'Booking rejected',
            message: $wasApproved
                ? 'The booking request has been approved.'
                : 'The booking request has been rejected and the reason saved.',
            type: 'success'
        );
    }

    public function completeBooking(int $bookingId): void
    {
        $this->authorizeStaff();

        $completed = DB::transaction(function () use ($bookingId): bool {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($bookingId);

            if ($booking->status !== 'Approved') {
                return false;
            }

            $booking->update(['status' => 'Completed']);

            return true;
        });

        if (! $completed) {
            $this->dispatch('toast', title: 'Booking not completed', message: 'Only an approved booking can be marked as returned and completed.', type: 'error');

            return;
        }

        unset($this->bookingItems, $this->bookingCounts, $this->selectedBooking);
        $this->closeReturnModal();
        $this->dispatch('toast', title: 'Booking completed', message: 'The booking has been marked as completed.', type: 'success');
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-' . $booking->booking_date->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    public function formattedTime(string $time): string
    {
        return Carbon::parse($time)->format('g:i A');
    }

    public function statusClass(string $status): string
    {
        return 'status-' . strtolower($status);
    }

    private function bookingQuery(): Builder
    {
        $this->authorizeStaff();

        return Booking::query()
            ->with(['user', 'equipment'])
            ->when($this->statusFilter !== 'All', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFilter !== '', fn (Builder $query) => $query->whereDate('booking_date', $this->dateFilter))
            ->when($this->scheduleFilter === 'upcoming', function (Builder $query): void {
                $query->whereDate('booking_date', '>=', today())
                    ->whereIn('status', ['Pending', 'Approved']);
            })
            ->when($this->scheduleFilter === 'today', fn (Builder $query) => $query->whereDate('booking_date', today()))
            ->when($this->scheduleFilter === 'past', fn (Builder $query) => $query->whereDate('booking_date', '<', today()))
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $term = trim($this->search);
                $likeTerm = '%' . $term . '%';
                preg_match('/(?:^|-)0*(\d+)$/i', $term, $referenceMatch);

                $query->where(function (Builder $query) use ($likeTerm, $referenceMatch): void {
                    $query->whereHas('user', function (Builder $userQuery) use ($likeTerm): void {
                        $userQuery->where('name', 'like', $likeTerm)
                            ->orWhere('university_id', 'like', $likeTerm);
                    })->orWhereHas('equipment', function (Builder $equipmentQuery) use ($likeTerm): void {
                        $equipmentQuery->where('name', 'like', $likeTerm)
                            ->orWhere('asset_code', 'like', $likeTerm)
                            ->orWhere('category', 'like', $likeTerm);
                    })->orWhere('purpose', 'like', $likeTerm)
                        ->orWhere('additional_notes', 'like', $likeTerm)
                        ->orWhere('status', 'like', $likeTerm);

                    if ($referenceMatch !== []) {
                        $query->orWhereKey((int) $referenceMatch[1]);
                    }
                });
            })
            ->when($this->sortBy === 'newest', fn (Builder $query) => $query->orderByDesc('created_at')->orderByDesc('id'))
            ->when($this->sortBy === 'oldest', fn (Builder $query) => $query->orderBy('created_at')->orderBy('id'))
            ->when($this->sortBy === 'date-asc', fn (Builder $query) => $query->orderBy('booking_date')->orderBy('start_time'))
            ->when($this->sortBy === 'date-desc', fn (Builder $query) => $query->orderByDesc('booking_date')->orderByDesc('start_time'))
            ->when($this->sortBy === 'student', fn (Builder $query) => $query->orderBy(User::query()->select('name')->whereColumn('users.id', 'bookings.user_id')))
            ->when($this->sortBy === 'status', fn (Builder $query) => $query->orderBy('status')->orderByDesc('created_at'));
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()?->role === 'staff', 403);
    }
};
?>

<div wire:keydown.escape.window="closeActiveModal">
    <section class="app-view is-entering" id="view-requests" aria-label="Booking management">
        <div id="requests-content">
            <div class="section-heading">
                <div>
                    <h2>Student booking requests</h2>
                    <p>Review requests, check live capacity, and process reservations.</p>
                </div>
                <div class="section-actions">
                    <button class="button button-outline" type="button" wire:click="showPendingBookings">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                        </svg>
                        Upcoming bookings ({{ $this->bookingCounts->pending ?? 0 }})
                    </button>
                </div>
            </div>

            <div class="filter-bar">
                <div class="search-control">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg></span>
                    <input type="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search reference, student, ID or equipment" aria-label="Search all bookings">
                </div>
                <div class="control-group">
                    <label for="staff-status">Status</label>
                    <select id="staff-status" wire:model.live="statusFilter">
                        <option value="All">All statuses</option>
                        @foreach (['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'] as $status)
                            <option wire:key="booking-status-{{ $loop->index }}" value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="control-group">
                    <label for="staff-scope">Schedule</label>
                    <select id="staff-scope" wire:model.live="scheduleFilter">
                        <option value="all">All bookings</option>
                        <option value="upcoming">Upcoming active</option>
                        <option value="today">Today</option>
                        <option value="past">Past dates</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="staff-date">Specific date</label>
                    <input id="staff-date" type="date" wire:model.live="dateFilter">
                    @error('dateFilter') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="control-group">
                    <label for="staff-sort">Sort records</label>
                    <select id="staff-sort" wire:model.live="sortBy">
                        <option value="newest">Newest submitted</option>
                        <option value="oldest">Oldest submitted</option>
                        <option value="date-asc">Booking date ↑</option>
                        <option value="date-desc">Booking date ↓</option>
                        <option value="student">Student name</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <button class="button button-outline" type="button" wire:click="resetFilters">Reset</button>
            </div>

            <div class="results-meta">
                <span>{{ $this->bookingItems->total() }} matching booking(s)</span>
                <span>{{ $this->bookingCounts->pending ?? 0 }} pending · {{ $this->bookingCounts->approved ?? 0 }} approved</span>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Booking Ref.</th>
                                <th>Student / ID</th>
                                <th>Equipment</th>
                                <th>Qty.</th>
                                <th>Booking Date</th>
                                <th>Time Slot</th>
                                <th>Purpose</th>
                                <th>Submission Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->bookingItems as $booking)
                                @php($reference = $this->bookingReference($booking))
                                <tr wire:key="booking-row-{{ $booking->id }}">
                                    <td data-label="Reference">
                                        <button class="reference-button" type="button" wire:click="viewBooking({{ $booking->id }})">
                                            {{ $reference }}
                                        </button>
                                    </td>
                                    <td data-label="Student">
                                        <span class="cell-primary">{{ $booking->user?->name ?? 'Unknown student' }}</span>
                                        <span class="cell-secondary">{{ $booking->user?->university_id ?? '—' }}</span>
                                    </td>
                                    <td data-label="Equipment">
                                        <span class="cell-primary">{{ $booking->equipment?->name ?? 'Equipment unavailable' }}</span>
                                        <span class="cell-secondary">{{ $booking->equipment?->category ?? '—' }}</span>
                                    </td>
                                    <td data-label="Qty.">{{ $booking->quantity }}</td>
                                    <td data-label="Booking date"><span class="nowrap">{{ $booking->booking_date->format('j M Y') }}</span></td>
                                    <td data-label="Time slot">{{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}</td>
                                    <td data-label="Purpose"><span class="purpose-cell" title="{{ $booking->purpose }}">{{ $booking->purpose ?: '—' }}</span></td>
                                    <td data-label="Submitted"><span class="nowrap">{{ $booking->created_at?->format('j M Y, H:i') ?? '—' }}</span></td>
                                    <td data-label="Status"><span class="status-badge {{ $this->statusClass($booking->status) }}">{{ $booking->status }}</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions">
                                            <button class="icon-button" type="button" wire:click="viewBooking({{ $booking->id }})" title="View details" aria-label="View booking details">
                                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                            @if ($booking->status === 'Pending')
                                                <button class="icon-button" type="button" wire:click="openDecisionModal({{ $booking->id }}, 'approve')" title="Approve request" aria-label="Approve request">
                                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg>
                                                </button>
                                                <button class="icon-button" type="button" wire:click="openDecisionModal({{ $booking->id }}, 'reject')" title="Reject request" aria-label="Reject request">
                                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                                                </button>
                                            @endif
                                            @if ($booking->status === 'Approved')
                                                <button class="icon-button" type="button" wire:click="openReturnModal({{ $booking->id }})" title="Mark equipment returned" aria-label="Mark equipment returned and booking completed">
                                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state compact">
                                            <h3>No bookings found</h3>
                                            <p>Try changing your search or filters to see booking requests.</p>
                                            <button class="button button-outline" type="button" wire:click="resetFilters">Clear filters</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($this->bookingItems->hasPages())
                <div class="pagination-wrap">{{ $this->bookingItems->links() }}</div>
            @endif
        </div>
    </section>

    @if ($showBookingModal && $this->selectedBooking)
        @php($booking = $this->selectedBooking)
        <div class="modal-backdrop" wire:click.self="closeBookingModal">
            <div class="modal modal-large" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title">
                <div class="modal-header">
                    <div class="modal-heading">
                        <span class="modal-heading-icon">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                            </svg>
                        </span>
                        <div>
                            <h2 id="booking-modal-title">Booking details</h2>
                            <p>Request information and current status</p>
                        </div>
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
                        <div class="detail-item">
                            <label>Student</label>
                            <p>{{ $booking->user?->name ?? 'Unknown student' }}<br><span class="muted">{{ $booking->user?->university_id ?? '—' }}</span></p>
                        </div>
                        <div class="detail-item">
                            <label>Date submitted</label>
                            <p>{{ $booking->created_at?->format('j M Y, H:i') ?? '—' }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Equipment</label>
                            <p>{{ $booking->equipment?->name ?? 'Equipment unavailable' }}<br><span class="muted">{{ $booking->equipment?->category ?? '—' }}</span></p>
                        </div>
                        <div class="detail-item">
                            <label>Quantity</label>
                            <p>{{ $booking->quantity }} unit(s)</p>
                        </div>
                        <div class="detail-item">
                            <label>Booking date</label>
                            <p>{{ $booking->booking_date->format('l, j M Y') }}</p>
                        </div>
                        <div class="detail-item">
                            <label>Time slot</label>
                            <p>{{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}</p>
                        </div>
                        <div class="detail-item full">
                            <label>Purpose of booking</label>
                            <p>{{ $booking->purpose ?: 'No purpose provided.' }}</p>
                        </div>
                        <div class="detail-item full">
                            <label>Additional notes</label>
                            <p>{{ $booking->additional_notes ?: 'No additional notes.' }}</p>
                        </div>
                    </div>
                    @if ($booking->rejection_reason)
                        <div class="rejection-box">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M9 9l6 6m0-6-6 6"></path></svg>
                            <div><strong>Rejection reason</strong><p>{{ $booking->rejection_reason }}</p></div>
                        </div>
                    @endif
                    <div class="booking-timeline">
                        <h3>Status history</h3>
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <div class="timeline-copy">
                                <strong>Pending</strong>
                                <p>Booking request submitted</p>
                                <small>{{ $booking->created_at?->format('j M Y, H:i') ?? '—' }} · {{ $booking->user?->name ?? 'Unknown student' }}</small>
                            </div>
                        </div>
                        @if ($booking->status !== 'Pending')
                            <div class="timeline-item">
                                <span class="timeline-dot"></span>
                                <div class="timeline-copy">
                                    <strong>{{ $booking->status }}</strong>
                                    <p>Booking status updated</p>
                                    <small>{{ $booking->updated_at?->format('j M Y, H:i') ?? '—' }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="button button-outline" type="button" wire:click="closeBookingModal">Close</button>
                    @if ($booking->status === 'Pending')
                        <button class="button button-danger-soft" type="button" wire:click="openDecisionModal({{ $booking->id }}, 'reject')">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg> Reject
                        </button>
                        <button class="button button-success" type="button" wire:click="openDecisionModal({{ $booking->id }}, 'approve')">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg> Approve
                        </button>
                    @elseif ($booking->status === 'Approved')
                        <button class="button button-success" type="button" wire:click="openReturnModal({{ $booking->id }})">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path></svg> Mark returned / completed
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($showDecisionModal && $this->selectedBooking)
        @php($booking = $this->selectedBooking)
        @php($isApproval = $decision === 'approve')
        <div class="modal-backdrop" wire:click.self="closeDecisionModal">
            <div class="modal modal-small" role="dialog" aria-modal="true" aria-labelledby="decision-modal-title">
                <div class="modal-header">
                    <div class="modal-heading">
                        <span class="modal-heading-icon {{ $isApproval ? 'success' : 'danger' }}">
                            @if ($isApproval)
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-6"></path></svg>
                            @else
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m9 9 6 6M15 9l-6 6"></path></svg>
                            @endif
                        </span>
                        <div>
                            <h2 id="decision-modal-title">{{ $isApproval ? 'Approve booking request?' : 'Reject booking request?' }}</h2>
                            <p>{{ $this->bookingReference($booking) }} · {{ $booking->user?->name ?? 'Unknown student' }}</p>
                        </div>
                    </div>
                    <button class="icon-button modal-close" type="button" wire:click="closeDecisionModal" aria-label="Close dialog">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="confirm-message">
                        @if ($isApproval)
                            Approve <strong>{{ $this->bookingReference($booking) }}</strong> for {{ $booking->user?->name ?? 'this student' }}?
                            This confirms {{ $booking->quantity }} × {{ $booking->equipment?->name ?? 'the requested equipment' }}
                            on {{ $booking->booking_date->format('j M Y') }}, {{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}.
                            Availability will be checked again before approval.
                        @else
                            Reject <strong>{{ $this->bookingReference($booking) }}</strong> for {{ $booking->user?->name ?? 'this student' }}?
                            The requested equipment will be released and the reason will be shared with the student.
                        @endif
                    </div>
                    @if (! $isApproval)
                        <div class="field-group">
                            <label for="rejection-reason">Rejection reason <span class="required">*</span></label>
                            <textarea id="rejection-reason" wire:model="rejectionReason" minlength="5" maxlength="2000"
                                required placeholder="Explain why this request cannot be approved."></textarea>
                            @error('rejectionReason') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    @error('decision') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button class="button button-outline" type="button" wire:click="closeDecisionModal">Go back</button>
                    <button class="button {{ $isApproval ? 'button-success' : 'button-danger' }}" type="button"
                        wire:click="confirmDecision" wire:loading.attr="disabled" wire:target="confirmDecision">
                        <span wire:loading.remove wire:target="confirmDecision" style="display: flex; align-items: center; gap: 0.5rem;">
                            @if ($isApproval)      
                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg> Approve request
                            @else
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg> Confirm rejection
                            @endif
                        </span>
                        <span wire:loading wire:target="confirmDecision">Processing…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showReturnModal && $this->selectedBooking)
        @php($booking = $this->selectedBooking)
        <div class="modal-backdrop" wire:click.self="closeReturnModal">
            <div class="modal modal-small" role="dialog" aria-modal="true" aria-labelledby="return-modal-title">
                <div class="modal-header">
                    <div class="modal-heading">
                        <span class="modal-heading-icon success">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path>
                            </svg>
                        </span>
                        <div>
                            <h2 id="return-modal-title">Confirm equipment return?</h2>
                            <p>{{ $this->bookingReference($booking) }} · {{ $booking->user?->name ?? 'Unknown student' }}</p>
                        </div>
                    </div>
                    <button class="icon-button modal-close" type="button" wire:click="closeReturnModal" aria-label="Close dialog">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="confirm-message">
                        Mark <strong>{{ $this->bookingReference($booking) }}</strong> as completed?
                        Confirm that {{ $booking->quantity }} × {{ $booking->equipment?->name ?? 'the requested equipment' }}
                        has been returned and checked. This releases its reservation.
                        @if ($booking->booking_date->toDateString() > today()->toDateString()
                            || ($booking->booking_date->isToday() && $booking->start_time > now()->format('H:i:s')))
                            <br><br><strong>This session has not started yet.</strong> Confirm only if the student has already handed the equipment back.
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="button button-outline" type="button" wire:click="closeReturnModal">Go back</button>
                    <button class="button button-success" type="button" wire:click="completeBooking({{ $booking->id }})"
                        wire:loading.attr="disabled" wire:target="completeBooking">
                        <span wire:loading.remove wire:target="completeBooking" style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg> Confirm return
                        </span>
                        <span wire:loading wire:target="completeBooking">Processing…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
