<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <section class="app-view is-entering" id="view-activity" aria-label="Activity log">
        <div id="activity-content">
            <div class="section-heading">
                <div>
                    <h2>System activity log</h2>
                    <p>A local audit trail of registrations, bookings, decisions, returns and stock changes.</p>
                </div>
            </div>
            <div class="filter-bar">
                <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg></span><input id="activity-search" type="search" value=""
                        placeholder="Search event, description or user" aria-label="Search activity log"></div>
                <div class="control-group"><label for="activity-type">Activity type</label><select id="activity-type">
                        <option value="All">All activity types</option>
                        <option>Booking approved</option>
                        <option>Booking cancelled</option>
                        <option>Booking completed</option>
                        <option>Booking expired</option>
                        <option>Booking rejected</option>
                        <option>Booking submitted</option>
                        <option>Equipment quantity updated</option>
                        <option>Inventory initialised</option>
                        <option>User signed in</option>
                        <option>User signed out</option>
                    </select></div>
                <div class="control-group"><label for="activity-date">Event date</label><input id="activity-date"
                        type="date" value=""></div><button class="button button-outline" type="button"
                    data-action="reset-activity-filters">Clear filters</button>
            </div>
            <div class="results-meta"><span>41 of 41 audit event(s)</span><span>Most recent activity first · Last 400
                    events retained</span></div>
            <div class="table-card">
                <div class="table-wrap">
                    <table class="data-table responsive-table">
                        <thead>
                            <tr>
                                <th>Activity Type</th>
                                <th>Description</th>
                                <th>Date &amp; Time</th>
                                <th>Relevant User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed in to the staff portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">9 Nov 2026, 4:12</span><span
                                        class="cell-secondary">4 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">9 Nov 2026, 4:12</span><span
                                        class="cell-secondary">4 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed in to the student portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Nov 2026, 18:50</span><span
                                        class="cell-secondary">13 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Nov 2026, 18:50</span><span
                                        class="cell-secondary">13 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Equipment quantity updated</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">High-Speed HDMI Cable (3m): total quantity
                                        changed from 15 to 18.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Nov 2026, 15:55</span><span
                                        class="cell-secondary">16 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed in to the staff portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Nov 2026, 15:18</span><span
                                        class="cell-secondary">17 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0008 expired automatically; 1 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Nov 2026, 15:10</span><span
                                        class="cell-secondary">17 hours ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0009 expired automatically; 2 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">7 Nov 2026, 15:13</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0007 expired automatically; 2 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">7 Nov 2026, 15:13</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0002 expired automatically; 3 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">7 Nov 2026, 15:13</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0001 expired automatically; 2 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">7 Nov 2026, 15:13</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 21:35</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed in to the staff portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 20:20</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 20:20</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed in to the student portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 20:08</span><span
                                        class="cell-secondary">2 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 18:21</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed in to the student portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 18:21</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 18:21</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed in to the staff portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 18:20</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 16:29</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed in to the student portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 16:28</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 16:06</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0010 expired automatically; 3 unit(s)
                                        released.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">6 Nov 2026, 13:10</span><span
                                        class="cell-secondary">3 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">AVBMS System</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Department Staff signed in to the staff portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 18:52</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed out</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed out of AVBMS.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 18:52</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>User signed in</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Demo Student signed in to the student portal.
                                    </p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 18:52</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking completed</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261016-0014 · 1 × 8-Way Surge Extension
                                        Board for Research methods lecture capture</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">13 Oct 2026, 14:12</span><span
                                        class="cell-secondary">27 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking rejected</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261109-0013 · 4 × High-Speed HDMI Cable (3m)
                                        for Gaming society tournament</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">3 Nov 2026, 13:12</span><span
                                        class="cell-secondary">6 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking expired</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261104-0012 · 1 × Epson EB-2265U Projector
                                        for Marketing research group meeting</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">1 Nov 2026, 12:12</span><span
                                        class="cell-secondary">8 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking completed</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261022-0011 · 1 × Portable PA Audio System
                                        for Robotics demonstration day</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">18 Oct 2026, 11:12</span><span
                                        class="cell-secondary">22 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking submitted</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0010 · 3 × 8-Way Surge Extension
                                        Board for Photography society lighting workshop</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">4 Nov 2026, 10:12</span><span
                                        class="cell-secondary">5 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Amina Yusuf</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="m5 12 4 4L19 6"></path>
                                            </svg></span><strong>Booking approved</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0009 · 2 × High-Speed HDMI Cable (3m)
                                        for Business case competition</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 9:12</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="m5 12 4 4L19 6"></path>
                                            </svg></span><strong>Booking approved</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0008 · 1 × Epson EB-2265U Projector
                                        for Computer science symposium</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">4 Nov 2026, 8:12</span><span
                                        class="cell-secondary">5 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking submitted</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0007 · 2 × Epson EB-2265U Projector
                                        for Architecture jury presentations</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 14:12</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Amina Yusuf</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking rejected</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261102-0006 · 1 × Portable PA Audio System
                                        for Outdoor club social</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">30 Oct 2026, 13:12</span><span
                                        class="cell-secondary">10 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking completed</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261029-0005 · 1 × Epson EB-2265U Projector
                                        for Multimedia systems practical</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">26 Oct 2026, 12:12</span><span
                                        class="cell-secondary">14 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                                </path>
                                                <path d="M12 9v4M12 17h.01"></path>
                                            </svg></span><strong>Booking cancelled</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261108-0004 · 2 × 8-Way Surge Extension
                                        Board for Design portfolio display</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">4 Nov 2026, 11:12</span><span
                                        class="cell-secondary">5 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking submitted</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261110-0003 · 1 × Portable PA Audio System
                                        for Debate club public speaking practice</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 10:12</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="m5 12 4 4L19 6"></path>
                                            </svg></span><strong>Booking approved</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261106-0002 · 3 × High-Speed HDMI Cable (3m)
                                        for Engineering society Arduino workshop</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 9:12</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Booking submitted</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">AVB-261107-0001 · 2 × Epson EB-2265U Projector
                                        for Final-year project proposal rehearsal</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">5 Nov 2026, 8:12</span><span
                                        class="cell-secondary">4 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Demo Student</span></td>
                            </tr>
                            <tr>
                                <td data-label="Activity type">
                                    <div class="log-type"><span><svg class="icon " viewBox="0 0 24 24"
                                                aria-hidden="true">
                                                <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                                            </svg></span><strong>Inventory initialised</strong></div>
                                </td>
                                <td data-label="Description">
                                    <p style="margin:0;max-width:520px">Default department inventory created: 5
                                        projectors, 15 HDMI cables, 8 extension boards and 3 audio systems.</p>
                                </td>
                                <td data-label="Date &amp; time"><span class="nowrap">8 Jul 2026, 9:00</span><span
                                        class="cell-secondary">124 days ago</span></td>
                                <td data-label="Relevant user"><span class="cell-primary">Department Staff</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>