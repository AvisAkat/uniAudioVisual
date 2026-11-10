<?php

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $actionFilter = 'All';

    public string $dateFilter = '';

    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    #[Computed]
    public function activityLogs(): LengthAwarePaginator
    {
        $this->authorizeAdmin();

        return ActivityLog::query()
            ->with('user')
            ->when($this->actionFilter !== 'All', fn (Builder $query) => $query->where('action', $this->actionFilter))
            ->when($this->dateFilter !== '', fn (Builder $query) => $query->whereDate('created_at', $this->dateFilter))
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $searchTerm = '%'.trim($this->search).'%';

                $query->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('action', 'like', $searchTerm)
                        ->orWhere('module', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm): void {
                            $userQuery->where('name', 'like', $searchTerm)
                                ->orWhere('email', 'like', $searchTerm);
                        });
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);
    }

    #[Computed]
    public function availableActions(): Collection
    {
        $this->authorizeAdmin();

        return ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
    }

    public function updatedSearch(): void
    {
        $this->authorizeAdmin();
        $this->resetPage();
    }

    public function updatedActionFilter(): void
    {
        $this->authorizeAdmin();

        if ($this->actionFilter !== 'All' && ! $this->availableActions->contains($this->actionFilter)) {
            throw ValidationException::withMessages([
                'actionFilter' => 'Select a valid activity type.',
            ]);
        }

        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->authorizeAdmin();
        $this->validateOnly('dateFilter', [
            'dateFilter' => ['nullable', 'date'],
        ]);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->authorizeAdmin();
        $this->reset('search', 'actionFilter', 'dateFilter');
        $this->actionFilter = 'All';
        $this->resetPage();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-activity" aria-label="Activity log">
        <div id="activity-content">
            <div class="section-heading">
                <div>
                    <h2>System activity log</h2>
                    <p>A chronological audit trail of registrations, bookings, decisions, returns and stock changes.</p>
                </div>
            </div>

            <div class="filter-bar">
                <div class="search-control">
                    <span>
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>
                    </span>
                    <input
                        id="activity-search"
                        type="search"
                        placeholder="Search event, description or user"
                        aria-label="Search activity log"
                        wire:model.live.debounce.300ms="search"
                    >
                </div>

                <div class="control-group">
                    <label for="activity-type">Activity type</label>
                    <select id="activity-type" wire:model.live="actionFilter">
                        <option value="All">All activity types</option>
                        @foreach ($this->availableActions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="control-group">
                    <label for="activity-date">Event date</label>
                    <input id="activity-date" type="date" wire:model.live="dateFilter">
                </div>

                <button class="button button-outline" type="button" wire:click="resetFilters">
                    Clear filters
                </button>
            </div>

            <div class="results-meta">
                <span>{{ $this->activityLogs->count() }} of {{ $this->activityLogs->total() }} audit event(s)</span>
                <span>Most recent activity first</span>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Activity Type</th>
                                <th>Description</th>
                                <th>Date &amp; Time</th>
                                <th>Relevant User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->activityLogs as $activityLog)
                                <tr wire:key="activity-log-{{ $activityLog->id }}">
                                    <td data-label="Activity type">
                                        <div class="log-type">
                                            <span>
                                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="m5 12 4 4L19 6"></path>
                                                </svg>
                                            </span>
                                            <strong>{{ $activityLog->action }}</strong>
                                        </div>
                                    </td>
                                    <td data-label="Description">
                                        <p style="margin:0;max-width:520px">
                                            {{ $activityLog->description ?: 'No description provided.' }}
                                        </p>
                                        <span class="cell-secondary">{{ $activityLog->module }}</span>
                                    </td>
                                    <td data-label="Date &amp; time">
                                        <span class="nowrap">{{ $activityLog->created_at->format('j M Y, H:i') }}</span>
                                        <span class="cell-secondary">{{ $activityLog->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td data-label="Relevant user">
                                        <span class="cell-primary">{{ $activityLog->user?->name ?? 'System / deleted user' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No activity matches the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($this->activityLogs->hasPages())
                <div class="pagination-wrap">{{ $this->activityLogs->links() }}</div>
            @endif
        </div>
    </section>
</div>
