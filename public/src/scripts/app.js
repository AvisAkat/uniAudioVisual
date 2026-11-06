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

    // Livewire request starts
    document.addEventListener("livewire:init", () => {
        Livewire.hook("request", ({ succeed, fail }) => {
            livewireRequests++;

            showLoader();

            const finishRequest = () => {
                livewireRequests = Math.max(0, livewireRequests - 1);

                if (livewireRequests === 0) {
                    hideLoader();
                }
            };

            succeed(() => {
                finishRequest();
            });

            fail(() => {
                finishRequest();
            });
        });

        // Livewire navigation starts
        document.addEventListener("livewire:navigate", () => {
            navigationActive = true;
            showLoader();
        });

        // Livewire navigation finishes
        document.addEventListener("livewire:navigated", () => {
            navigationActive = false;
            hideLoader();
        });
    });
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
