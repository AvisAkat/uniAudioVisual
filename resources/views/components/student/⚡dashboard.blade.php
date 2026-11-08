<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <section class="app-view is-entering" id="view-overview" aria-label="Dashboard overview">
        <div id="overview-content">
            <section class="welcome-banner">
                <div class="welcome-copy"><span class="eyebrow eyebrow-light">Student workspace</span>
                    <h2>Good evening, Demo.</h2>
                    <p>Your equipment requests, live stock and collection schedule are all in one place. Reserve ahead
                        and skip the department queue.</p>
                    <div class="welcome-actions">
                        <a href="{{ route('student.browse_equipment') }}">
                            <button class="button button-primary" type="button" data-view="equipment"><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg> Book equipment
                            </button>
                        </a>
                        <a href="{{ route('student.check_availability') }}">
                            <button class="button button-outline" type="button" data-view="availability"><svg
                                    class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01">
                                    </path>
                                </svg> Check a time slot
                            </button>
                        </a>
                    </div>
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
</div>