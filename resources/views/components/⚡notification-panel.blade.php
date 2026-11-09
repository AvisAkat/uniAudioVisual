<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $isOpen = false;

    #[Computed]
    public function unreadCount(): int
    {
        return $this->userNotifications()->whereNull('read_at')->count();
    }

    #[Computed]
    public function notifications(): Collection
    {
        return $this->userNotifications()
            ->latest('created_at')
            ->latest('id')
            ->limit(30)
            ->get();
    }

    public function toggleDrawer(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function closeDrawer(): void
    {
        $this->isOpen = false;
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = $this->userNotifications()->findOrFail($notificationId);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function markAllAsRead(): void
    {
        $this->userNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function clearAll(): void
    {
        $this->userNotifications()->delete();
    }

    private function userNotifications(): HasMany
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 401);

        return $user->notifications();
    }
};
?>

<div x-effect="document.body.classList.toggle('drawer-open', $wire.isOpen)"
    x-on:keydown.escape.window="$wire.isOpen && $wire.closeDrawer()">
    <button type="button" id="notification-toggle" class="icon-button notification-button"
        aria-controls="notification-drawer" aria-label="Open notifications" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
        wire:click="toggleDrawer">
        <span data-icon="bell">
            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"></path>
            </svg>
        </span>
        @if ($this->unreadCount > 0)
            <span class="notification-count" aria-label="{{ $this->unreadCount }} unread notifications">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <div class="drawer-backdrop" id="drawer-backdrop" @if (! $isOpen) hidden @endif
        wire:click="closeDrawer"></div>
    <aside class="notification-drawer {{ $isOpen ? 'is-open' : '' }}" id="notification-drawer"
        aria-label="Notifications" aria-hidden="{{ $isOpen ? 'false' : 'true' }}">
        <div class="drawer-header">
            <div>
                <span class="eyebrow">Stay up to date</span>
                <h2>Notifications</h2>
            </div>
            <button class="icon-button" type="button" id="notification-close" aria-label="Close notifications"
                wire:click="closeDrawer">
                <span data-icon="x">
                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg>
                </span>
            </button>
        </div>

        <div class="drawer-actions">
            <button class="text-button" type="button" wire:click="markAllAsRead"
                @disabled($this->unreadCount === 0)>Mark all as read</button>
            <button class="text-button danger-text" type="button" wire:click="clearAll"
                wire:confirm="Clear all notifications?">Clear all</button>
        </div>

        <div class="notification-list" id="notification-list">
            @if ($isOpen)
                @forelse ($this->notifications as $notification)
                    @php
                        $iconClass = match ($notification->type) {
                            'success' => 'success',
                            'warning' => 'warning',
                            'error' => 'danger',
                            default => '',
                        };
                    @endphp
                    <button class="notification-item {{ $notification->read_at === null ? 'is-unread' : '' }}"
                        type="button" wire:key="notification-{{ $notification->id }}"
                        wire:click="markAsRead({{ $notification->id }})"
                        aria-label="{{ $notification->read_at === null ? 'Unread notification: ' : 'Notification: ' }}{{ $notification->title }}">
                        <span class="notification-icon {{ $iconClass }}">
                            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 3 3 5-6"></path>
                            </svg>
                        </span>
                        <span class="notification-copy">
                            <strong>{{ $notification->title }}</strong>
                            <p>{{ $notification->message }}</p>
                            <small>{{ $notification->created_at?->diffForHumans() }}</small>
                        </span>
                    </button>
                @empty
                    <div class="notification-empty">You’re all caught up. New updates will appear here.</div>
                @endforelse
            @endif
        </div>

        <div class="drawer-footer">
            <span data-icon="info">
                <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v6M12 7h.01"></path>
                </svg>
            </span>
            Booking, account, and inventory updates appear here.
        </div>
    </aside>
</div>