<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <section class="app-view is-entering" id="view-requests" aria-label="Booking management">
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

    {{-- View Booking Modal --}}
    <div class="modal-backdrop" id="modal-backdrop">
        <div class="modal modal-large" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div id="modal-content">
                <div class="modal-header">
                    <div class="modal-heading"><span class="modal-heading-icon "><svg class="icon " viewBox="0 0 24 24"
                                aria-hidden="true">
                                <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                            </svg></span>
                        <div>
                            <h2 id="modal-title">Booking details</h2>
                            <p>Complete reservation information and status history</p>
                        </div>
                    </div><button class="icon-button modal-close" type="button" data-close-modal=""
                        aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg></button>
                </div>
                <div class="modal-body">
                    <div class="detail-reference"><code>AVB-261110-0003</code><span
                            class="status-badge status-pending">Pending</span></div>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Student</label>
                            <p>Demo Student<br><span class="muted">STU2026001</span></p>
                        </div>
                        <div class="detail-item"><label>Date submitted</label>
                            <p>5 Nov 2026, 10:12</p>
                        </div>
                        <div class="detail-item"><label>Equipment</label>
                            <p>Portable PA Audio System<br><span class="muted">Audio Systems</span></p>
                        </div>
                        <div class="detail-item"><label>Quantity</label>
                            <p>1 unit(s)</p>
                        </div>
                        <div class="detail-item"><label>Booking date</label>
                            <p>Tuesday, 10 Nov 2026</p>
                        </div>
                        <div class="detail-item"><label>Time slot</label>
                            <p>1:00 PM – 2:00 PM</p>
                        </div>
                        <div class="detail-item full"><label>Purpose of booking</label>
                            <p>Debate club public speaking practice</p>
                        </div>
                        <div class="detail-item full"><label>Additional notes</label>
                            <p>Wireless microphone required.</p>
                        </div>

                    </div>

                    <div class="booking-timeline">
                        <h3>Status history</h3>
                        <div class="timeline-item"><span class="timeline-dot"></span>
                            <div class="timeline-copy"><strong>Pending</strong>
                                <p>Booking request submitted</p><small>5 Nov 2026, 10:12 · Demo Student</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button class="button button-outline" type="button"
                        data-close-modal="">Close</button><button class="button button-danger-soft" type="button"
                        data-action="reject-booking" data-id="b-003"><svg class="icon " viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg> Reject</button><button class="button button-success" type="button"
                        data-action="approve-booking" data-id="b-003"><svg class="icon " viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="m5 12 4 4L19 6"></path>
                        </svg> Approve</button></div>
            </div>
        </div>
    </div>

    {{-- Approve booking modal --}}
    <div class="modal-backdrop" id="modal-backdrop">
        <div class="modal modal-small" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div id="modal-content">
                <div class="modal-header">
                    <div class="modal-heading"><span class="modal-heading-icon success"><svg class="icon "
                                viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 3 3 5-6"></path>
                            </svg></span>
                        <div>
                            <h2 id="modal-title">Approve booking request?</h2>
                            <p>Please review this action before continuing.</p>
                        </div>
                    </div><button class="icon-button modal-close" type="button" data-close-modal=""
                        aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg></button>
                </div>
                <div class="modal-body">
                    <div class="confirm-message">Approve <strong>AVB-261110-0003</strong> for Demo Student? This
                        confirms 1 × Portable PA Audio System on 10 Nov 2026, 1:00 PM – 2:00 PM.</div>
                </div>
                <div class="modal-footer"><button class="button button-outline" type="button" data-close-modal="">Go
                        back</button><button class="button button-success" type="button" id="confirm-action"><svg
                            class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m5 12 4 4L19 6"></path>
                        </svg> Approve request</button></div>
            </div>
        </div>
    </div>



    {{-- Reject Booking Modal --}}
    <div class="modal-backdrop" id="modal-backdrop">
        <div class="modal modal-small" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div id="modal-content">
                <form id="rejection-form" data-booking-id="b-003">
                    <div class="modal-header">
                        <div class="modal-heading"><span class="modal-heading-icon danger"><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="m9 9 6 6M15 9l-6 6"></path>
                                </svg></span>
                            <div>
                                <h2 id="modal-title">Confirm booking rejection</h2>
                                <p>AVB-261110-0003 · Demo Student</p>
                            </div>
                        </div><button class="icon-button modal-close" type="button" data-close-modal=""
                            aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert warning"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M10.3 3.7 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z">
                                </path>
                                <path d="M12 9v4M12 17h.01"></path>
                            </svg>
                            <div><strong>Reserved stock will be released</strong>
                                <p>The student will see your reason, so provide clear and helpful guidance.</p>
                            </div>
                        </div>
                        <div class="detail-grid" style="margin-bottom:18px">
                            <div class="detail-item"><label>Equipment</label>
                                <p>Portable PA Audio System</p>
                            </div>
                            <div class="detail-item"><label>Requested session</label>
                                <p>10 Nov 2026<br>1:00 PM – 2:00 PM</p>
                            </div>
                        </div>
                        <div class="field-group"><label for="rejection-reason">Rejection reason <span
                                    class="required">*</span></label><textarea id="rejection-reason" name="reason"
                                minlength="5" required=""
                                placeholder="e.g. Equipment is required for a scheduled examination. Please select another date."></textarea><span
                                class="field-error" data-error-for="rejection-reason"></span></div>
                    </div>
                    <div class="modal-footer"><button class="button button-outline" type="button"
                            data-close-modal="">Keep pending</button><button class="button button-danger"
                            type="submit"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg> Confirm rejection</button></div>
                </form>
            </div>
        </div>
    </div>

</div>