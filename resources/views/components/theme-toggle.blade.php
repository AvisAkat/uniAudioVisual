 <button
    type="button"
    id="theme-toggle"
    class="icon-button theme-toggle"
    aria-label="Switch to dark mode"
    aria-pressed="false"
    title="Change theme"
>
    {{-- Moon icon: shown in light mode --}}
    <span id="moon-icon">
        <svg viewBox="0 0 24 24" width="20" height="20"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.9 13A9 9 0 0 1 11 3.1
                     9 9 0 1 0 20.9 13Z"/>
        </svg>
    </span>

    {{-- Sun icon: shown in dark mode --}}
    <span id="sun-icon" hidden>
        <svg viewBox="0 0 24 24" width="20" height="20"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42
                     M17.65 17.65l1.42 1.42M2 12h2
                     M20 12h2M4.93 19.07l1.42-1.42
                     M17.65 6.35l1.42-1.42"/>
        </svg>
    </span>
</button>