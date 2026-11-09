<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="University Audio Visual Equipment Booking and Management System">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <title>@yield('pageTitle', 'AVBMS | University Audio Visuals Department') </title>

    {{-- Prevents page from flashing in light mode before switching to dark mode if the user has dark mode enabled --}}
    <script>
        (() => {
            const savedTheme = localStorage.getItem('avbms_theme') || 'light';

            document.documentElement.dataset.theme =
                savedTheme === 'dark' ? 'dark' : 'light';
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('src/styles/styles.css') }}">

    @stack('stylesheets')


</head>

<body>
    <!-- Initial application loading screen -->
    <x-loading-screen />

    <!-- Toast messages are inserted here by JavaScript -->
    <x-toast />

    <!-- Authentication: login and registration share one animated shell -->
    <div class="auth-shell" id="auth-shell">
        <section class="auth-showcase" aria-label="About AVBMS">
            <div class="showcase-grid" aria-hidden="true"></div>
            <div class="showcase-orb orb-one" aria-hidden="true"></div>
            <div class="showcase-orb orb-two" aria-hidden="true"></div>
            <div class="showcase-content">
                <a class="brand brand-light" href="#" data-auth-home="" aria-label="AVBMS home">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 40 40" fill="none">
                            <path d="M8 11h24v15H8z" stroke="currentColor" stroke-width="2.5"></path>
                            <path d="M20 26v6M14 32h12M12 16h16" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round"></path>
                            <circle cx="20" cy="19" r="3" fill="currentColor"></circle>
                        </svg>
                    </span>
                    <span><strong>AVBMS</strong><small>Audio Visuals Department</small></span>
                </a>
                <div class="showcase-copy">
                    <span class="eyebrow eyebrow-light"><span></span> University equipment, ready when you are</span>
                    <h1>Borrow with clarity.<br><em>Teach without delay.</em></h1>
                    <p>Plan ahead, reserve the right equipment, and follow every request from submission to
                        return—without joining a physical queue.</p>
                </div>
                <div class="showcase-board" aria-label="Live availability preview">
                    <div class="board-top"><span>Next available session</span><span class="live-dot">Live
                            inventory</span></div>
                    <div class="board-time"><strong id="showcase-time">8:00 AM — 9:00 AM</strong><span
                            id="showcase-day">Saturday 7 Nov</span></div>
                    <div class="board-equipment">
                        <div><span class="equipment-mini-icon" data-icon="projector"><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                    <circle cx="15" cy="12.5" r="2.5"></circle>
                                    <path d="M7 11h3M7 14h2M6 18v2M18 18v2M6 7V5h5v2"></path>
                                </svg></span><span><strong>Projectors</strong><small>Lecture-ready laser
                                    units</small></span><b>5 total</b></div>
                        <div><span class="equipment-mini-icon" data-icon="cable"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path
                                        d="M8 3v5M5 3v5M4 8h5v3a3 3 0 0 1-3 3H5a3 3 0 0 0-3 3v4M16 21v-5M19 21v-5M15 16h5v-3a3 3 0 0 1 3-3h0a3 3 0 0 0 3-3V3"
                                        transform="translate(-2 0) scale(.95)"></path>
                                </svg></span><span><strong>HDMI Cables</strong><small>High-speed 3 metre
                                    cables</small></span><b>15 total</b></div>
                        <div><span class="equipment-mini-icon" data-icon="audio"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <rect x="5" y="2" width="14" height="20" rx="3"></rect>
                                    <circle cx="12" cy="15" r="4"></circle>
                                    <circle cx="12" cy="7" r="2"></circle>
                                    <path d="M9 22h6"></path>
                                </svg></span><span><strong>Audio Systems</strong><small>Portable PA and
                                    microphone</small></span><b>3 total</b></div>
                    </div>
                    <div class="board-footer"><span><i></i> Slot-based availability</span><span>No double booking</span>
                    </div>
                </div>
                <div class="showcase-features">
                    <span><b>01</b> Check live stock</span><span><b>02</b> Reserve a time</span><span><b>03</b> Track to
                        return</span>
                </div>
            </div>
        </section>

        <main class="auth-panel">
            <div class="auth-panel-top">
                <span class="auth-mobile-brand">AVBMS</span>
                <x-theme-toggle />
            </div>

            @yield('content')

            <footer class="auth-footer">University Audio Visuals Department <span>•</span> AVBMS 1.0</footer>
        </main>
    </div>




    <script src="{{ asset('src/scripts/app.js') }}"></script>
    @stack('scripts')
</body>

</html>