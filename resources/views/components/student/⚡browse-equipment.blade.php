<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <div id="equipment-content">
        <div class="section-heading">
            <div>
                <h2>Equipment catalogue</h2>
                <p>Availability below is live for <strong>9 Nov 2026</strong>, <strong>8:00 AM – 9:00 AM</strong>.</p>
            </div>
            <div class="section-actions">
                <a href="{{ route('student.check_availability') }}">
                    <button class="button button-outline" type="button" data-view="availability">
                        <svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                            <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                        </svg> Compare all slots
                    </button>
                </a>
            </div>
        </div>
        <div class="filter-bar">
            <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg></span><input id="equipment-search" type="search" value=""
                    placeholder="Search equipment name or feature" aria-label="Search equipment"></div>
            <div class="control-group"><label for="equipment-date">Availability date</label><input id="equipment-date"
                    type="date" min="2026-11-08" value="2026-11-09"></div>
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
        <div class="results-meta"><span>Showing 4 of 4 equipment type(s)</span><span>Pending and approved bookings hold
                stock</span></div>
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
                        <div class="stock-item"><small>Total quantity</small><strong>5 <span>units</span></strong></div>
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
                        <div class="stock-item"><small>Total quantity</small><strong>8 <span>units</span></strong></div>
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
                    <p class="equipment-description">Rechargeable PA speaker with wireless microphone, Bluetooth input
                        and adjustable tripod stand.</p>
                    <div class="condition-line"><span>Condition</span><span
                            class="condition-badge status-good">Good</span></div>
                    <div class="stock-row">
                        <div class="stock-item"><small>Total quantity</small><strong>3 <span>units</span></strong></div>
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
</div>