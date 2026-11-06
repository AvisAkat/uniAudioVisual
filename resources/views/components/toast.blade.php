<div class="toast-region" x-data="{
        toasts: [],
        nextId: 0,

        show(event) {
            const detail = event.detail || {};

            const toast = {
                id: ++this.nextId,
                title: detail.title || 'Notification',
                message: detail.message || '',
                type: ['success', 'error', 'warning', 'info'].includes(detail.type)
                    ? detail.type
                    : 'info'
            };

            this.toasts.push(toast);

            setTimeout(() => this.remove(toast.id), 4000);
        },

        remove(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        }
    }" x-on:toast.window="show($event)" aria-live="polite" aria-atomic="false">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="toast" :class="toast.type" x-transition role="status">
            <div class="toast-icon">
                <template x-if="toast.type === 'success'">
                    <span>✓</span>
                </template>

                <template x-if="toast.type === 'error'">
                    <span>!</span>
                </template>

                <template x-if="toast.type === 'warning'">
                    <span>⚠</span>
                </template>

                <template x-if="toast.type === 'info'">
                    <span>i</span>
                </template>
            </div>

            <div class="toast-copy">
                <strong x-text="toast.title"></strong>
                <p x-text="toast.message"></p>
            </div>

            <button type="button" class="toast-close" aria-label="Dismiss notification" @click="remove(toast.id)">
                &times;
            </button>
        </div>
    </template>
</div>