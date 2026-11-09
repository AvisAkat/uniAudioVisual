<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <div class="drawer-backdrop" id="drawer-backdrop" hidden></div>
    <aside class="notification-drawer is-open" id="notification-drawer" aria-label="Notifications" aria-hidden="false">
        <div class="drawer-header">
            <div><span class="eyebrow">Stay up to date</span>
                <h2>Notifications</h2>
            </div><button class="icon-button" type="button" id="notification-close"
                aria-label="Close notifications"><span data-icon="x"><svg class="icon " viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12"></path>
                    </svg></span></button>
        </div>
        <div class="drawer-actions"><button class="text-button" type="button" id="mark-all-read">Mark all as
                read</button><button class="text-button danger-text" type="button" id="clear-notifications">Clear
                all</button></div>
        <div class="notification-list" id="notification-list">
            <button class="notification-item is-unread" type="button"
                data-notification-id="notification-mw80k5fk-14fjcy9"
                aria-label="Unread notification: Equipment inventory updated">
                <span class="notification-icon success"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 3 3 5-6"></path>
                    </svg></span>
                <span class="notification-copy"><strong>Equipment inventory updated</strong>
                    <p>High-Speed HDMI Cable (3m) now has 18 total unit(s), condition: Good.</p><small>yesterday · 8 Nov
                        2026, 15:55</small>
                </span>
            </button>
        </div>
        <div class="drawer-footer"><span data-icon="info"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v6M12 7h.01"></path>
                </svg></span> Booking and return reminders are generated automatically from your reservation times.
        </div>
    </aside>
</div>