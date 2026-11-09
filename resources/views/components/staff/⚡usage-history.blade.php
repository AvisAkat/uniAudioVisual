<?php

use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $returnDate = '';

    public string $categoryFilter = 'All';

    public string $search = '';

    public ?int $viewingBookingId = null;

    public bool $showBookingModal = false;

    public function mount(): void
    {
        $this->authorizeStaffOrAdmin();
    }

    #[Computed]
    public function equipmentCategories()
    {
        $this->authorizeStaffOrAdmin();

        return Equipment::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    #[Computed]
    public function usageRecords(): LengthAwarePaginator
    {
        return $this->usageQuery()
            ->with(['user', 'equipment', 'returnedBy'])
            ->orderByDesc('returned_at')
            ->orderByDesc('updated_at')
            ->paginate(15);
    }

    #[Computed]
    public function usageSummary(): array
    {
        $query = $this->usageQuery();

        return [
            'total' => (clone $query)->count(),
            'thisMonth' => (clone $query)
                ->whereBetween('returned_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'missingReturnTime' => (clone $query)
                ->whereNull('returned_at')
                ->count(),
        ];
    }

    #[Computed]
    public function selectedBooking(): ?Booking
    {
        $this->authorizeStaffOrAdmin();

        if ($this->viewingBookingId === null) {
            return null;
        }

        return $this->usageQuery()
            ->with(['user', 'equipment', 'returnedBy'])
            ->find($this->viewingBookingId);
    }

    public function updatedReturnDate(): void
    {
        $this->authorizeStaffOrAdmin();
        $this->validateOnly('returnDate', ['returnDate' => ['nullable', 'date']]);
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->authorizeStaffOrAdmin();

        if ($this->categoryFilter !== 'All') {
            validator(
                ['categoryFilter' => $this->categoryFilter],
                ['categoryFilter' => ['required', 'string', Rule::exists('equipment', 'category')]]
            )->validate();
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->authorizeStaffOrAdmin();
        $this->validateOnly('search', ['search' => ['nullable', 'string', 'max:255']]);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->authorizeStaffOrAdmin();
        $this->reset('returnDate', 'categoryFilter', 'search');
        $this->resetPage();
        $this->resetValidation();
    }

    public function viewBooking(int $bookingId): void
    {
        $this->authorizeStaffOrAdmin();

        $booking = $this->usageQuery()->findOrFail($bookingId);

        $this->viewingBookingId = $booking->id;
        $this->showBookingModal = true;
    }

    public function closeBookingModal(): void
    {
        $this->authorizeStaffOrAdmin();
        $this->showBookingModal = false;
        $this->viewingBookingId = null;
        unset($this->selectedBooking);
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-' . $booking->booking_date->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    public function returnStatus(Booking $booking): string
    {
        if ($booking->returned_at === null) {
            return 'Return time not recorded';
        }

        $expectedReturn = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->end_time);

        return $booking->returned_at->gt($expectedReturn)
            ? 'Returned late'
            : 'Returned on time';
    }

    public function returnStatusClass(Booking $booking): string
    {
        return match ($this->returnStatus($booking)) {
            'Returned late' => 'usage-late',
            'Returned on time' => 'usage-returned',
            default => 'usage-overdue',
        };
    }

    private function usageQuery(): Builder
    {
        $user = $this->authorizeStaffOrAdmin();

        return Booking::query()
            ->where('status', 'Completed')
            ->when($user->role === 'staff', fn (Builder $query) => $query->where('returned_by', $user->id))
            ->when($this->returnDate !== '', fn (Builder $query) => $query->whereDate('returned_at', $this->returnDate))
            ->when($this->categoryFilter !== 'All', fn (Builder $query) => $query->whereHas(
                'equipment',
                fn (Builder $equipmentQuery) => $equipmentQuery->where('category', $this->categoryFilter)
            ))
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $term = trim($this->search);
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
                        ->orWhere('purpose', 'like', $likeTerm);

                    if ($referenceMatch !== []) {
                        $query->orWhereKey((int) $referenceMatch[1]);
                    }
                });
            });
    }

    private function authorizeStaffOrAdmin(): \App\Models\User
    {
        $user = auth()->user();
        abort_unless(in_array($user?->role, ['staff', 'admin'], true), 403);

        return $user;
    }
};
?>

