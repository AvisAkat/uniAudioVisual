@extends('layouts.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'AVBMS | University Audio Visual Department')
@section('content')

    <main class="main-content" id="main-content">
        <section class="app-view is-entering" id="view-overview" aria-label="Dashboard overview">
            <div id="overview-content">
                <section class="welcome-banner">
                    <div class="welcome-copy"><span class="eyebrow eyebrow-light">Student workspace</span>
                        <h2>Good evening, Demo.</h2>
                        <p>Your equipment requests, live stock and collection schedule are all in one place. Reserve ahead
                            and skip the department queue.</p>
                        <div class="welcome-actions"><button class="button button-primary" type="button"
                                data-view="equipment"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book equipment</button><button class="button button-outline" type="button"
                                data-view="availability"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                                </svg> Check a time slot</button></div>
                    </div>
                    <div class="welcome-side"><span class="welcome-side-label"><svg class="icon " viewBox="0 0 24 24"
                                aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg> Next reservation</span><strong>Portable PA Audio System</strong>
                        <p>Tuesday, 10 Nov 2026<br>1:00 PM – 2:00 PM</p>
                        <div class="mini-meta">AVB-261110-0003 · Pending</div>
                    </div>
                </section>
                <section class="stats-grid">
                    <article class="stat-card">
                        <div class="stat-card-top"><span class="stat-icon blue"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                    <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                </svg></span><span class="stat-trend">Lifetime</span></div>
                        <div class="stat-value">7</div>
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-footer">All requests in your history</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-top"><span class="stat-icon yellow"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M12 7v5l3 2"></path>
                                </svg></span><span class="stat-trend">Needs review</span></div>
                        <div class="stat-value">1</div>
                        <div class="stat-label">Pending Requests</div>
                        <div class="stat-footer">Awaiting department review</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-top"><span class="stat-icon green"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m8 12 3 3 5-6"></path>
                                </svg></span><span class="stat-trend">Ready</span></div>
                        <div class="stat-value">0</div>
                        <div class="stat-label">Approved Bookings</div>
                        <div class="stat-footer">Confirmed reservations</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-top"><span class="stat-icon purple"><svg class="icon " viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path>
                                </svg></span><span class="stat-trend">Returned</span></div>
                        <div class="stat-value">2</div>
                        <div class="stat-label">Completed Bookings</div>
                        <div class="stat-footer">Equipment successfully returned</div>
                    </article>
                </section>
                <section class="charts-grid">
                    <article class="card chart-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Booking volume</h2>
                                <p>Your requests grouped by equipment category</p>
                            </div>
                            <div class="chart-modes" role="group" aria-label="Booking volume data view"><button
                                    class="chart-mode is-active" type="button" data-action="chart-mode" data-mode="category"
                                    data-target="student" aria-pressed="true">By equipment</button><button
                                    class="chart-mode" type="button" data-action="chart-mode" data-mode="month"
                                    data-target="student" aria-pressed="false">By month</button></div>
                        </div>
                        <div class="card-body">
                            <div class="chart-canvas"><svg class="chart-svg bar-chart is-rendered" viewBox="0 0 560 250"
                                    role="img" aria-label="Bar chart of your bookings by equipment category">
                                    <line class="chart-gridline" x1="38" y1="208" x2="546" y2="208"></line><text
                                        class="chart-axis-label" x="29" y="212" text-anchor="end">0</text>
                                    <line class="chart-gridline" x1="38" y1="162" x2="546" y2="162"></line><text
                                        class="chart-axis-label" x="29" y="166" text-anchor="end">1</text>
                                    <line class="chart-gridline" x1="38" y1="116" x2="546" y2="116"></line><text
                                        class="chart-axis-label" x="29" y="120" text-anchor="end">2</text>
                                    <line class="chart-gridline" x1="38" y1="70" x2="546" y2="70"></line><text
                                        class="chart-axis-label" x="29" y="74" text-anchor="end">3</text>
                                    <line class="chart-gridline" x1="38" y1="24" x2="546" y2="24"></line><text
                                        class="chart-axis-label" x="29" y="28" text-anchor="end">4</text>
                                    <line class="chart-baseline" x1="38" y1="208" x2="546" y2="208"></line>
                                    <g class="bar-group"
                                        data-tip="&lt;strong&gt;Projectors&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;2 bookings&lt;/span&gt;&lt;span&gt;29% of 7 total&lt;/span&gt;"
                                        style="--bar-color: var(--primary)">
                                        <rect class="bar-hit" x="38" y="24" width="127" height="184"></rect>
                                        <rect class="bar-rect" x="79.5" y="116" width="44" height="92" rx="7"
                                            fill="var(--primary)" style="transition-delay:0ms"></rect><text
                                            class="bar-value" x="101.5" y="107" text-anchor="middle"
                                            style="transition-delay:280ms">2</text><text class="chart-axis-label bar-label"
                                            x="101.5" y="234" text-anchor="middle">Projectors</text>
                                    </g>
                                    <g class="bar-group"
                                        data-tip="&lt;strong&gt;HDMI Cables&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;1 bookings&lt;/span&gt;&lt;span&gt;14% of 7 total&lt;/span&gt;"
                                        style="--bar-color: var(--accent)">
                                        <rect class="bar-hit" x="165" y="24" width="127" height="184"></rect>
                                        <rect class="bar-rect" x="206.5" y="162" width="44" height="46" rx="7"
                                            fill="var(--accent)" style="transition-delay:70ms"></rect><text
                                            class="bar-value" x="228.5" y="153" text-anchor="middle"
                                            style="transition-delay:350ms">1</text><text class="chart-axis-label bar-label"
                                            x="228.5" y="234" text-anchor="middle">HDMI</text>
                                    </g>
                                    <g class="bar-group"
                                        data-tip="&lt;strong&gt;Extension Boards&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;2 bookings&lt;/span&gt;&lt;span&gt;29% of 7 total&lt;/span&gt;"
                                        style="--bar-color: var(--purple)">
                                        <rect class="bar-hit" x="292" y="24" width="127" height="184"></rect>
                                        <rect class="bar-rect" x="333.5" y="116" width="44" height="92" rx="7"
                                            fill="var(--purple)" style="transition-delay:140ms"></rect><text
                                            class="bar-value" x="355.5" y="107" text-anchor="middle"
                                            style="transition-delay:420ms">2</text><text class="chart-axis-label bar-label"
                                            x="355.5" y="234" text-anchor="middle">Power</text>
                                    </g>
                                    <g class="bar-group"
                                        data-tip="&lt;strong&gt;Audio Systems&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;2 bookings&lt;/span&gt;&lt;span&gt;29% of 7 total&lt;/span&gt;"
                                        style="--bar-color: var(--info)">
                                        <rect class="bar-hit" x="419" y="24" width="127" height="184"></rect>
                                        <rect class="bar-rect" x="460.5" y="116" width="44" height="92" rx="7"
                                            fill="var(--info)" style="transition-delay:210ms"></rect><text class="bar-value"
                                            x="482.5" y="107" text-anchor="middle"
                                            style="transition-delay:490ms">2</text><text class="chart-axis-label bar-label"
                                            x="482.5" y="234" text-anchor="middle">Audio</text>
                                    </g>
                                </svg></div>
                            <p class="chart-insight"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                </svg><span>Projectors leads with 2 bookings, which is 29% of the total.</span></p>
                        </div>
                    </article>
                    <article class="card chart-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Request outcomes</h2>
                                <p>Your bookings by current status</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-canvas donut-canvas"><svg class="chart-svg donut-chart is-rendered"
                                    viewBox="0 0 236 236" role="img" aria-label="Donut chart of your bookings by status">
                                    <circle class="donut-track" cx="118" cy="118" r="82" fill="none" stroke-width="30">
                                    </circle>
                                    <g transform="rotate(-90 118 118)">
                                        <circle class="donut-segment" data-chart-seg="seg-0" cx="118" cy="118" r="82"
                                            fill="none" stroke="var(--warning)" stroke-width="30"
                                            stroke-dasharray="0 515.221195188726"
                                            data-dash="71.10302788410371 444.1181673046223" stroke-dashoffset="0"
                                            data-tip="&lt;strong&gt;Pending&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;1 bookings&lt;/span&gt;&lt;span&gt;14% of 7&lt;/span&gt;"
                                            style="transition-delay: 0ms; stroke-dasharray: 71.103, 444.118;"></circle>
                                        <circle class="donut-segment" data-chart-seg="seg-1" cx="118" cy="118" r="82"
                                            fill="none" stroke="var(--danger)" stroke-width="30"
                                            stroke-dasharray="0 515.221195188726"
                                            data-dash="71.10302788410371 444.1181673046223"
                                            stroke-dashoffset="-73.60302788410371"
                                            data-tip="&lt;strong&gt;Rejected&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;1 bookings&lt;/span&gt;&lt;span&gt;14% of 7&lt;/span&gt;"
                                            style="transition-delay: 90ms; stroke-dasharray: 71.103, 444.118;"></circle>
                                        <circle class="donut-segment" data-chart-seg="seg-2" cx="118" cy="118" r="82"
                                            fill="none" stroke="var(--muted-light)" stroke-width="30"
                                            stroke-dasharray="0 515.221195188726"
                                            data-dash="71.10302788410371 444.1181673046223"
                                            stroke-dashoffset="-147.20605576820742"
                                            data-tip="&lt;strong&gt;Cancelled&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;1 bookings&lt;/span&gt;&lt;span&gt;14% of 7&lt;/span&gt;"
                                            style="transition-delay: 180ms; stroke-dasharray: 71.103, 444.118;"></circle>
                                        <circle class="donut-segment" data-chart-seg="seg-3" cx="118" cy="118" r="82"
                                            fill="none" stroke="var(--primary)" stroke-width="30"
                                            stroke-dasharray="0 515.221195188726"
                                            data-dash="144.70605576820742 370.5151394205186"
                                            stroke-dashoffset="-220.80908365231113"
                                            data-tip="&lt;strong&gt;Completed&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;2 bookings&lt;/span&gt;&lt;span&gt;29% of 7&lt;/span&gt;"
                                            style="transition-delay: 270ms; stroke-dasharray: 144.706, 370.515;"></circle>
                                        <circle class="donut-segment" data-chart-seg="seg-4" cx="118" cy="118" r="82"
                                            fill="none" stroke="var(--purple)" stroke-width="30"
                                            stroke-dasharray="0 515.221195188726"
                                            data-dash="144.70605576820742 370.5151394205186"
                                            stroke-dashoffset="-368.01513942051855"
                                            data-tip="&lt;strong&gt;Expired&lt;/strong&gt;&lt;span class=&quot;tip-value&quot;&gt;2 bookings&lt;/span&gt;&lt;span&gt;29% of 7&lt;/span&gt;"
                                            style="transition-delay: 360ms; stroke-dasharray: 144.706, 370.515;"></circle>
                                    </g><text class="donut-total" x="118" y="116" text-anchor="middle">7</text><text
                                        class="donut-caption" x="118" y="136" text-anchor="middle">Requests</text>
                                </svg>
                                <ul class="chart-legend">
                                    <li class="legend-row" data-legend="seg-0"><span class="legend-swatch"
                                            style="background:var(--warning)"></span><span
                                            class="legend-label">Pending</span><span class="legend-value">1</span><span
                                            class="legend-pct">14%</span></li>
                                    <li class="legend-row" data-legend="seg-1"><span class="legend-swatch"
                                            style="background:var(--danger)"></span><span
                                            class="legend-label">Rejected</span><span class="legend-value">1</span><span
                                            class="legend-pct">14%</span></li>
                                    <li class="legend-row" data-legend="seg-2"><span class="legend-swatch"
                                            style="background:var(--muted-light)"></span><span
                                            class="legend-label">Cancelled</span><span class="legend-value">1</span><span
                                            class="legend-pct">14%</span></li>
                                    <li class="legend-row" data-legend="seg-3"><span class="legend-swatch"
                                            style="background:var(--primary)"></span><span
                                            class="legend-label">Completed</span><span class="legend-value">2</span><span
                                            class="legend-pct">29%</span></li>
                                    <li class="legend-row" data-legend="seg-4"><span class="legend-swatch"
                                            style="background:var(--purple)"></span><span
                                            class="legend-label">Expired</span><span class="legend-value">2</span><span
                                            class="legend-pct">29%</span></li>
                                </ul>
                            </div>
                            <p class="chart-insight"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                    <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                </svg><span>Completed leads with 2 bookings, which is 29% of the total.</span></p>
                        </div>
                    </article>
                </section>
                <div class="dashboard-grid">
                    <section class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Recent booking activity</h2>
                                <p>Your latest requests and status changes</p>
                            </div><button class="text-button" type="button" data-view="my-bookings">View all
                                bookings</button>
                        </div>
                        <div class="info-list">
                            <div class="info-list-item"><span class="activity-symbol "><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                        <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                    </svg></span>
                                <div class="info-list-copy"><strong>Epson EB-2265U Projector · 2 unit(s)</strong>
                                    <p>Final-year project proposal rehearsal</p><small>AVB-261107-0001 · 7 Nov 2026 · 9:00
                                        AM – 10:00 AM</small>
                                </div>
                                <div class="info-list-meta"><span class="status-badge status-expired">Expired</span><small
                                        style="display:block;margin-top:5px;color:var(--muted-light);font-size:8px">yesterday</small>
                                </div>
                            </div>
                            <div class="info-list-item"><span class="activity-symbol "><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                        <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                    </svg></span>
                                <div class="info-list-copy"><strong>High-Speed HDMI Cable (3m) · 3 unit(s)</strong>
                                    <p>Engineering society Arduino workshop</p><small>AVB-261106-0002 · 6 Nov 2026 · 11:00
                                        AM – 12:00 PM</small>
                                </div>
                                <div class="info-list-meta"><span class="status-badge status-expired">Expired</span><small
                                        style="display:block;margin-top:5px;color:var(--muted-light);font-size:8px">yesterday</small>
                                </div>
                            </div>
                            <div class="info-list-item"><span class="activity-symbol warning"><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                        <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                    </svg></span>
                                <div class="info-list-copy"><strong>Portable PA Audio System · 1 unit(s)</strong>
                                    <p>Debate club public speaking practice</p><small>AVB-261110-0003 · 10 Nov 2026 · 1:00
                                        PM – 2:00 PM</small>
                                </div>
                                <div class="info-list-meta"><span class="status-badge status-pending">Pending</span><small
                                        style="display:block;margin-top:5px;color:var(--muted-light);font-size:8px">3 days
                                        ago</small></div>
                            </div>
                            <div class="info-list-item"><span class="activity-symbol "><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                        <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                    </svg></span>
                                <div class="info-list-copy"><strong>8-Way Surge Extension Board · 2 unit(s)</strong>
                                    <p>Design portfolio display</p><small>AVB-261108-0004 · 8 Nov 2026 · 10:00 AM – 11:00
                                        AM</small>
                                </div>
                                <div class="info-list-meta"><span
                                        class="status-badge status-cancelled">Cancelled</span><small
                                        style="display:block;margin-top:5px;color:var(--muted-light);font-size:8px">4 days
                                        ago</small></div>
                            </div>
                            <div class="info-list-item"><span class="activity-symbol danger"><svg class="icon "
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M18 6 6 18M6 6l12 12"></path>
                                    </svg></span>
                                <div class="info-list-copy"><strong>Portable PA Audio System · 1 unit(s)</strong>
                                    <p>Outdoor club social</p><small>AVB-261102-0006 · 2 Nov 2026 · 12:00 PM – 1:00
                                        PM</small>
                                </div>
                                <div class="info-list-meta"><span class="status-badge status-rejected">Rejected</span><small
                                        style="display:block;margin-top:5px;color:var(--muted-light);font-size:8px">9 days
                                        ago</small></div>
                            </div>
                        </div>
                    </section>
                    <div class="dashboard-stack">
                        <section class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Next-slot capacity</h2>
                                    <p>9 Nov 2026 · 8:00 AM – 9:00 AM</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="capacity-list">
                                    <div>
                                        <div class="capacity-item-top"><strong>Epson EB-2265U Projector</strong><span>0/5
                                                reserved</span></div>
                                        <div class="progress-track">
                                            <div class="progress-bar " style="width:0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="capacity-item-top"><strong>High-Speed HDMI Cable (3m)</strong><span>0/18
                                                reserved</span></div>
                                        <div class="progress-track">
                                            <div class="progress-bar " style="width:0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="capacity-item-top"><strong>8-Way Surge Extension Board</strong><span>0/8
                                                reserved</span></div>
                                        <div class="progress-track">
                                            <div class="progress-bar " style="width:0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="capacity-item-top"><strong>Portable PA Audio System</strong><span>0/3
                                                reserved</span></div>
                                        <div class="progress-track">
                                            <div class="progress-bar " style="width:0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Booking essentials</h2>
                                    <p>Simple rules that keep stock moving</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="rules-list">
                                    <li><span>1</span>
                                        <div><strong>Reserve the exact slot.</strong> Stock in another hour does not affect
                                            your booking.</div>
                                    </li>
                                    <li><span>2</span>
                                        <div><strong>Pending holds stock.</strong> Staff approval keeps the same units
                                            reserved.</div>
                                    </li>
                                    <li><span>3</span>
                                        <div><strong>Cancel before start.</strong> Release equipment you no longer need.
                                        </div>
                                    </li>
                                    <li><span>4</span>
                                        <div><strong>Return at slot end.</strong> Staff record the return and close your
                                            booking.</div>
                                    </li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
        <section class="app-view is-entering" id="view-equipment" aria-label="Browse equipment" hidden="">
            <div id="equipment-content">
                <div class="section-heading">
                    <div>
                        <h2>Equipment catalogue</h2>
                        <p>Availability below is live for <strong>9 Nov 2026</strong>, <strong>8:00 AM – 9:00 AM</strong>.
                        </p>
                    </div>
                    <div class="section-actions"><button class="button button-outline" type="button"
                            data-view="availability"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg> Compare all slots</button></div>
                </div>
                <div class="filter-bar">
                    <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-4-4"></path>
                            </svg></span><input id="equipment-search" type="search" value=""
                            placeholder="Search equipment name or feature" aria-label="Search equipment"></div>
                    <div class="control-group"><label for="equipment-date">Availability date</label><input
                            id="equipment-date" type="date" min="2026-11-08" value="2026-11-09"></div>
                    <div class="control-group"><label for="equipment-slot">Time slot</label><select id="equipment-slot">
                            <option value="slot-1" selected="">8:00 AM – 9:00 AM</option>
                            <option value="slot-2">9:00 AM – 10:00 AM</option>
                            <option value="slot-3">10:00 AM – 11:00 AM</option>
                            <option value="slot-4">11:00 AM – 12:00 PM</option>
                            <option value="slot-5">12:00 PM – 1:00 PM</option>
                            <option value="slot-6">1:00 PM – 2:00 PM</option>
                            <option value="slot-7">2:00 PM – 3:00 PM</option>
                            <option value="slot-8">3:00 PM – 4:00 PM</option>
                            <option value="slot-9">4:00 PM – 5:00 PM</option>
                        </select></div>
                </div>
                <div class="category-tabs" aria-label="Equipment categories"><button class="category-tab is-active"
                        type="button" data-equipment-category="All">All Equipment<span>4</span></button><button
                        class="category-tab " type="button"
                        data-equipment-category="Projectors">Projectors<span>1</span></button><button class="category-tab "
                        type="button" data-equipment-category="HDMI Cables">HDMI Cables<span>1</span></button><button
                        class="category-tab " type="button" data-equipment-category="Extension Boards">Extension
                        Boards<span>1</span></button><button class="category-tab " type="button"
                        data-equipment-category="Audio Systems">Audio Systems<span>1</span></button></div>
                <div class="results-meta"><span>Showing 4 of 4 equipment type(s)</span><span>Pending and approved bookings
                        hold stock</span></div>
                <div class="equipment-grid">
                    <article class="equipment-card">
                        <div class="equipment-visual projector"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                <circle cx="15" cy="12.5" r="2.5"></circle>
                                <path d="M7 11h3M7 14h2M6 18v2M18 18v2M6 7V5h5v2"></path>
                            </svg><span class="visual-category">Projectors</span><span
                                class="status-badge status-available">Available</span></div>
                        <div class="equipment-card-body">
                            <h3>Epson EB-2265U Projector</h3>
                            <p class="equipment-description">Bright 5,500-lumen projector with wireless display and HDMI
                                connectivity for lectures and presentations.</p>
                            <div class="condition-line"><span>Condition</span><span
                                    class="condition-badge status-good">Good</span></div>
                            <div class="stock-row">
                                <div class="stock-item"><small>Total quantity</small><strong>5 <span>units</span></strong>
                                </div>
                                <div class="stock-item available"><small>Available this slot</small><strong>5 <span>of
                                            5</span></strong></div>
                            </div><button class="button button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book Equipment</button>
                        </div>
                    </article>
                    <article class="equipment-card">
                        <div class="equipment-visual cable"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8 3v5M5 3v5M4 8h5v3a3 3 0 0 1-3 3H5a3 3 0 0 0-3 3v4M16 21v-5M19 21v-5M15 16h5v-3a3 3 0 0 1 3-3h0a3 3 0 0 0 3-3V3"
                                    transform="translate(-2 0) scale(.95)"></path>
                            </svg><span class="visual-category">HDMI Cables</span><span
                                class="status-badge status-available">Available</span></div>
                        <div class="equipment-card-body">
                            <h3>High-Speed HDMI Cable (3m)</h3>
                            <p class="equipment-description">Durable HDMI 2.0 cable supporting 4K video and audio between
                                laptops, projectors and displays.</p>
                            <div class="condition-line"><span>Condition</span><span
                                    class="condition-badge status-good">Good</span></div>
                            <div class="stock-row">
                                <div class="stock-item"><small>Total quantity</small><strong>18 <span>units</span></strong>
                                </div>
                                <div class="stock-item available"><small>Available this slot</small><strong>18 <span>of
                                            18</span></strong></div>
                            </div><button class="button button-primary" type="button" data-action="open-booking"
                                data-id="eq-hdmi"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book Equipment</button>
                        </div>
                    </article>
                    <article class="equipment-card">
                        <div class="equipment-visual power"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="2" y="8" width="20" height="8" rx="2"></rect>
                                <path d="M6 11v2M10 11v2M14 11v2M18 11v2M12 8V5a3 3 0 0 1 3-3h2"></path>
                            </svg><span class="visual-category">Extension Boards</span><span
                                class="status-badge status-available">Available</span></div>
                        <div class="equipment-card-body">
                            <h3>8-Way Surge Extension Board</h3>
                            <p class="equipment-description">Surge-protected eight-way power board with a 5-metre lead for
                                classroom and event setups.</p>
                            <div class="condition-line"><span>Condition</span><span
                                    class="condition-badge status-good">Good</span></div>
                            <div class="stock-row">
                                <div class="stock-item"><small>Total quantity</small><strong>8 <span>units</span></strong>
                                </div>
                                <div class="stock-item available"><small>Available this slot</small><strong>8 <span>of
                                            8</span></strong></div>
                            </div><button class="button button-primary" type="button" data-action="open-booking"
                                data-id="eq-extension"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book Equipment</button>
                        </div>
                    </article>
                    <article class="equipment-card">
                        <div class="equipment-visual audio"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="2" width="14" height="20" rx="3"></rect>
                                <circle cx="12" cy="15" r="4"></circle>
                                <circle cx="12" cy="7" r="2"></circle>
                                <path d="M9 22h6"></path>
                            </svg><span class="visual-category">Audio Systems</span><span
                                class="status-badge status-available">Available</span></div>
                        <div class="equipment-card-body">
                            <h3>Portable PA Audio System</h3>
                            <p class="equipment-description">Rechargeable PA speaker with wireless microphone, Bluetooth
                                input and adjustable tripod stand.</p>
                            <div class="condition-line"><span>Condition</span><span
                                    class="condition-badge status-good">Good</span></div>
                            <div class="stock-row">
                                <div class="stock-item"><small>Total quantity</small><strong>3 <span>units</span></strong>
                                </div>
                                <div class="stock-item available"><small>Available this slot</small><strong>3 <span>of
                                            3</span></strong></div>
                            </div><button class="button button-primary" type="button" data-action="open-booking"
                                data-id="eq-audio"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book Equipment</button>
                        </div>
                    </article>
                </div>
            </div>
        </section>
        <section class="app-view is-entering" id="view-availability" aria-label="Equipment availability" hidden="">
            <div id="availability-content">
                <div class="section-heading">
                    <div>
                        <h2>Plan around real capacity</h2>
                        <p>Select a date and equipment type. Each card independently subtracts active reservations.</p>
                    </div>
                    <div class="section-actions"><button class="button button-outline" type="button"
                            data-view="equipment"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="13" rx="2"></rect>
                                <path d="M8 21h8M12 17v4M7 8h10"></path>
                            </svg> Equipment catalogue</button></div>
                </div>
                <div class="filter-bar">
                    <div class="control-group" style="flex:1"><label for="availability-equipment">Equipment to
                            check</label><select id="availability-equipment">
                            <option value="eq-projector" selected="">Epson EB-2265U Projector — Projectors</option>
                            <option value="eq-hdmi">High-Speed HDMI Cable (3m) — HDMI Cables</option>
                            <option value="eq-extension">8-Way Surge Extension Board — Extension Boards</option>
                            <option value="eq-audio">Portable PA Audio System — Audio Systems</option>
                        </select></div>
                    <div class="control-group"><label for="availability-date">Preferred date</label><input
                            id="availability-date" type="date" min="2026-11-08" value="2026-11-09"></div><button
                        class="button button-outline" type="button" data-action="availability-today"><svg class="icon "
                            viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                        </svg> Next available day</button>
                </div>
                <div class="date-strip"><button class="date-strip-nav" type="button" data-action="week-previous"
                        aria-label="Previous week"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg></button><button class="date-strip-day is-selected " type="button"
                        data-action="select-availability-date"
                        data-date="2026-11-09"><span>Mon</span><strong>9</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-10"><span>Tue</span><strong>10</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-11"><span>Wed</span><strong>11</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-12"><span>Thu</span><strong>12</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-13"><span>Fri</span><strong>13</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-14"><span>Sat</span><strong>14</strong><small>Nov</small></button><button
                        class="date-strip-day  " type="button" data-action="select-availability-date"
                        data-date="2026-11-15"><span>Sun</span><strong>15</strong><small>Nov</small></button><button
                        class="date-strip-nav" type="button" data-action="week-next" aria-label="Next week"><svg
                            class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg></button></div>
                <div class="availability-summary">
                    <div class="mini-stat"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg></span>
                        <div><strong>9</strong><small>Bookable slots on selected date</small></div>
                    </div>
                    <div class="mini-stat"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m12 3 9 5-9 5-9-5 9-5Z M3 8v9l9 5 9-5V8M12 13v9"></path>
                            </svg></span>
                        <div><strong>45</strong><small>Total slot-unit availability (not unique stock)</small></div>
                    </div>
                    <div class="mini-stat"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg></span>
                        <div><strong>0</strong><small>Slot-unit reservations across the day</small></div>
                    </div>
                </div>
                <div class="section-heading">
                    <div>
                        <h2>Monday, 9 Nov 2026</h2>
                        <p>Epson EB-2265U Projector · 5 total units · Good</p>
                    </div>
                </div>
                <div class="slot-grid">
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>8:00 AM – 9:00 AM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-1">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>9:00 AM – 10:00 AM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-2">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>10:00 AM – 11:00 AM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-3">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>11:00 AM – 12:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-4">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>12:00 PM – 1:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-5">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>1:00 PM – 2:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-6">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>2:00 PM – 3:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-7">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>3:00 PM – 4:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-8">Book slot</button></div>
                    </article>
                    <article class="slot-card ">
                        <div class="slot-card-top">
                            <div class="slot-time"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 7v5l3 2"></path>
                                    </svg></span><strong>4:00 PM – 5:00 PM</strong></div><span
                                class="status-badge status-available">Available</span>
                        </div>
                        <div class="slot-stock"><strong>5<small> / 5</small></strong><span>0 unit(s) reserved</span></div>
                        <div class="progress-track">
                            <div class="progress-bar " style="width:100%"></div>
                        </div>
                        <div class="slot-card-footer"><span>Ready to request</span><button
                                class="button button-small button-primary" type="button" data-action="open-booking"
                                data-id="eq-projector" data-date="2026-11-09" data-slot="slot-9">Book slot</button></div>
                    </article>
                </div>
            </div>
        </section>
        <section class="app-view" id="view-my-bookings" aria-label="My bookings" hidden="">
            <div id="my-bookings-content"></div>
        </section>
        <section class="app-view is-entering" id="view-requests" aria-label="Booking management" hidden="">
            <div id="requests-content">
                <div class="section-heading">
                    <div>
                        <h2>Student booking requests</h2>
                        <p>Approve, reject and complete reservations with live capacity checks.</p>
                    </div>
                    <div class="section-actions"><button class="button button-outline" type="button"
                            data-action="show-upcoming"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg> Upcoming bookings</button></div>
                </div>
                <div class="filter-bar">
                    <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-4-4"></path>
                            </svg></span><input id="staff-search" type="search" value=""
                            placeholder="Search reference, student, ID or equipment" aria-label="Search all bookings"></div>
                    <div class="control-group"><label for="staff-status">Status</label><select id="staff-status">
                            <option value="All">All statuses</option>
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>Cancelled</option>
                            <option>Completed</option>
                            <option>Expired</option>
                        </select></div>
                    <div class="control-group"><label for="staff-scope">Schedule</label><select id="staff-scope">
                            <option value="all" selected="">All bookings</option>
                            <option value="upcoming">Upcoming active</option>
                            <option value="today">Today</option>
                            <option value="past">Past dates</option>
                        </select></div>
                    <div class="control-group"><label for="staff-date">Specific date</label><input id="staff-date"
                            type="date" value=""></div>
                    <div class="control-group"><label for="staff-sort">Sort records</label><select id="staff-sort">
                            <option value="newest" selected="">Newest submitted</option>
                            <option value="oldest">Oldest submitted</option>
                            <option value="date-asc">Booking date ↑</option>
                            <option value="date-desc">Booking date ↓</option>
                            <option value="student">Student name</option>
                            <option value="status">Status</option>
                        </select></div><button class="button button-outline" type="button"
                        data-action="reset-staff-filters">Reset</button>
                </div>
                <div class="results-meta"><span>14 of 14 booking record(s)</span><span>1 pending · 0 approved</span></div>
                <div class="table-card">
                    <div class="table-wrap">
                        <table class="data-table responsive-table">
                            <thead>
                                <tr>
                                    <th>Booking Ref.</th>
                                    <th>Student / ID</th>
                                    <th>Equipment</th>
                                    <th>Qty.</th>
                                    <th>Booking Date</th>
                                    <th>Time Slot</th>
                                    <th>Purpose</th>
                                    <th>Submission Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-007">AVB-261107-0007</button></td>
                                    <td data-label="Student"><span class="cell-primary">Amina Yusuf</span><span
                                            class="cell-secondary">STU2026014</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Epson EB-2265U
                                            Projector</span><span class="cell-secondary">Projectors</span></td>
                                    <td data-label="Qty.">2</td>
                                    <td data-label="Booking date"><span class="nowrap">7 Nov 2026</span></td>
                                    <td data-label="Time slot">9:00 AM – 10:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Architecture jury presentations">Architecture jury presentations</span>
                                    </td>
                                    <td data-label="Submitted"><span class="nowrap">5 Nov 2026, 14:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-007" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-003">AVB-261110-0003</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Portable PA Audio
                                            System</span><span class="cell-secondary">Audio Systems</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">10 Nov 2026</span></td>
                                    <td data-label="Time slot">1:00 PM – 2:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Debate club public speaking practice">Debate club public speaking
                                            practice</span></td>
                                    <td data-label="Submitted"><span class="nowrap">5 Nov 2026, 10:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-pending">Pending</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-003" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button><button class="icon-button action-approve" type="button"
                                                data-action="approve-booking" data-id="b-003" title="Approve request"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="m5 12 4 4L19 6"></path>
                                                </svg></button><button class="icon-button action-reject" type="button"
                                                data-action="reject-booking" data-id="b-003" title="Reject request"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M18 6 6 18M6 6l12 12"></path>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-002">AVB-261106-0002</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">High-Speed HDMI Cable
                                            (3m)</span><span class="cell-secondary">HDMI Cables</span></td>
                                    <td data-label="Qty.">3</td>
                                    <td data-label="Booking date"><span class="nowrap">6 Nov 2026</span></td>
                                    <td data-label="Time slot">11:00 AM – 12:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Engineering society Arduino workshop">Engineering society Arduino
                                            workshop</span></td>
                                    <td data-label="Submitted"><span class="nowrap">5 Nov 2026, 9:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-002" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-009">AVB-261106-0009</button></td>
                                    <td data-label="Student"><span class="cell-primary">Sarah Mensah</span><span
                                            class="cell-secondary">STU2026032</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">High-Speed HDMI Cable
                                            (3m)</span><span class="cell-secondary">HDMI Cables</span></td>
                                    <td data-label="Qty.">2</td>
                                    <td data-label="Booking date"><span class="nowrap">6 Nov 2026</span></td>
                                    <td data-label="Time slot">11:00 AM – 12:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Business case competition">Business case competition</span></td>
                                    <td data-label="Submitted"><span class="nowrap">5 Nov 2026, 9:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-009" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-001">AVB-261107-0001</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Epson EB-2265U
                                            Projector</span><span class="cell-secondary">Projectors</span></td>
                                    <td data-label="Qty.">2</td>
                                    <td data-label="Booking date"><span class="nowrap">7 Nov 2026</span></td>
                                    <td data-label="Time slot">9:00 AM – 10:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Final-year project proposal rehearsal">Final-year project proposal
                                            rehearsal</span></td>
                                    <td data-label="Submitted"><span class="nowrap">5 Nov 2026, 8:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-001" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-004">AVB-261108-0004</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">8-Way Surge Extension
                                            Board</span><span class="cell-secondary">Extension Boards</span></td>
                                    <td data-label="Qty.">2</td>
                                    <td data-label="Booking date"><span class="nowrap">8 Nov 2026</span></td>
                                    <td data-label="Time slot">10:00 AM – 11:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Design portfolio display">Design portfolio display</span></td>
                                    <td data-label="Submitted"><span class="nowrap">4 Nov 2026, 11:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-cancelled">Cancelled</span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-004" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-010">AVB-261106-0010</button></td>
                                    <td data-label="Student"><span class="cell-primary">Amina Yusuf</span><span
                                            class="cell-secondary">STU2026014</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">8-Way Surge Extension
                                            Board</span><span class="cell-secondary">Extension Boards</span></td>
                                    <td data-label="Qty.">3</td>
                                    <td data-label="Booking date"><span class="nowrap">6 Nov 2026</span></td>
                                    <td data-label="Time slot">8:00 AM – 9:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Photography society lighting workshop">Photography society lighting
                                            workshop</span></td>
                                    <td data-label="Submitted"><span class="nowrap">4 Nov 2026, 10:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-010" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-008">AVB-261107-0008</button></td>
                                    <td data-label="Student"><span class="cell-primary">Brian Otieno</span><span
                                            class="cell-secondary">STU2026027</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Epson EB-2265U
                                            Projector</span><span class="cell-secondary">Projectors</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">7 Nov 2026</span></td>
                                    <td data-label="Time slot">9:00 AM – 10:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Computer science symposium">Computer science symposium</span></td>
                                    <td data-label="Submitted"><span class="nowrap">4 Nov 2026, 8:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-008" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-013">AVB-261109-0013</button></td>
                                    <td data-label="Student"><span class="cell-primary">Brian Otieno</span><span
                                            class="cell-secondary">STU2026027</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">High-Speed HDMI Cable
                                            (3m)</span><span class="cell-secondary">HDMI Cables</span></td>
                                    <td data-label="Qty.">4</td>
                                    <td data-label="Booking date"><span class="nowrap">9 Nov 2026</span></td>
                                    <td data-label="Time slot">2:00 PM – 3:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Gaming society tournament">Gaming society tournament</span></td>
                                    <td data-label="Submitted"><span class="nowrap">3 Nov 2026, 13:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-rejected">Rejected</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-013" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-012">AVB-261104-0012</button></td>
                                    <td data-label="Student"><span class="cell-primary">Sarah Mensah</span><span
                                            class="cell-secondary">STU2026032</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Epson EB-2265U
                                            Projector</span><span class="cell-secondary">Projectors</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">4 Nov 2026</span></td>
                                    <td data-label="Time slot">9:00 AM – 10:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Marketing research group meeting">Marketing research group meeting</span>
                                    </td>
                                    <td data-label="Submitted"><span class="nowrap">1 Nov 2026, 12:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-expired">Expired</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-012" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-006">AVB-261102-0006</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Portable PA Audio
                                            System</span><span class="cell-secondary">Audio Systems</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">2 Nov 2026</span></td>
                                    <td data-label="Time slot">12:00 PM – 1:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell" title="Outdoor club social">Outdoor
                                            club social</span></td>
                                    <td data-label="Submitted"><span class="nowrap">30 Oct 2026, 13:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-rejected">Rejected</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-006" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-005">AVB-261029-0005</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Epson EB-2265U
                                            Projector</span><span class="cell-secondary">Projectors</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">29 Oct 2026</span></td>
                                    <td data-label="Time slot">8:00 AM – 9:00 AM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Multimedia systems practical">Multimedia systems practical</span></td>
                                    <td data-label="Submitted"><span class="nowrap">26 Oct 2026, 12:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-completed">Completed</span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-005" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-011">AVB-261022-0011</button></td>
                                    <td data-label="Student"><span class="cell-primary">Brian Otieno</span><span
                                            class="cell-secondary">STU2026027</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">Portable PA Audio
                                            System</span><span class="cell-secondary">Audio Systems</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">22 Oct 2026</span></td>
                                    <td data-label="Time slot">2:00 PM – 3:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Robotics demonstration day">Robotics demonstration day</span></td>
                                    <td data-label="Submitted"><span class="nowrap">18 Oct 2026, 11:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-completed">Completed</span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-011" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Reference"><button class="reference-button" type="button"
                                            data-action="view-booking" data-id="b-014">AVB-261016-0014</button></td>
                                    <td data-label="Student"><span class="cell-primary">Demo Student</span><span
                                            class="cell-secondary">STU2026001</span></td>
                                    <td data-label="Equipment"><span class="cell-primary">8-Way Surge Extension
                                            Board</span><span class="cell-secondary">Extension Boards</span></td>
                                    <td data-label="Qty.">1</td>
                                    <td data-label="Booking date"><span class="nowrap">16 Oct 2026</span></td>
                                    <td data-label="Time slot">3:00 PM – 4:00 PM</td>
                                    <td data-label="Purpose"><span class="purpose-cell"
                                            title="Research methods lecture capture">Research methods lecture capture</span>
                                    </td>
                                    <td data-label="Submitted"><span class="nowrap">13 Oct 2026, 14:12</span></td>
                                    <td data-label="Status"><span class="status-badge status-completed">Completed</span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="view-booking" data-id="b-014" title="View details"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section class="app-view is-entering" id="view-inventory" aria-label="Inventory management" hidden="">
            <div id="inventory-content">
                <div class="section-heading">
                    <div>
                        <h2>Equipment inventory</h2>
                        <p>Stock figures below are calculated for <strong>9 Nov 2026</strong>, <strong>8:00 AM – 9:00
                                AM</strong>.</p>
                    </div>
                    <div class="section-actions"><button class="button button-primary" type="button"
                            data-action="add-equipment"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg> Add equipment</button></div>
                </div>
                <div class="alert"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 11v6M12 7h.01"></path>
                    </svg>
                    <div><strong>Reservation-safe stock controls</strong>
                        <p>Quantity cannot be reduced below the highest active reservation in any single slot. Maintenance
                            and damaged conditions prevent new bookings.</p>
                    </div>
                </div>
                <div class="filter-bar">
                    <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-4-4"></path>
                            </svg></span><input id="inventory-search" type="search" value=""
                            placeholder="Search equipment inventory" aria-label="Search inventory"></div>
                    <div class="control-group"><label for="inventory-category">Category</label><select
                            id="inventory-category">
                            <option value="All">All categories</option>
                            <option value="Projectors">Projectors</option>
                            <option value="HDMI Cables">HDMI Cables</option>
                            <option value="Extension Boards">Extension Boards</option>
                            <option value="Audio Systems">Audio Systems</option>
                        </select></div>
                    <div class="control-group"><label for="inventory-condition">Condition</label><select
                            id="inventory-condition">
                            <option value="All">All conditions</option>
                            <option>Good</option>
                            <option>Under Maintenance</option>
                            <option>Damaged</option>
                        </select></div>
                    <div class="control-group"><label for="inventory-date">Stock date</label><input id="inventory-date"
                            type="date" value="2026-11-09"></div>
                    <div class="control-group"><label for="inventory-slot">Stock time</label><select id="inventory-slot">
                            <option value="slot-1" selected="">8:00 AM – 9:00 AM</option>
                            <option value="slot-2">9:00 AM – 10:00 AM</option>
                            <option value="slot-3">10:00 AM – 11:00 AM</option>
                            <option value="slot-4">11:00 AM – 12:00 PM</option>
                            <option value="slot-5">12:00 PM – 1:00 PM</option>
                            <option value="slot-6">1:00 PM – 2:00 PM</option>
                            <option value="slot-7">2:00 PM – 3:00 PM</option>
                            <option value="slot-8">3:00 PM – 4:00 PM</option>
                            <option value="slot-9">4:00 PM – 5:00 PM</option>
                        </select></div><button class="button button-outline" type="button"
                        data-action="reset-inventory-filters">Reset</button>
                </div>
                <div class="results-meta"><span>4 equipment type(s) · 34 total units shown</span><span>Reserved = Pending +
                        Approved for selected slot</span></div>
                <div class="table-card">
                    <div class="table-wrap">
                        <table class="data-table responsive-table">
                            <thead>
                                <tr>
                                    <th>Equipment Name</th>
                                    <th>Category</th>
                                    <th>Total Quantity</th>
                                    <th>Currently Reserved</th>
                                    <th>Available Quantity</th>
                                    <th>Condition</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Equipment">
                                        <div class="inventory-name"><span class="inventory-icon"><svg class="icon "
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                                    <circle cx="15" cy="12.5" r="2.5"></circle>
                                                    <path d="M7 11h3M7 14h2M6 18v2M18 18v2M6 7V5h5v2"></path>
                                                </svg></span><span><span class="cell-primary">Epson EB-2265U
                                                    Projector</span><span class="cell-secondary truncate">Bright 5,500-lumen
                                                    projector with wireless display and HDMI connectivity for lectures and
                                                    presentations.</span></span></div>
                                    </td>
                                    <td data-label="Category"><span class="category-badge">Projectors</span></td>
                                    <td data-label="Total quantity"><strong>5</strong> units</td>
                                    <td data-label="Currently reserved"><strong class="">0</strong> units</td>
                                    <td data-label="Available"><strong class="text-success">5</strong> units</td>
                                    <td data-label="Condition"><span class="condition-badge status-good">Good</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="equipment-availability" data-id="eq-projector"
                                                title="View all slot availability"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button><button class="icon-button" type="button"
                                                data-action="edit-equipment" data-id="eq-projector"
                                                title="Edit equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                                </svg></button><button class="icon-button action-delete" type="button"
                                                data-action="delete-equipment" data-id="eq-projector"
                                                title="Remove equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Equipment">
                                        <div class="inventory-name"><span class="inventory-icon"><svg class="icon "
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path
                                                        d="M8 3v5M5 3v5M4 8h5v3a3 3 0 0 1-3 3H5a3 3 0 0 0-3 3v4M16 21v-5M19 21v-5M15 16h5v-3a3 3 0 0 1 3-3h0a3 3 0 0 0 3-3V3"
                                                        transform="translate(-2 0) scale(.95)"></path>
                                                </svg></span><span><span class="cell-primary">High-Speed HDMI Cable
                                                    (3m)</span><span class="cell-secondary truncate">Durable HDMI 2.0 cable
                                                    supporting 4K video and audio between laptops, projectors and
                                                    displays.</span></span></div>
                                    </td>
                                    <td data-label="Category"><span class="category-badge">HDMI Cables</span></td>
                                    <td data-label="Total quantity"><strong>18</strong> units</td>
                                    <td data-label="Currently reserved"><strong class="">0</strong> units</td>
                                    <td data-label="Available"><strong class="text-success">18</strong> units</td>
                                    <td data-label="Condition"><span class="condition-badge status-good">Good</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="equipment-availability" data-id="eq-hdmi"
                                                title="View all slot availability"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button><button class="icon-button" type="button"
                                                data-action="edit-equipment" data-id="eq-hdmi" title="Edit equipment"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                                </svg></button><button class="icon-button action-delete" type="button"
                                                data-action="delete-equipment" data-id="eq-hdmi"
                                                title="Remove equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Equipment">
                                        <div class="inventory-name"><span class="inventory-icon"><svg class="icon "
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <rect x="2" y="8" width="20" height="8" rx="2"></rect>
                                                    <path d="M6 11v2M10 11v2M14 11v2M18 11v2M12 8V5a3 3 0 0 1 3-3h2"></path>
                                                </svg></span><span><span class="cell-primary">8-Way Surge Extension
                                                    Board</span><span class="cell-secondary truncate">Surge-protected
                                                    eight-way power board with a 5-metre lead for classroom and event
                                                    setups.</span></span></div>
                                    </td>
                                    <td data-label="Category"><span class="category-badge">Extension Boards</span></td>
                                    <td data-label="Total quantity"><strong>8</strong> units</td>
                                    <td data-label="Currently reserved"><strong class="">0</strong> units</td>
                                    <td data-label="Available"><strong class="text-success">8</strong> units</td>
                                    <td data-label="Condition"><span class="condition-badge status-good">Good</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="equipment-availability" data-id="eq-extension"
                                                title="View all slot availability"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button><button class="icon-button" type="button"
                                                data-action="edit-equipment" data-id="eq-extension"
                                                title="Edit equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                                </svg></button><button class="icon-button action-delete" type="button"
                                                data-action="delete-equipment" data-id="eq-extension"
                                                title="Remove equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                                                </svg></button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="Equipment">
                                        <div class="inventory-name"><span class="inventory-icon"><svg class="icon "
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <rect x="5" y="2" width="14" height="20" rx="3"></rect>
                                                    <circle cx="12" cy="15" r="4"></circle>
                                                    <circle cx="12" cy="7" r="2"></circle>
                                                    <path d="M9 22h6"></path>
                                                </svg></span><span><span class="cell-primary">Portable PA Audio
                                                    System</span><span class="cell-secondary truncate">Rechargeable PA
                                                    speaker with wireless microphone, Bluetooth input and adjustable tripod
                                                    stand.</span></span></div>
                                    </td>
                                    <td data-label="Category"><span class="category-badge">Audio Systems</span></td>
                                    <td data-label="Total quantity"><strong>3</strong> units</td>
                                    <td data-label="Currently reserved"><strong class="">0</strong> units</td>
                                    <td data-label="Available"><strong class="text-success">3</strong> units</td>
                                    <td data-label="Condition"><span class="condition-badge status-good">Good</span></td>
                                    <td data-label="Actions">
                                        <div class="table-actions"><button class="icon-button" type="button"
                                                data-action="equipment-availability" data-id="eq-audio"
                                                title="View all slot availability"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg></button><button class="icon-button" type="button"
                                                data-action="edit-equipment" data-id="eq-audio" title="Edit equipment"><svg
                                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                                </svg></button><button class="icon-button action-delete" type="button"
                                                data-action="delete-equipment" data-id="eq-audio"
                                                title="Remove equipment"><svg class="icon " viewBox="0 0 24 24"
                                                    aria-hidden="true">
                                                    <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                                                </svg></button></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section class="app-view" id="view-slots" aria-label="Time slot management" hidden="">
            <div id="slots-content"></div>
        </section>
        <section class="app-view" id="view-usage" aria-label="Usage history" hidden="">
            <div id="usage-content"></div>
        </section>
        <section class="app-view" id="view-activity" aria-label="Activity log" hidden="">
            <div id="activity-content"></div>
        </section>
    </main>

@endsection