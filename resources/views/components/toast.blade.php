<div
    class="toast-region"
    id="toast-region"
    aria-live="polite"
    aria-atomic="false"
></div>

<script>
    window.showToast = function (message, type = 'info', title = '') {
        const region = document.getElementById('toast-region');

        if (!region) {
            console.error('Toast region was not found.');
            return;
        }

        const allowedTypes = ['success', 'error', 'warning', 'info'];

        if (!allowedTypes.includes(type)) {
            type = 'info';
        }

        const headings = {
            success: 'Success',
            error: 'Something went wrong',
            warning: 'Please note',
            info: 'Notification'
        };

        const icons = {
            success: '✓',
            error: '✕',
            warning: '!',
            info: 'i'
        };

        const toast = document.createElement('div');

        toast.className = `toast ${type}`;
        toast.setAttribute(
            'role',
            type === 'error' ? 'alert' : 'status'
        );

        const icon = document.createElement('span');
        icon.className = 'toast-icon';
        icon.textContent = icons[type];

        const copy = document.createElement('span');
        copy.className = 'toast-copy';

        const heading = document.createElement('strong');
        heading.textContent = title || headings[type];

        const text = document.createElement('p');
        text.textContent = message;

        copy.append(heading, text);

        const close = document.createElement('button');
        close.className = 'toast-close';
        close.type = 'button';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.textContent = '×';

        toast.append(icon, copy, close);
        region.appendChild(toast);

        const removeToast = () => {
            if (!toast.isConnected) return;

            toast.classList.add('is-leaving');

            setTimeout(() => toast.remove(), 250);
        };

        close.addEventListener('click', removeToast);

        setTimeout(removeToast, 5200);
    };

    // Listen for notifications dispatched by Livewire.
    window.addEventListener('toast', function (event) {
        const data = event.detail || {};

        window.showToast(
            data.message || '',
            data.type || 'info',
            data.title || ''
        );
    });

    // Display notifications flashed by Laravel controllers.
    const flashToast = @js(session('toast'));

    if (flashToast) {
        window.showToast(
            flashToast.message || '',
            flashToast.type || 'info',
            flashToast.title || ''
        );
    }
</script>