<div wire:keydown.escape.window="closeBookingModal">
    <section class="app-view is-entering" id="view-usage" aria-label="Usage history">
        <div id="usage-content">
            <div class="section-heading">
                <div>
                    <h2>Equipment usage history</h2>
                    <p>
                        @if (auth()->user()->role === 'admin')
                            Review completed equipment returns processed by all staff.
                        @else
                            Review equipment returns you have processed.
                        @endif
                    </p>
                </div>
                <div class="section-actions">
                    <a class="button button-outline" href="{{ route('staff.booking_request') }}">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path>
                        </svg>
                        Process returns
                    </a>
                </div>
            </div>

            <div class="availability-summary">
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5M12 7v5l3 2"></path></svg></span>
                    <div><strong>{{ $this->usageSummary['total'] }}</strong><small>Total tracked return records</small></div>
                </div>
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-6"></path></svg></span>
                    <div><strong>{{ $this->usageSummary['thisMonth'] }}</strong><small>Returns recorded this month</small></div>
                </div>
                <div class="mini-stat">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path><path d="M12 9v4M12 17h.01"></path></svg></span>
                    <div><strong>{{ $this->usageSummary['missingReturnTime'] }}</strong><small>Legacy returns missing return time</small></div>
                </div>
            </div>

            <div class="filter-bar">
                <div class="search-control">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg></span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search reference, student or equipment" aria-label="Search usage history">
                    @error('search') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="control-group">
                    <label for="usage-date">Actual return date</label>
                    <input id="usage-date" type="date" wire:model.live="returnDate">
                    @error('returnDate') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="control-group">
                    <label for="usage-category">Equipment category</label>
                    <select id="usage-category" wire:model.live="categoryFilter">
                        <option value="All">All categories</option>
                        @foreach ($this->equipmentCategories as $category)
                            <option wire:key="usage-category-{{ $loop->index }}" value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('categoryFilter') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button class="button button-outline" type="button" wire:click="resetFilters">Clear filters</button>
            </div>

            <div class="results-meta">
                <span>{{ $this->usageRecords->total() }} matching return record(s)</span>
                <span>Returned equipment is recorded with its return time and processing staff member.</span>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Equipment Borrowed</th>
                                <th>Student</th>
                                <th>Booking Ref.</th>
                                <th>Collection Date</th>
                                <th>Expected Return</th>
                                <th>Actual Return Status</th>
                                <th>Staff Who Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->usageRecords as $booking)
                                <tr wire:key="usage-record-{{ $booking->id }}">
                                    <td data-label="Equipment borrowed">
                                        <span class="cell-primary">{{ $booking->equipment?->name ?? 'Equipment unavailable' }}</span>
                                        <span class="cell-secondary">{{ $booking->equipment?->category ?? '—' }}</span>
                                    </td>
                                    <td data-label="Student">
                                        <span class="cell-primary">{{ $booking->user?->name ?? 'Unknown student' }}</span>
                                        <span class="cell-secondary">{{ $booking->user?->university_id ?? '—' }}</span>
                                    </td>
                                    <td data-label="Booking reference">
                                        <button class="reference-button" type="button" wire:click="viewBooking({{ $booking->id }})">
                                            {{ $this->bookingReference($booking) }}
                                        </button>
                                    </td>
                                    <td data-label="Collection date">{{ $booking->booking_date->format('j M Y') }}</td>
                                    <td data-label="Expected return">
                                        {{ $booking->booking_date->format('j M Y') }}, {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('g:i A') }}
                                    </td>
                                    <td data-label="Actual return status">
                                        <span class="usage-status {{ $this->returnStatusClass($booking) }}">{{ $this->returnStatus($booking) }}</span>
                                        @if ($booking->returned_at)
                                            <span class="cell-secondary">{{ $booking->returned_at->format('j M Y, g:i A') }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Processed by">{{ $booking->returnedBy?->name ?? 'Not recorded' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state compact">
                                            <h3>No usage records found</h3>
                                            <p>Completed returns that match your access and filters will appear here.</p>
                                            @if ($search !== '' || $returnDate !== '' || $categoryFilter !== 'All')
                                                <button class="button button-outline" type="button" wire:click="resetFilters">Clear filters</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($this->usageRecords->hasPages())
                <div class="pagination-wrap">{{ $this->usageRecords->links() }}</div>
            @endif
        </div>
    </section>

    @if ($showBookingModal && $this->selectedBooking)
        @php($booking = $this->selectedBooking)
        <div class="modal-backdrop" wire:click.self="closeBookingModal">
            <div class="modal modal-large" role="dialog" aria-modal="true" aria-labelledby="usage-booking-title">
                <div class="modal-header">
                    <div class="modal-heading">
                        <span class="modal-heading-icon success">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 12 3 3 5-6"></path><circle cx="12" cy="12" r="9"></circle></svg>
                        </span>
                        <div><h2 id="usage-booking-title">Usage record</h2><p>{{ $this->bookingReference($booking) }}</p></div>
                    </div>
                    <button class="icon-button modal-close" type="button" wire:click="closeBookingModal" aria-label="Close dialog">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="detail-reference">
                        <code>{{ $this->bookingReference($booking) }}</code>
                        <span class="status-badge status-completed">Completed</span>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Student</label><p>{{ $booking->user?->name ?? 'Unknown student' }}<br><span class="muted">{{ $booking->user?->university_id ?? '—' }}</span></p></div>
                        <div class="detail-item"><label>Equipment</label><p>{{ $booking->equipment?->name ?? 'Equipment unavailable' }}<br><span class="muted">{{ $booking->equipment?->category ?? '—' }}</span></p></div>
                        <div class="detail-item"><label>Quantity</label><p>{{ $booking->quantity }} unit(s)</p></div>
                        <div class="detail-item"><label>Collection date</label><p>{{ $booking->booking_date->format('j M Y') }}</p></div>
                        <div class="detail-item"><label>Expected return</label><p>{{ $booking->booking_date->format('j M Y') }}, {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('g:i A') }}</p></div>
                        <div class="detail-item"><label>Actual return</label><p>{{ $booking->returned_at?->format('j M Y, g:i A') ?? 'Not recorded' }}</p></div>
                        <div class="detail-item"><label>Processed by</label><p>{{ $booking->returnedBy?->name ?? 'Not recorded' }}</p></div>
                        <div class="detail-item"><label>Return status</label><p>{{ $this->returnStatus($booking) }}</p></div>
                        <div class="detail-item full"><label>Purpose</label><p>{{ $booking->purpose ?: 'No purpose provided.' }}</p></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="button button-outline" type="button" wire:click="closeBookingModal">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
