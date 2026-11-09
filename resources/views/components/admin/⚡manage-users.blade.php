<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public string $roleFilter = 'all';

    public string $statusFilter = 'all';

    public string $sortBy = 'newest';

    public string $activeModal = '';

    #[Locked]
    public ?int $editingUserId = null;

    #[Locked]
    public ?int $viewingUserId = null;

    #[Locked]
    public ?int $statusUserId = null;

    #[Locked]
    public ?int $deletingUserId = null;

    public string $name = '';

    public string $universityId = '';

    public string $email = '';

    public string $role = 'student';

    public string $status = 'active';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    #[Computed]
    public function userStats(): array
    {
        $this->authorizeAdmin();

        $stats = User::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as students', ['student'])
            ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as staff', ['staff'])
            ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admins', ['admin'])
            ->selectRaw('SUM(CASE WHEN LOWER(status) = ? THEN 1 ELSE 0 END) as active', ['active'])
            ->selectRaw('SUM(CASE WHEN LOWER(status) = ? THEN 1 ELSE 0 END) as suspended', ['suspended'])
            ->first();

        return [
            'total' => (int) $stats->total,
            'students' => (int) $stats->students,
            'staff' => (int) $stats->staff,
            'admins' => (int) $stats->admins,
            'active' => (int) $stats->active,
            'suspended' => (int) $stats->suspended,
        ];
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $this->authorizeAdmin();

        $query = User::query()
            ->addSelect([
                'last_sign_in_at' => ActivityLog::query()
                    ->select('created_at')
                    ->whereColumn('activity_logs.user_id', 'users.id')
                    ->where('action', 'User signed in')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit(1),
            ])
            ->withCasts(['last_sign_in_at' => 'datetime'])
            ->withCount([
                'bookings',
                'bookings as pending_bookings_count' => fn(Builder $bookings): Builder => $bookings->where('status', 'Pending'),
                'bookings as active_bookings_count' => fn(Builder $bookings): Builder => $bookings->whereIn('status', ['Pending', 'Approved']),
            ]);

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function (Builder $users) use ($search): void {
                $users->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('university_id', 'like', $search);
            });
        }

        if (in_array($this->roleFilter, ['student', 'staff', 'admin'], true)) {
            $query->where('role', $this->roleFilter);
        }

        if (in_array($this->statusFilter, ['active', 'suspended'], true)) {
            $query->whereRaw('LOWER(status) = ?', [$this->statusFilter]);
        }

        match ($this->sortBy) {
            'name' => $query->orderBy('name')->orderBy('id'),
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'bookings' => $query->orderByDesc('bookings_count')->orderBy('name'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };

        return $query->paginate(10);
    }

    #[Computed]
    public function viewingUser(): ?User
    {
        $this->authorizeAdmin();

        if ($this->viewingUserId === null) {
            return null;
        }

        return User::query()
            ->addSelect([
                'last_sign_in_at' => ActivityLog::query()
                    ->select('created_at')
                    ->whereColumn('activity_logs.user_id', 'users.id')
                    ->where('action', 'User signed in')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit(1),
            ])
            ->withCasts(['last_sign_in_at' => 'datetime'])
            ->with([
                'bookings' => fn(Builder $bookings): Builder => $bookings
                    ->with('equipment')
                    ->orderByDesc('booking_date')
                    ->orderByDesc('start_time')
                    ->limit(5),
            ])
            ->withCount([
                'bookings',
                'bookings as pending_bookings_count' => fn(Builder $bookings): Builder => $bookings->where('status', 'Pending'),
                'bookings as approved_bookings_count' => fn(Builder $bookings): Builder => $bookings->where('status', 'Approved'),
                'bookings as completed_bookings_count' => fn(Builder $bookings): Builder => $bookings->where('status', 'Completed'),
            ])
            ->find($this->viewingUserId);
    }

    #[Computed]
    public function statusTarget(): ?User
    {
        $this->authorizeAdmin();

        return $this->statusUserId === null ? null : User::query()->find($this->statusUserId);
    }

    #[Computed]
    public function deleteTarget(): ?User
    {
        $this->authorizeAdmin();

        return $this->deletingUserId === null ? null : User::query()->find($this->deletingUserId);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->authorizeAdmin();
        $this->search = '';
        $this->roleFilter = 'all';
        $this->statusFilter = 'all';
        $this->sortBy = 'newest';
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->activeModal = 'form';
    }

    public function openEditModal(int $userId): void
    {
        $this->authorizeAdmin();
        $user = User::query()->findOrFail($userId);
        $this->resetForm();
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->universityId = $user->university_id;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = strtolower($user->status);
        $this->activeModal = 'form';
    }

    public function saveUser(): void
    {
        $this->authorizeAdmin();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'universityId' => [
                'required',
                'digits:8',
                Rule::unique('users', 'university_id')->ignore($this->editingUserId),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'role' => ['required', Rule::in(['student', 'staff', 'admin'])],
            'status' => ['required', Rule::in(['active', 'suspended'])],
            'password' => $this->editingUserId === null
                ? ['required', 'string', 'min:8', 'confirmed']
                : ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required_with:password', 'string'],
        ], [], [
            'universityId' => 'university ID',
        ]);

        $user = $this->editingUserId === null
            ? new User()
            : User::query()->findOrFail($this->editingUserId);
        $accountStatus = $this->editingUserId === null
            ? $validated['status']
            : strtolower($user->status);

        if ($this->editingUserId !== null) {
            if (
                $user->role === 'student'
                && $validated['role'] !== 'student'
                && $this->hasPendingOrApprovedBookings($user)
            ) {
                throw ValidationException::withMessages([
                    'account' => 'A student with pending or approved bookings cannot be changed to another role.',
                ]);
            }

            if (
                $user->is(auth()->user())
                && $validated['role'] !== $user->role
            ) {
                throw ValidationException::withMessages([
                    'account' => 'You cannot change your own role.',
                ]);
            }

            $this->ensureAdminAccountRemainsAvailable($user, $validated['role'], $accountStatus);
        }

        $isCreating = $this->editingUserId === null;

        DB::transaction(function () use ($user, $validated, $accountStatus, $isCreating): void {
            $user->name = $validated['name'];
            $user->university_id = $validated['universityId'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];
            $user->status = $accountStatus;

            if ($validated['password'] !== null && $validated['password'] !== '') {
                $user->password = $validated['password'];
            }

            $user->save();

            ActivityLog::record(
                $isCreating ? 'User created' : 'User updated',
                'User Management',
                ($isCreating ? 'Created' : 'Updated') . ' the account for ' . $user->name . '.',
                $user,
            );
        });

        $this->closeModal();
        unset($this->userStats, $this->users);
        $this->dispatch('toast', title: $isCreating ? 'Account created' : 'Account updated', message: $isCreating ? 'The user account was created successfully.' : 'The user account was updated successfully.', type: 'success');
    }

    public function openViewModal(int $userId): void
    {
        $this->authorizeAdmin();
        User::query()->findOrFail($userId);
        $this->viewingUserId = $userId;
        unset($this->viewingUser);
        $this->activeModal = 'view';
    }

    public function openStatusModal(int $userId): void
    {
        $this->authorizeAdmin();
        $user = User::query()->findOrFail($userId);
        $this->ensureNotSelf($user);
        $this->ensureAdminAccountRemainsAvailable(
            $user,
            $user->role,
            strtolower($user->status) === 'active' ? 'suspended' : 'active',
        );
        $this->statusUserId = $user->id;
        unset($this->statusTarget);
        $this->activeModal = 'status';
    }

    public function toggleStatus(): void
    {
        $this->authorizeAdmin();
        $user = User::query()->findOrFail($this->statusUserId);
        $this->ensureNotSelf($user);

        $newStatus = strtolower($user->status) === 'active' ? 'suspended' : 'active';
        $this->ensureAdminAccountRemainsAvailable($user, $user->role, $newStatus);

        DB::transaction(function () use ($user, $newStatus): void {
            $user->update(['status' => $newStatus]);

            ActivityLog::record(
                $newStatus === 'active' ? 'Account reactivated' : 'Account suspended',
                'User Management',
                ucfirst($newStatus) . ' the account for ' . $user->name . '.',
                $user,
            );
        });

        $this->closeModal();
        unset($this->userStats, $this->users);
        $this->dispatch('toast', title: $newStatus === 'active' ? 'Account reactivated' : 'Account suspended', message: $newStatus === 'active' ? 'The user can sign in again.' : 'The user can no longer sign in.', type: 'success');
    }

    public function openDeleteModal(int $userId): void
    {
        $this->authorizeAdmin();
        $user = User::query()->findOrFail($userId);
        $this->ensureNotSelf($user);
        $this->ensureAdminAccountRemainsAvailable($user, 'deleted', 'deleted');
        $this->ensureStudentHasNoPendingOrApprovedBookings($user);
        $this->deletingUserId = $user->id;
        unset($this->deleteTarget);
        $this->activeModal = 'delete';
    }

    public function deleteUser(): void
    {
        $this->authorizeAdmin();
        $user = User::query()->findOrFail($this->deletingUserId);
        $this->ensureNotSelf($user);
        $this->ensureAdminAccountRemainsAvailable($user, 'deleted', 'deleted');
        $deletedName = $user->name;

        DB::transaction(function () use ($user, $deletedName): void {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            $this->ensureNotSelf($user);
            $this->ensureAdminAccountRemainsAvailable($user, 'deleted', 'deleted');
            $this->ensureStudentHasNoPendingOrApprovedBookings($user);

            ActivityLog::record(
                'User deleted',
                'User Management',
                'Deleted the account for ' . $deletedName . '.',
                $user,
            );

            $user->delete();
        });

        $this->closeModal();
        $this->resetPage();
        unset($this->userStats, $this->users);
        $this->dispatch('toast', title: 'Account deleted', message: 'The account and its booking history were deleted.', type: 'success');
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->editingUserId = null;
        $this->viewingUserId = null;
        $this->statusUserId = null;
        $this->deletingUserId = null;
        $this->resetForm();
        unset($this->viewingUser, $this->statusTarget, $this->deleteTarget);
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-' . Carbon::parse($booking->booking_date)->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editingUserId = null;
        $this->name = '';
        $this->universityId = '';
        $this->email = '';
        $this->role = 'student';
        $this->status = 'active';
        $this->password = '';
        $this->password_confirmation = '';
    }

    private function ensureNotSelf(User $user): void
    {
        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages([
                'account' => 'You cannot suspend or delete your own account.',
            ]);
        }
    }

    private function ensureAdminAccountRemainsAvailable(User $user, string $role, string $status): void
    {
        if (
            $user->role === 'admin'
            && strtolower($user->status) === 'active'
            && ($role !== 'admin' || strtolower($status) !== 'active')
            && User::query()->where('role', 'admin')->whereRaw('LOWER(status) = ?', ['active'])->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'account' => 'The last active administrator cannot be suspended, deleted, or changed to another role.',
            ]);
        }

    }

    private function ensureStudentHasNoPendingOrApprovedBookings(User $user): void
    {
        if ($user->role === 'student' && $this->hasPendingOrApprovedBookings($user)) {
            throw ValidationException::withMessages([
                'account' => 'A student with pending or approved bookings cannot be deleted. You can suspend the account instead.',
            ]);
        }
    }

    private function hasPendingOrApprovedBookings(User $user): bool
    {
        return $user->bookings()
            ->whereIn('status', ['Pending', 'Approved'])
            ->exists();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-users" aria-label="User management">
        <div id="users-content">
            <div class="section-heading">
                <div>
                    <h2>User management</h2>
                    <p>Manage user access, roles, and account details.</p>
                </div>
                <div class="section-actions">
                    <button class="button button-outline" type="button" wire:click="resetFilters">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 7h-6V1M4 17h6v6M5.5 8a8 8 0 0 1 13-3l1.5 2M18.5 16a8 8 0 0 1-13 3L4 17"></path>
                        </svg>
                        Clear filters
                    </button>
                    <button class="button button-primary" type="button" wire:click="openCreateModal">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Add user
                    </button>
                </div>
            </div>

            @error('account')
                <div class="form-note" role="alert">{{ $message }}</div>
            @enderror

            <div class="availability-summary user-summary">
                <div class="mini-stat"><span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="9" cy="8" r="3.4"></circle>
                            <path
                                d="M2.5 20a6.5 6.5 0 0 1 13 0M16 5.2a3.4 3.4 0 0 1 0 6.6M17.5 20a6.5 6.5 0 0 0-2.2-4.9">
                            </path>
                        </svg></span>
                    <div><strong>{{ $this->userStats['total'] }}</strong><small>Total accounts</small></div>
                </div>
                <div class="mini-stat"><span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m2 9 10-5 10 5-10 5L2 9ZM6 11.5V16c3 2.7 9 2.7 12 0v-4.5M22 9v6"></path>
                        </svg></span>
                    <div><strong>{{ $this->userStats['students'] }}</strong><small>Student accounts</small></div>
                </div>
                <div class="mini-stat"><span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg></span>
                    <div><strong>{{ $this->userStats['staff'] }}</strong><small>Staff accounts</small></div>
                </div>
                <div class="mini-stat"><span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3 20 6v5c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg></span>
                    <div><strong>{{ $this->userStats['admins'] }}</strong><small>Administrator accounts</small></div>
                </div>
                <div class="mini-stat"><span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m5.5 5.5 13 13"></path>
                        </svg></span>
                    <div><strong>{{ $this->userStats['suspended'] }}</strong><small>Suspended accounts</small></div>
                </div>
            </div>

            <div class="filter-bar">
                <div class="search-control">
                    <span><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg></span>
                    <input id="user-search" type="search" wire:model.live.debounce.300ms="search"
                        placeholder="Search name, email or university ID" aria-label="Search users">
                </div>
                <div class="control-group"><label for="user-role">Role</label><select id="user-role"
                        wire:model.live="roleFilter">
                        <option value="all">All roles</option>
                        <option value="student">Students</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Administrators</option>
                    </select></div>
                <div class="control-group"><label for="user-status">Account status</label><select id="user-status"
                        wire:model.live="statusFilter">
                        <option value="all">All statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select></div>
                <div class="control-group"><label for="user-sort">Sort by</label><select id="user-sort"
                        wire:model.live="sortBy">
                        <option value="newest">Newest first</option>
                        <option value="name">Name A–Z</option>
                        <option value="oldest">Oldest first</option>
                        <option value="bookings">Most bookings</option>
                    </select></div>
            </div>

            <div class="results-meta">
                <span>Showing {{ $this->users->firstItem() ?? 0 }}–{{ $this->users->lastItem() ?? 0 }} of
                    {{ $this->users->total() }} account(s)</span>
                <span>Your own account cannot be suspended or deleted. Deleting an account also deletes its booking
                    history.</span>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>University ID</th>
                                <th>Role</th>
                                <th>Account status</th>
                                <th>Bookings</th>
                                <th>Joined / Last active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->users as $user)
                            @php($isSelf = $user->is(auth()->user()))
                            <tr wire:key="user-{{ $user->id }}">
                                <td data-label="User">
                                    <div class="user-cell">
                                        <span class="user-avatar {{ $user->role }}">{{ Str::upper(Str::substr(Str::initials($user->name), 0, 2)) }} 
                                    </span>
                                    <span class="user-cell-copy">
                                        <strong>{{ $user->name }} 
                                            @if ($isSelf)
                                                <small title="This is your account">(You)</small>
                                            @endif
                                         </strong>
                                         <small>{{ $user->email }}</small>
                                    </span>
                                    </div>
                                </td>
                                <td data-label="University ID"><span
                                        class="cell-primary">{{ $user->university_id }}</span></td>
                                <td data-label="Role">
                                    <span class="role-badge role-{{ $user->role }}">{{ Str::title($user->role) }}</span>
                                </td>
                                <td data-label="Account status"><span
                                        class="status-badge status-{{ strtolower($user->status) }}">{{ Str::title($user->status) }}</span>
                                </td>
                                <td data-label="Bookings"><span
                                        class="cell-primary">{{ $user->bookings_count }}</span><span
                                        class="cell-secondary">{{ $user->pending_bookings_count }} pending</span>
                                    @if ($user->role === 'student' && $user->active_bookings_count > 0)
                                        <span class="cell-secondary">{{ $user->active_bookings_count }} pending or approved — deletion blocked</span>
                                    @endif
                                </td>
                                <td data-label="Joined / Last active"><span
                                        class="cell-primary">{{ $user->created_at?->format('j M Y') }}</span><span
                                        class="cell-secondary">{{ $user->last_sign_in_at?->diffForHumans() ?? 'Never signed in' }}</span>
                                </td>
                                <td data-label="Actions">
                                    <div class="table-actions">
                                        <button class="icon-button" type="button"
                                            wire:click="openViewModal({{ $user->id }})" title="View profile"
                                            aria-label="View {{ $user->name }}"><svg class="icon" viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                        <button class="icon-button" type="button"
                                            wire:click="openEditModal({{ $user->id }})" title="Edit account"
                                            aria-label="Edit {{ $user->name }}"><svg class="icon" viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            class="icon-button {{ strtolower($user->status) === 'active' ? 'action-danger' : '' }}"
                                            type="button" wire:click="openStatusModal({{ $user->id }})"
                                            title="{{ strtolower($user->status) === 'active' ? 'Suspend account' : 'Reactivate account' }}"
                                            aria-label="{{ strtolower($user->status) === 'active' ? 'Suspend' : 'Reactivate' }} {{ $user->name }}"
                                            @disabled($isSelf)><svg class="icon" viewBox="0 0 24 24"
                                                aria-hidden="true">@if (strtolower($user->status) === 'active')
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                <path d="m5.5 5.5 13 13"></path>@else<path d="m5 12 4 4L19 6"></path>
                                                @endif
                                            </svg>
                                        </button>
                                        <button class="icon-button action-danger" type="button"
                                            wire:click="openDeleteModal({{ $user->id }})" title="Delete account"
                                            aria-label="Delete {{ $user->name }}"
                                            @disabled($isSelf || ($user->role === 'student' && $user->active_bookings_count > 0))>
                                            <svg class="icon"
                                                viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state"><strong>No users found</strong>
                                        <p>Try changing your search or filters, or add a new account.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($this->users->hasPages())
                    <div class="pagination-wrap">{{ $this->users->links() }}</div>
                @endif
            </div>
        </div>
    </section>

    @if ($activeModal === 'form')
        <div class="modal-backdrop" wire:click.self="closeModal">
            <div class="modal modal-large" role="dialog" aria-modal="true" aria-labelledby="user-form-title">
                <div class="modal-header">
                    <div class="modal-heading"><span class="modal-heading-icon"><svg class="icon" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <circle cx="9" cy="8" r="3.4"></circle>
                                <path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 5.2a3.4 3.4 0 0 1 0 6.6"></path>
                            </svg></span>
                        <div>
                            <h2 id="user-form-title">{{ $editingUserId ? 'Edit user account' : 'Add user account' }}</h2>
                            <p>{{ $editingUserId ? 'Update profile details and account access.' : 'Create a student, staff, or administrator account.' }}
                            </p>
                        </div>
                    </div>
                    <button class="icon-button modal-close" type="button" wire:click="closeModal"
                        aria-label="Close dialog"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg></button>
                </div>
                <form wire:submit="saveUser">
                    <div class="modal-body">
                        @error('account')
                            <div class="form-note" role="alert">{{ $message }}</div>
                        @enderror
                        <div class="form-grid two-columns">
                            <div class="field-group"><label for="user-name">Full name <span
                                        class="required">*</span></label><input id="user-name" type="text" wire:model="name"
                                    autocomplete="name">@error('name')<span
                                    class="field-error">{{ $message }}</span>@enderror</div>
                            <div class="field-group"><label for="user-university-id">University ID <span
                                        class="required">*</span></label><input id="user-university-id" type="text"
                                    wire:model="universityId" inputmode="numeric" maxlength="8"
                                    placeholder="8-digit ID">@error('universityId')<span
                                    class="field-error">{{ $message }}</span>@enderror</div>
                        </div>
                        <div class="field-group"><label for="user-email">Email address <span
                                    class="required">*</span></label><input id="user-email" type="email" wire:model="email"
                                autocomplete="email">@error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-grid two-columns">
                            <div class="field-group"><label for="user-role-input">Role <span
                                        class="required">*</span></label><select id="user-role-input" wire:model="role">
                                    <option value="student">Student</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Administrator</option>
                                </select>@error('role')<span class="field-error">{{ $message }}</span>@enderror</div>
                            @if (!$editingUserId)
                                <div class="field-group"><label for="user-status-input">Account status</label><select
                                        id="user-status-input" wire:model="status">
                                        <option value="active">Active — can sign in</option>
                                        <option value="suspended">Suspended — sign-in blocked</option>
                                    </select>@error('status')<span class="field-error">{{ $message }}</span>@enderror</div>
                            @else
                                <div class="field-group"><label>Account status</label>
                                    <p><span
                                            class="status-badge status-{{ $status }}">{{ Str::title($status) }}</span>
                                    </p><small>Use the account status action to suspend or reactivate.</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-grid two-columns">
                            <div class="field-group"><label for="user-password">Password @if (!$editingUserId)<span
                            class="required">*</span>@else (optional)@endif</label><input id="user-password"
                                    type="password" wire:model="password" autocomplete="new-password"
                                    placeholder="{{ $editingUserId ? 'Leave blank to keep current' : 'At least 8 characters' }}">@error('password')<span
                                    class="field-error">{{ $message }}</span>@enderror</div>
                            <div class="field-group"><label for="user-password-confirmation">Confirm password</label><input
                                    id="user-password-confirmation" type="password" wire:model="password_confirmation"
                                    autocomplete="new-password">@error('password_confirmation')<span
                                    class="field-error">{{ $message }}</span>@enderror</div>
                        </div>
                        <div class="form-note"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 11v6M12 7h.01"></path>
                            </svg><span>Changing a user's role changes the workspace they can access. Suspended accounts
                                cannot sign in.</span></div>
                    </div>
                    <div class="modal-footer"><button class="button button-outline" type="button"
                            wire:click="closeModal">Cancel</button><button class="button button-primary" type="submit"
                            wire:loading.attr="disabled" wire:target="saveUser"><svg class="icon" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path d="m5 12 4 4L19 6"></path>
                            </svg>{{ $editingUserId ? 'Save changes' : 'Create account' }}</button></div>
                </form>
            </div>
        </div>
    @endif

    @if ($activeModal === 'view' && $this->viewingUser)
    @php($profile = $this->viewingUser)
    <div class="modal-backdrop" wire:click.self="closeModal">
        <div class="modal modal-large" role="dialog" aria-modal="true" aria-labelledby="user-profile-title">
            <div class="modal-header">
                <div class="modal-heading"><span class="modal-heading-icon success"><svg class="icon"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg></span>
                    <div>
                        <h2 id="user-profile-title">{{ $profile->name }}</h2>
                        <p>{{ Str::title($profile->role) }} account · {{ $profile->university_id }}
                        </p>
                    </div>
                </div><button class="icon-button modal-close" type="button" wire:click="closeModal"
                    aria-label="Close dialog"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="modal-body">
                <div class="user-profile-head"><span
                        class="user-avatar {{ $profile->role }}">{{ Str::upper(Str::substr(Str::initials($profile->name), 0, 2)) }}</span>
                    <div>
                        <strong>{{ $profile->name }}</strong><small>{{ $profile->email }}</small>
                    </div>
                    <span
                        class="status-badge status-{{ strtolower($profile->status) }}">{{ Str::title($profile->status) }}
                    </span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item"><label>University ID</label>
                        <p>{{ $profile->university_id }}</p>
                    </div>
                    <div class="detail-item"><label>Role</label>
                        <p>{{ Str::title($profile->role) }}</p>
                    </div>
                    <div class="detail-item"><label>Account status</label>
                        <p>{{ Str::title($profile->status) }}</p>
                    </div>
                    <div class="detail-item"><label>Joined</label>
                        <p>{{ $profile->created_at?->format('j M Y') }}</p>
                    </div>
                    <div class="detail-item"><label>Last sign-in</label>
                        <p>{{ $profile->last_sign_in_at?->diffForHumans() ?? 'Never signed in' }}</p>
                    </div>
                    <div class="detail-item"><label>Last updated</label>
                        <p>{{ $profile->updated_at?->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="user-stat-row">
                    <div class="user-stat"><strong>{{ $profile->bookings_count }}</strong><small>Total bookings</small>
                    </div>
                    <div class="user-stat"><strong>{{ $profile->pending_bookings_count }}</strong><small>Pending</small>
                    </div>
                    <div class="user-stat">
                        <strong>{{ $profile->approved_bookings_count }}</strong><small>Approved</small></div>
                    <div class="user-stat">
                        <strong>{{ $profile->completed_bookings_count }}</strong><small>Completed</small></div>
                </div>
                <div class="booking-timeline">
                    <h3>Recent bookings</h3>
                    @forelse ($profile->bookings as $booking)
                        <div class="timeline-item" wire:key="profile-booking-{{ $booking->id }}"><span
                                class="timeline-dot"></span>
                            <div class="timeline-copy"><strong>{{ $booking->equipment?->name ?? 'Equipment unavailable' }} ×
                                    {{ $booking->quantity }}</strong>
                                <p>{{ $booking->purpose ?: 'No purpose provided' }}</p>
                                <small>{{ $this->bookingReference($booking) }} ·
                                    {{ $booking->booking_date->format('j M Y') }} ·
                                    {{ Carbon::parse($booking->start_time)->format('g:i A') }} –
                                    {{ Carbon::parse($booking->end_time)->format('g:i A') }}</small>
                            </div><span style="margin-left:auto"><span
                                    class="status-badge status-{{ strtolower($booking->status) }}">{{ $booking->status }}</span></span>
                        </div>
                    @empty
                        <p>This user has no bookings yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer"><button class="button button-outline" type="button"
                    wire:click="closeModal">Close</button><button class="button button-primary" type="button"
                    wire:click="openEditModal({{ $profile->id }})">Edit account</button></div>
        </div>
    </div>
    @endif

    @if ($activeModal === 'status' && $this->statusTarget)
    @php($statusTarget = $this->statusTarget)
    @php($willReactivate = strtolower($statusTarget->status) !== 'active')
    <div class="modal-backdrop" wire:click.self="closeModal">
        <div class="modal modal-small" role="dialog" aria-modal="true" aria-labelledby="status-confirm-title">
            <div class="modal-header">
                <div class="modal-heading"><span
                        class="modal-heading-icon {{ $willReactivate ? 'success' : 'danger' }}"><svg class="icon"
                            viewBox="0 0 24 24" aria-hidden="true">@if ($willReactivate)
                            <path d="m5 12 4 4L19 6"></path>@else<circle cx="12" cy="12" r="9"></circle>
                            <path d="m5.5 5.5 13 13"></path>@endif
                        </svg></span>
                    <div>
                        <h2 id="status-confirm-title">
                            {{ $willReactivate ? 'Reactivate this account?' : 'Suspend this account?' }}</h2>
                        <p>Confirm the account access change.</p>
                    </div>
                </div><button class="icon-button modal-close" type="button" wire:click="closeModal"
                    aria-label="Close dialog"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="modal-body">
                @error('account')
                    <div class="form-note" role="alert">{{ $message }}</div>
                @enderror
                <div class="confirm-message"><strong>{{ $statusTarget->name }}</strong> will
                    {{ $willReactivate ? 'be able to sign in again' : 'no longer be able to sign in' }}. Their account
                    and booking history will be preserved.</div>
            </div>
            <div class="modal-footer"><button class="button button-outline" type="button" wire:click="closeModal">Go
                    back</button><button class="button {{ $willReactivate ? 'button-success' : 'button-danger' }}"
                    type="button" wire:click="toggleStatus" wire:loading.attr="disabled"
                    wire:target="toggleStatus">{{ $willReactivate ? 'Reactivate account' : 'Suspend account' }}</button>
            </div>
        </div>
    </div>
    @endif

    @if ($activeModal === 'delete' && $this->deleteTarget)
    @php($deleteTarget = $this->deleteTarget)
    <div class="modal-backdrop" wire:click.self="closeModal">
        <div class="modal modal-small" role="dialog" aria-modal="true" aria-labelledby="delete-confirm-title">
            <div class="modal-header">
                <div class="modal-heading"><span class="modal-heading-icon danger"><svg class="icon" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                        </svg></span>
                    <div>
                        <h2 id="delete-confirm-title">Delete this account?</h2>
                        <p>This action cannot be undone.</p>
                    </div>
                </div><button class="icon-button modal-close" type="button" wire:click="closeModal"
                    aria-label="Close dialog"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="modal-body">
                @error('account')
                    <div class="form-note" role="alert">{{ $message }}</div>
                @enderror
                <div class="confirm-message">Permanently delete <strong>{{ $deleteTarget->name }}</strong>
                    ({{ $deleteTarget->university_id }})? The account's booking history will also be deleted. Activity
                    audit records will be retained.</div>
            </div>
            <div class="modal-footer"><button class="button button-outline" type="button"
                    wire:click="closeModal">Cancel</button><button class="button button-danger" type="button"
                    wire:click="deleteUser" wire:loading.attr="disabled" wire:target="deleteUser">Delete
                    account</button></div>
        </div>
    </div>
    @endif
</div>