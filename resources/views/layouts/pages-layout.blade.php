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

    <div class="app-shell" id="app-shell">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <div class="sidebar-brand">
                <a href="#overview" class="brand brand-light" data-view="overview">
                    <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 40 40" fill="none">
                            <path d="M8 11h24v15H8z" stroke="currentColor" stroke-width="2.5"></path>
                            <path d="M20 26v6M14 32h12M12 16h16" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round"></path>
                            <circle cx="20" cy="19" r="3" fill="currentColor"></circle>
                        </svg></span>
                    <span class="brand-copy"><strong>AVBMS</strong><small>Audio Visuals Dept.</small></span>
                </a>
                <button class="sidebar-mobile-close icon-button" type="button" id="sidebar-mobile-close"
                    aria-label="Close navigation"><span data-icon="x"><svg class="icon " viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg></span></button>
            </div>
            <div class="sidebar-role"><span class="role-pulse"></span>
                <span class="brand-copy"><small>Signed in as</small><strong id="sidebar-role-name"
                        style="text-transform: capitalize;">
                        {{ auth()->user()->role }} portal</strong>
                </span>
            </div>
            <nav class="sidebar-nav">
                @if (auth()->user()->role == 'student')
                    <div class="nav-group">
                        <span class="nav-group-label brand-copy">Workspace</span>
                        <a href="{{ route('student.dashboard') }}">
                            <button class="nav-item {{ Route::Is('student.dashboard') ? 'is-active' : '' }}" type="button"
                                data-view="overview" data-roles="student" aria-current="page">
                                <span class="nav-icon" data-icon="dashboard">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                        <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                        <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                        <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                    </svg>
                                </span>
                                <span class="nav-label">Dashboard</span>
                            </button>
                        </a>
                        <a href="{{ route('student.browse_equipment') }}">
                            <button class="nav-item {{ Route::Is('student.browse_equipment') ? 'is-active' : '' }}"
                                type="button" data-view="equipment" data-roles="student">
                                <span class="nav-icon" data-icon="monitor">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="13" rx="2"></rect>
                                        <path d="M8 21h8M12 17v4M7 8h10"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">Browse Equipment</span>
                            </button>
                        </a>
                        <a href="{{ route('student.check_availability') }}">
                            <button class="nav-item {{ Route::Is('student.check_availability') ? 'is-active' : '' }}"
                                type="button" data-view="availability" data-roles="student">
                                <span class="nav-icon" data-icon="calendar">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                        <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01">
                                        </path>
                                    </svg>
                                </span>
                                <span class="nav-label">Check Availability</span>
                            </button>
                        </a>
                        <a href="{{ route('student.my_bookings') }}">
                            <button class="nav-item {{ Route::Is('student.my_bookings') ? 'is-active' : '' }}"" type="
                                button" data-view="my-bookings" data-roles="student">
                                <span class="nav-icon" data-icon="clipboard">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                        <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">My Bookings</span>
                                <span class="nav-count" id="student-booking-count">{{ $pendingBookingCount }}</span>
                            </button>
                        </a>
                    </div>
                @endif
                @if (in_array(auth()->user()->role, ['staff', 'admin'], true))
                    <div class="nav-group">
                        <span class="nav-group-label brand-copy">Workspace</span>
                        @if (auth()->user()->role == 'admin')
                            <a href="{{ route('admin.dashboard') }}">
                                <button class="nav-item {{ Route::Is('admin.dashboard') ? 'is-active' : '' }}" type="button"
                                    data-view="overview" data-roles="admin" aria-current="page">
                                    <span class="nav-icon" data-icon="dashboard">
                                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                            <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                            <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                            <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                        </svg>
                                    </span>
                                    <span class="nav-label">Dashboard</span>
                                </button>
                            </a>
                        @else
                            <a href="{{ route('staff.dashboard') }}">
                                <button class="nav-item {{ Route::Is('staff.dashboard') ? 'is-active' : '' }}" type="button"
                                    data-view="overview" data-roles="staff" aria-current="page">
                                    <span class="nav-icon" data-icon="dashboard">
                                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                            <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                            <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                            <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                        </svg>
                                    </span>
                                    <span class="nav-label">Dashboard</span>
                                </button>
                            </a>
                        @endif

                    </div>
                    <div class="nav-group" data-staff-nav="">
                        <span class="nav-group-label brand-copy">Department management</span>
                        <a href="{{ route('staff.booking_request') }}">
                            <button class="nav-item {{ Route::Is('staff.booking_request') ? 'is-active' : '' }}"
                                type="button" data-view="requests" data-roles="staff,admin">
                                <span class="nav-icon" data-icon="requests">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <path d="M14 2v6h6M8 13h8M8 17h5"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">Booking Requests</span>
                                <span class="nav-count" id="staff-request-count">{{ $pendingBookingCount }}</span>
                            </button>
                        </a>
                        <a href="{{ route('staff.inventory') }}">
                            <button class="nav-item {{ Route::Is('staff.inventory') ? 'is-active' : '' }}" type="button"
                                data-view="inventory" data-roles="staff,admin">
                                <span class="nav-icon" data-icon="boxes">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m12 2 8 4-8 4-8-4 8-4Z M4 6v6l8 4 8-4V6M4 12v6l8 4 8-4v-6M12 10v12"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">Inventory</span>
                            </button>
                        </a>
                        <a href="{{ route('staff.time_slot') }}">
                            <button class="nav-item {{ Route::Is('staff.time_slot') ? 'is-active' : '' }}" type="button"
                                data-view="slots" data-roles="staff,admin">
                                <span class="nav-icon" data-icon="clock">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">Time Slots &amp; Calendar</span>
                            </button>
                        </a>
                        <a href="{{ route('staff.usage_history') }}">
                            <button class="nav-item {{ Route::Is('staff.usage_history') ? 'is-active' : '' }}" type="button"
                                data-view="usage" data-roles="staff,admin">
                                <span class="nav-icon" data-icon="history">
                                    <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path>
                                        <path d="M3 3v5h5M12 7v5l3 2"></path>
                                    </svg>
                                </span>
                                <span class="nav-label">Usage History</span>
                            </button>
                        </a>
                    </div>
                    @if (auth()->user()->role == 'admin')
                        <div class="nav-group" data-staff-nav="">
                            <span class="nav-group-label brand-copy">Account Management</span>
                            <a href="{{ route('admin.manage_users') }}">
                                <button class="nav-item {{ Route::Is('admin.manage_users') ? 'is-active' : '' }}" type="button"
                                    data-view="users" data-roles="admin">
                                    <span class="nav-icon" data-icon="users"><svg class="icon " viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <circle cx="9" cy="8" r="3.4"></circle>
                                            <path d="M2.5 20a6.5 6.5 0 0 1 13 0"></path>
                                            <path d="M16 5.2a3.4 3.4 0 0 1 0 6.6M17.5 20a6.5 6.5 0 0 0-2.2-4.9"></path>
                                        </svg></span>
                                    <span class="nav-label">Users</span>
                                </button>
                            </a>
                            <a href="{{ route('admin.activity_log') }}">
                                <button class="nav-item {{ Route::Is('admin.activity_log') ? 'is-active' : '' }}" type="button"
                                    data-view="activity" data-roles="staff,admin">
                                    <span class="nav-icon" data-icon="activity">
                                        <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                        </svg>
                                    </span>
                                    <span class="nav-label">Activity Log</span>
                                </button>
                            </a>
                        </div>
                    @endif
                @endif

            </nav>
            @if (auth()->user()->role == 'student')
                <div class="sidebar-help brand-copy">
                    <span data-icon="help">
                        <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M9.5 9a2.6 2.6 0 1 1 4.2 2c-1 .7-1.7 1.2-1.7 2.5M12 17h.01"></path>
                        </svg>
                    </span>
                    <div><strong>Need assistance?</strong>
                        <p>Visit Room AV-104 or call ext. 2140 during office hours.</p>
                    </div>
                </div>
            @endif
            <div class="sidebar-user">
                <span class="avatar" id="sidebar-avatar"
                    style="text-transform: uppercase">{{ substr(auth()->user()->name, 0, 2) }}</span>
                <span class="brand-copy sidebar-user-copy"><strong
                        id="sidebar-user-name">{{ auth()->user()->name }}</strong><small
                        id="sidebar-user-email">{{ auth()->user()->email }}</small></span>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button class="icon-button logout-button" type="submit" aria-label="Sign out" title="Sign out">
                        <span data-icon="logout">
                            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </aside>
        <div class="sidebar-backdrop" id="sidebar-backdrop" hidden=""></div>

        <div class="app-main">
            <header class="topbar">
                <div class="topbar-leading">
                    <button class="icon-button sidebar-toggle" type="button" id="sidebar-toggle"
                        aria-label="Collapse navigation" title="Toggle navigation"><span data-icon="menu"><svg
                                class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg></span></button>
                    <div class="page-identity">
                        <h1 id="page-title">{{ $appInfo['appTitle'] }}</h1>
                        <p id="page-subtitle">{{ $appInfo['appDescription'] }}</p>
                    </div>
                </div>
                <div class="topbar-actions">
                    <span class="today-chip">
                        <span data-icon="calendar">
                            <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg>
                        </span>
                        <span id="topbar-date">{{ now()->format('D, j M Y') }}</span>
                    </span>
                    {{-- Theme toggle button --}}
                    <x-theme-toggle />
                    <div class="notification-wrap">
                        @livewire('notification-panel')
                    </div>
                    <button class="topbar-profile" type="button" data-view="overview" aria-label="Open dashboard">
                        <span class="avatar" id="topbar-avatar" style="text-transform: uppercase">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </span>
                        <span>
                            <strong id="topbar-user-name">{{ auth()->user()->name }}</strong>
                            <small id="topbar-role" style="text-transform: capitalize;">
                                {{ auth()->user()->role }}
                            </small>
                        </span>
                    </button>
                </div>
            </header>

            <main class="main-content" id="main-content">
                @yield('content')
            </main>
            <footer class="app-footer">
                <span>AVBMS · University Audio Visuals Department</span>
                <span>Created by @AvisAkat</span>
            </footer>
        </div>
    </div>

    <script src="{{ asset('src/scripts/app.js') }}"></script>
    @stack('scripts')
</body>

</html>