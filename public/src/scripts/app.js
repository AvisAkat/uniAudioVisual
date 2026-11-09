// Script for managing the app loader during Livewire requests and navigation events.
(() => {
    const loader = document.getElementById("app-loader");

    if (!loader) return;

    let livewireRequests = 0;
    let navigationActive = false;
    let hideTimer = null;

    const showLoader = () => {
        clearTimeout(hideTimer);

        loader.classList.remove("is-finished");
    };

    const hideLoader = (delay = 350) => {
        clearTimeout(hideTimer);

        hideTimer = setTimeout(() => {
            if (livewireRequests === 0 && !navigationActive) {
                loader.classList.add("is-finished");
            }
        }, delay);
    };

    // Initial browser page load / refresh
    if (document.readyState === "complete") {
        hideLoader();
    } else {
        window.addEventListener(
            "load",
            () => {
                hideLoader();
            },
            { once: true },
        );
    }
})();

// Script for managing the theme toggle button and persisting the user's theme preference.
(() => {
    const initializeThemeToggle = () => {
        const button = document.getElementById("theme-toggle");
        const moonIcon = document.getElementById("moon-icon");
        const sunIcon = document.getElementById("sun-icon");

        if (!button || !moonIcon || !sunIcon) return;

        const applyTheme = (theme) => {
            const isDark = theme === "dark";

            document.documentElement.dataset.theme = isDark ? "dark" : "light";

            moonIcon.hidden = isDark;
            sunIcon.hidden = !isDark;

            button.setAttribute("aria-pressed", String(isDark));
            button.setAttribute(
                "aria-label",
                isDark ? "Switch to light mode" : "Switch to dark mode",
            );

            localStorage.setItem("avbms_theme", isDark ? "dark" : "light");
        };

        // Apply saved theme when the component initializes.
        applyTheme(
            localStorage.getItem("avbms_theme") === "dark" ? "dark" : "light",
        );

        button.addEventListener("click", () => {
            const currentTheme = document.documentElement.dataset.theme;

            applyTheme(currentTheme === "dark" ? "light" : "dark");
        });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeThemeToggle, {
            once: true,
        });
    } else {
        initializeThemeToggle();
    }
})();

// Sidebar navigation button click handling
document.addEventListener("DOMContentLoaded", () => {
    const toggleButton = document.getElementById("sidebar-toggle");
    const closeButton = document.getElementById("sidebar-mobile-close");
    const backdrop = document.getElementById("sidebar-backdrop");

    if (!toggleButton) return;

    const isMobile = () => window.innerWidth <= 900;

    // Restore saved desktop sidebar preference.
    if (!isMobile()) {
        const savedState = localStorage.getItem("avbms_sidebar");

        document.body.classList.toggle(
            "sidebar-collapsed",
            savedState === "collapsed",
        );
    }

    function openMobileSidebar() {
        document.body.classList.add("mobile-nav-open");

        if (backdrop) {
            backdrop.hidden = false;
        }

        toggleButton.setAttribute("aria-expanded", "true");
    }

    function closeMobileSidebar() {
        document.body.classList.remove("mobile-nav-open");

        if (backdrop) {
            backdrop.hidden = true;
        }

        toggleButton.setAttribute("aria-expanded", "false");
    }

    function toggleSidebar() {
        if (isMobile()) {
            if (document.body.classList.contains("mobile-nav-open")) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }

            return;
        }

        const collapsed = document.body.classList.toggle("sidebar-collapsed");

        localStorage.setItem(
            "avbms_sidebar",
            collapsed ? "collapsed" : "expanded",
        );

        toggleButton.setAttribute("aria-expanded", String(!collapsed));
    }

    toggleButton.addEventListener("click", toggleSidebar);

    closeButton?.addEventListener("click", closeMobileSidebar);

    backdrop?.addEventListener("click", closeMobileSidebar);

    // Close mobile sidebar when switching to desktop.
    window.addEventListener("resize", () => {
        if (!isMobile()) {
            closeMobileSidebar();
        }
    });
});

//Show / Hide Password Toggle
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-password-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const inputId = button.dataset.passwordToggle;
            const passwordInput = document.getElementById(inputId);

            if (!passwordInput) return;

            const isPassword = passwordInput.type === "password";

            // Toggle input visibility
            passwordInput.type = isPassword ? "text" : "password";

            // Update accessibility label
            button.setAttribute(
                "aria-label",
                isPassword ? "Hide password" : "Show password",
            );

            // Update icon
            button.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
        });
    });
});

const eyeIcon = `
    <svg class="icon" viewBox="0 0 24 24">
        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
        <circle cx="12" cy="12" r="3"/>
    </svg>
`;

const eyeOffIcon = `
    <svg class="icon" viewBox="0 0 24 24">
        <path d="m3 3 18 18"/>
        <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
        <path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a18 18 0 0 1-2.1 3.2"/>
        <path d="M6.6 6.6C3.5 8.6 2 12 2 12s3.5 8 10 8a10 10 0 0 0 4.1-.9"/>
    </svg>
`;
