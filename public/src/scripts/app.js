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
