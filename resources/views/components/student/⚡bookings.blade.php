<?php

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {
    public string $search = '';

    public string $statusFilter = 'All';

    public string $sortBy = 'newest';

    public ?int $viewingBookingId = null;

    public ?int $cancellingBookingId = null;

    #[\Livewire\Attributes\Computed]
    public function allBookings(): Collection
    {
        return Booking::query()
            ->where('user_id', auth()->id())
            ->with('equipment')
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function bookingItems(): Collection
    {
        $searchTerm = Str::lower(trim($this->search));

        $bookings = $this->allBookings
            ->when($this->statusFilter !== 'All', fn(Collection $bookings) => $bookings->where('status', $this->statusFilter))
            ->when($searchTerm !== '', function (Collection $bookings) use ($searchTerm): Collection {
                return $bookings->filter(function (Booking $booking) use ($searchTerm): bool {
                    return Str::contains(Str::lower($this->bookingReference($booking)), $searchTerm)
                        || Str::contains(Str::lower($booking->equipment?->name ?? ''), $searchTerm)
                        || Str::contains(Str::lower($booking->purpose ?? ''), $searchTerm);
                });
            });

        return match ($this->sortBy) {
            'oldest' => $bookings->sortBy('created_at')->values(),
            'date-asc' => $bookings->sortBy(fn(Booking $booking) => $booking->booking_date->timestamp)->values(),
            'date-desc' => $bookings->sortByDesc(fn(Booking $booking) => $booking->booking_date->timestamp)->values(),
            'status' => $bookings->sortBy('status')->values(),
            default => $bookings->sortByDesc('created_at')->values(),
        };
    }

    #[\Livewire\Attributes\Computed]
    public function viewedBooking(): ?Booking
    {
        return $this->viewingBookingId === null
            ? null
            : $this->studentBookings()->with('equipment')->find($this->viewingBookingId);
    }

    #[\Livewire\Attributes\Computed]
    public function bookingToCancel(): ?Booking
    {
        return $this->cancellingBookingId === null
            ? null
            : $this->studentBookings()->with('equipment')->find($this->cancellingBookingId);
    }

    public function viewBooking(int $bookingId): void
    {
        $this->authorizeStudent();

        $booking = $this->studentBookings()->findOrFail($bookingId);
        $this->viewingBookingId = $booking->id;
        unset($this->viewedBooking);
    }

    public function closeBookingView(): void
    {
        $this->viewingBookingId = null;
        unset($this->viewedBooking);
    }

    public function openCancelBooking(int $bookingId): void
    {
        $this->authorizeStudent();

        $booking = $this->studentBookings()->findOrFail($bookingId);

        if (!$this->isCancellable($booking)) {
            $this->dispatch(
                'toast',
                title: 'Booking cannot be cancelled',
                message: 'Only pending or approved bookings can be cancelled before their start time.',
                type: 'error'
            );

            return;
        }

        $this->closeBookingView();
        $this->cancellingBookingId = $booking->id;
        unset($this->bookingToCancel);
    }

    public function closeCancelBooking(): void
    {
        $this->cancellingBookingId = null;
        unset($this->bookingToCancel);
    }

    public function cancelBooking(): void
    {
        $this->authorizeStudent();

        if ($this->cancellingBookingId === null) {
            return;
        }

        $cancelled = DB::transaction(function (): bool {
            $booking = $this->studentBookings()
                ->lockForUpdate()
                ->findOrFail($this->cancellingBookingId);

            if (!$this->isCancellable($booking)) {
                return false;
            }

            $booking->status = 'Cancelled';

            return $booking->save();
        });

        if (!$cancelled) {
            $this->dispatch(
                'toast',
                title: 'Booking cannot be cancelled',
                message: 'The booking has started or is no longer pending or approved.',
                type: 'error'
            );

            $this->closeCancelBooking();
            unset($this->allBookings, $this->bookingItems);

            return;
        }

        $this->closeCancelBooking();
        unset($this->allBookings, $this->bookingItems);

        $this->dispatch(
            'toast',
            title: 'Booking cancelled',
            message: 'Your booking has been cancelled.',
            type: 'success'
        );
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'statusFilter', 'sortBy');
        $this->statusFilter = 'All';
        $this->sortBy = 'newest';
        unset($this->allBookings, $this->bookingItems);
    }

    private function studentBookings(): \Illuminate\Database\Eloquent\Builder
    {
        return Booking::query()->where('user_id', auth()->id());
    }

    private function authorizeStudent(): void
    {
        abort_unless(auth()->user()?->role === 'student', 403);
    }

    public function isCancellable(Booking $booking): bool
    {
        if (!in_array($booking->status, ['Pending', 'Approved'], true)) {
            return false;
        }

        $startsAt = Carbon::parse(
            Carbon::parse($booking->booking_date)->toDateString() . ' ' . $booking->start_time
        );

        return $startsAt->isFuture();
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-' . Carbon::parse($booking->booking_date)->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    public function formattedTimeSlot(Booking $booking): string
    {
        return Carbon::parse($booking->start_time)->format('g:i A')
            . ' – '
            . Carbon::parse($booking->end_time)->format('g:i A');
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-my-bookings" aria-label="My bookings">
        <div id="my-bookings-content">
            <div class="section-heading">
                <div>
                    <h2>My booking history</h2>
                    <p>Search your reference, monitor decisions and release reservations you no longer need.</p>
                </div>
                <div class="section-actions">
                    <a href="{{ route('student.browse_equipment') }}">
                        <button class="button button-primary" type="button" data-view="equipment">
                            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg>
                            New booking
                        </button>
                    </a>
                </div>
            </div>
            <div class="filter-bar">
                <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg></span><input id="student-booking-search" type="search" value=""
                        placeholder="Search reference, equipment or purpose" aria-label="Search my bookings"
                        wire:model.live.debounce.300ms="search"></div>
                <div class="control-group"><label for="student-booking-status">Status</label><select
                        id="student-booking-status" wire:model.live="statusFilter">
                        <option value="All">All statuses</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                        <option>Cancelled</option>
                        <option>Completed</option>
                        <option>Expired</option>
                    </select></div>
                <div class="control-group"><label for="student-booking-sort">Sort records</label><select
                        id="student-booking-sort" wire:model.live="sortBy">
                        <option value="newest" selected="">Newest submitted</option>
                        <option value="oldest">Oldest submitted</option>
                        <option value="date-asc">Booking date ↑</option>
                        <option value="date-desc">Booking date ↓</option>
                        <option value="status">Status</option>
                    </select></div><button class="button button-outline" type="button"
                    wire:click="resetFilters">Reset</button>
            </div>
            <div class="results-meta"><span>{{ $this->bookingItems->count() }} booking(s) found ·
                    {{ $this->allBookings->count() }} total</span><span>Approved and pending reservations
                    can be cancelled before their start time</span></div>
            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Booking Ref.</th>
                                <th>Equipment</th>
                                <th>Qty.</th>
                                <th>Booking Date</th>
                                <th>Time Slot</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->bookingItems as $booking)
                                @php
                                    $reference = $this->bookingReference($booking);
                                    $canCancel = $this->isCancellable($booking);
                                @endphp
                                <tr wire:key="student-booking-{{ $booking->id }}">
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            wire:click="viewBooking({{ $booking->id }})">{{ $reference }}</button></td>
                                    <td data-label="Equipment"><span
                                            class="cell-primary">{{ $booking->equipment?->name ?? 'Equipment removed' }}</span>
                                        @if ($booking->equipment)
                                            <span class="cell-secondary">{{ $booking->equipment->category }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Quantity">{{ $booking->quantity }}</td>
                                    <td data-label="Booking date"><span
                                            class="nowrap">{{ $booking->booking_date->format('d M Y') }}</span></td>
                                    <td data-label="Time slot">{{ $this->formattedTimeSlot($booking) }}</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="{{ $booking->purpose }}">{{ $booking->purpose }}</span></td>
                                    <td data-label="Status"><span
                                            class="status-badge status-{{ Str::slug($booking->status) }}">{{ $booking->status }}</span>
                                    </td>
                                    <td data-label="Submitted"><span
                                            class="nowrap">{{ $booking->created_at->format('d M Y, H:i') }}</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions">
                                            <button class="icon-button" type="button"
                                                wire:click="viewBooking({{ $booking->id }})" title="View details"
                                                aria-label="View {{ $reference }}"><svg class="icon" viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button>
                                            @if ($canCancel)
                                                <button class="icon-button action-reject" type="button"
                                                    wire:click="openCancelBooking({{ $booking->id }})" title="Cancel booking"
                                                    aria-label="Cancel {{ $reference }}"><svg class="icon" viewBox="0 0 24 24"
                                                        aria-hidden="true">
                                                        <circle cx="12" cy="12" r="9"></circle>
                                                        <path d="m5.5 5.5 13 13"></path>
                                                    </svg></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty-state">No bookings match your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- View Booking Modal --}}
    @if ($this->viewedBooking)
        <div class="modal-backdrop" id="booking-details-backdrop">
            <div class="modal modal-large" id="booking-details-modal" role="dialog" aria-modal="true"
                aria-labelledby="booking-details-title">
                <div id="booking-details-content">
                    <div class="modal-header">
                        <div class="modal-heading"><span class="modal-heading-icon "><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                    <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                </svg></span>
                            <div>
                                <h2 id="booking-details-title">Booking details</h2>
                                <p>Complete reservation information and status history</p>
                            </div>
                        </div><button class="icon-button modal-close" type="button" wire:click="closeBookingView"
                            aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg></button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-reference">
                            <code>{{ $this->bookingReference($this->viewedBooking) }}</code>
                            <span
                                class="status-badge status-{{ Str::slug($this->viewedBooking->status) }}">{{ $this->viewedBooking->status }}</span>
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item"><label>Student</label>
                                <p>{{ auth()->user()->name }}<br><span
                                        class="muted">{{ auth()->user()->university_id }}</span></p>
                            </div>
                            <div class="detail-item"><label>Date submitted</label>
                                <p>{{ $this->viewedBooking->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="detail-item"><label>Equipment</label>
                                <p>{{ $this->viewedBooking->equipment?->name ?? 'Equipment removed' }}
                                    @if ($this->viewedBooking->equipment)
                                        <br><span class="muted">{{ $this->viewedBooking->equipment->category }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="detail-item"><label>Quantity</label>
                                <p>{{ $this->viewedBooking->quantity }} unit(s)</p>
                            </div>
                            <div class="detail-item"><label>Booking date</label>
                                <p>{{ $this->viewedBooking->booking_date->format('l, d M Y') }}</p>
                            </div>
                            <div class="detail-item"><label>Time slot</label>
                                <p>{{ $this->formattedTimeSlot($this->viewedBooking) }}</p>
                            </div>
                            <div class="detail-item full"><label>Purpose of booking</label>
                                <p>{{ $this->viewedBooking->purpose }}</p>
                            </div>
                            <div class="detail-item full"><label>Additional notes</label>
                                <p>{{ $this->viewedBooking->additional_notes ?: 'No additional notes.' }}</p>
                            </div>
                            @if ($this->viewedBooking->rejection_reason)
                                <div class="detail-item full"><label>Reason for rejection</label>
                                    <p>{{ $this->viewedBooking->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="booking-timeline">
                            <h3>Booking status</h3>
                            <div class="timeline-item"><span class="timeline-dot"></span>
                                <div class="timeline-copy"><strong>Request submitted</strong>
                                    <p>Booking request submitted</p>
                                    <small>{{ $this->viewedBooking->created_at->format('d M Y, H:i') }} ·
                                        {{ auth()->user()->name }}</small>
                                </div>
                            </div>
                            @if ($this->viewedBooking->status !== 'Pending')
                                <div class="timeline-item"><span class="timeline-dot"></span>
                                    <div class="timeline-copy"><strong>{{ $this->viewedBooking->status }}</strong>
                                        <p>Current booking status</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer"><button class="button button-outline" type="button"
                            wire:click="closeBookingView">Close</button>
                        @if ($this->isCancellable($this->viewedBooking))
                            <button class="button button-danger-soft" type="button"
                                wire:click="openCancelBooking({{ $this->viewedBooking->id }})"><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m5.5 5.5 13 13"></path>
                                </svg> Cancel booking</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- Cancel Booking Modal --}}
    @if ($this->bookingToCancel)
        <div class="modal-backdrop" id="cancel-booking-backdrop">
            <div class="modal modal-small" id="cancel-booking-modal" role="dialog" aria-modal="true"
                aria-labelledby="cancel-booking-title">
                <div id="cancel-booking-content">
                    <div class="modal-header">
                        <div class="modal-heading"><span class="modal-heading-icon danger"><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m5.5 5.5 13 13"></path>
                                </svg></span>
                            <div>
                                <h2 id="cancel-booking-title">Cancel this booking?</h2>
                                <p>Please review this action before continuing.</p>
                            </div>
                        </div><button class="icon-button modal-close" type="button" wire:click="closeCancelBooking"
                            aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg></button>
                    </div>
                    <div class="modal-body">
                        <div class="confirm-message"><strong>{{ $this->bookingReference($this->bookingToCancel) }}</strong>
                            reserves {{ $this->bookingToCancel->quantity }} ×
                            {{ $this->bookingToCancel->equipment?->name ?? 'Equipment removed' }}
                            for {{ $this->bookingToCancel->booking_date->format('d M Y') }},
                            {{ $this->formattedTimeSlot($this->bookingToCancel) }}.
                            Cancelling immediately releases this equipment for other
                            students.</div>
                    </div>
                    <div class="modal-footer"><button class="button button-outline" type="button"
                            wire:click="closeCancelBooking">Go
                            back</button><button class="button button-danger" type="button" wire:click="cancelBooking"
                            wire:loading.attr="disabled"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg> Cancel booking</button></div>
                </div>
            </div>
        </div>
    @endif
</div>