<?php

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $search = '';

    public string $categoryFilter = 'All';

    #[Computed]
    public function equipmentItems(): Collection
    {
        return Equipment::query()
            ->when($this->search !== '', function (Builder $query): void {
                $searchTerm = '%' . $this->search . '%';

                $query->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('name', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhere('asset_code', 'like', $searchTerm);
                });
            })
            ->when($this->categoryFilter !== 'All', fn(Builder $query) => $query->where('category', $this->categoryFilter))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function categoryCounts(): array
    {
        return Equipment::query()
            ->select('category')
            ->selectRaw('COUNT(*) as equipment_count')
            ->groupBy('category')
            ->orderBy('category')
            ->pluck('equipment_count', 'category')
            ->all();
    }
};
?>

<div>
    <div id="equipment-content">
        <div class="section-heading">
            <div>
                <h2>Equipment catalogue</h2>
                <p>Availability below is live for 11 Nov 2026, 3:00 PM – 4:00 PM.</p>
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
                    placeholder="Search equipment name or feature" aria-label="Search equipment"
                    wire:model.live.debounce.300ms="search">
                </div>
                <div class="control-group"><label for="equipment-date">Availability date</label><input id="equipment-date" type="date" min="2026-11-08" value="2026-11-11"></div>
                <div class="control-group"><label for="equipment-slot">Time slot</label><select id="equipment-slot"><option value="slot-1">8:00 AM – 9:00 AM</option><option value="slot-2">9:00 AM – 10:00 AM</option><option value="slot-3">10:00 AM – 11:00 AM</option><option value="slot-4">11:00 AM – 12:00 PM</option><option value="slot-5">12:00 PM – 1:00 PM</option><option value="slot-6">1:00 PM – 2:00 PM</option><option value="slot-7">2:00 PM – 3:00 PM</option><option value="slot-8" selected="">3:00 PM – 4:00 PM</option><option value="slot-9">4:00 PM – 5:00 PM</option></select></div>

        </div>
        <div class="category-tabs" aria-label="Equipment categories">
            <button class="category-tab {{ $categoryFilter === 'All' ? 'is-active' : '' }}" type="button"
                wire:click="$set('categoryFilter', 'All')">All
                Equipment<span>{{ array_sum($this->categoryCounts) }}</span></button>
            @foreach ($this->categoryCounts as $category => $count)
                <button class="category-tab {{ $categoryFilter === $category ? 'is-active' : '' }}" type="button"
                    data-equipment-category="{{ $category }}"
                    wire:click="$set('categoryFilter', $event.currentTarget.dataset.category)">{{ $category }}<span>{{ $count }}</span></button>
            @endforeach
        </div>
        <div class="results-meta">
            <span>Showing {{ $this->equipmentItems->count() }} of {{ array_sum($this->categoryCounts) }} equipment
                type(s)</span>
            <span>Availability reflects current inventory stock</span>
        </div>
        <div class="equipment-grid">
            @forelse ($this->equipmentItems as $equipment)
                @php
                    $visualClass = match ($equipment->category) {
                        'Projectors' => 'projector',
                        'HDMI Cables' => 'cable',
                        'Extension Boards' => 'power',
                        'Audio Systems' => 'audio',
                        default => '',
                    };
                    $isAvailable = $equipment->available_quantity > 0 && $equipment->condition === 'Good';
                @endphp
                <article class="equipment-card" wire:key="student-equipment-{{ $equipment->id }}">
                    <div class="equipment-visual {{ $visualClass }}">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            @switch($equipment->category)
                                @case('Projectors')
                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                    <circle cx="15" cy="12.5" r="2.5"></circle>
                                    <path d="M7 11h3M7 14h2M6 18v2M18 18v2M6 7V5h5v2"></path>
                                    @break
                                @case('HDMI Cables')
                                    <path d="M8 3v5M5 3v5M4 8h5v3a3 3 0 0 1-3 3H5a3 3 0 0 0-3 3v4M16 21v-5M19 21v-5M15 16h5v-3a3 3 0 0 1 3-3h0a3 3 0 0 0 3-3V3" transform="translate(-2 0) scale(.95)"></path>
                                    @break
                                @case('Extension Boards')
                                    <rect x="2" y="8" width="20" height="8" rx="2"></rect>
                                    <path d="M6 11v2M10 11v2M14 11v2M18 11v2M12 8V5a3 3 0 0 1 3-3h2"></path>
                                    @break
                                @case('Audio Systems')
                                    <rect x="5" y="2" width="14" height="20" rx="3"></rect>
                                    <circle cx="12" cy="15" r="4"></circle>
                                    <circle cx="12" cy="7" r="2"></circle>
                                    <path d="M9 22h6"></path>
                                    @break
                                @default
                                    <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                                    <path d="M7 11h10M7 14h6M6 18v2M18 18v2"></path>
                            @endswitch
                        </svg>
                        <span class="visual-category">{{ $equipment->category }}</span>
                        <span class="status-badge {{ $isAvailable ? 'status-available' : 'status-unavailable' }}">
                            {{ $isAvailable ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                    <div class="equipment-card-body">
                        <h3>{{ $equipment->name }}</h3>
                        <p class="equipment-description">{{ $equipment->description }}</p>
                        <div class="condition-line"><span>Condition</span><span
                                class="condition-badge status-{{ \Illuminate\Support\Str::slug($equipment->condition) }}">{{ $equipment->condition }}</span>
                        </div>
                        <div class="stock-row">
                            <div class="stock-item"><small>Total quantity</small><strong>{{ $equipment->total_quantity }}
                                    <span>units</span></strong></div>
                            <div class="stock-item {{ $isAvailable ? 'available' : '' }}"><small>Available
                                    now</small><strong>{{ $equipment->available_quantity }} <span>of
                                        {{ $equipment->total_quantity }}</span></strong></div>
                        </div>
                        <button class="button button-primary" type="button" data-action="open-booking"
                            data-id="{{ $equipment->id }}" @disabled(!$isAvailable)>
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg> Book Equipment
                        </button>
                    </div>
                </article>
            @empty
                <p class="empty-state">No equipment matches your search.</p>
            @endforelse
        </div>
    </div>

    {{-- Book Equipment Modal --}}
    <div class="modal-backdrop" id="modal-backdrop">
        <div class="modal modal-large" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div id="modal-content">
                <form id="booking-form" novalidate="">
                    <div class="modal-header">
                        <div class="modal-heading"><span class="modal-heading-icon "><svg class="icon "
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01">
                                    </path>
                                </svg></span>
                            <div>
                                <h2 id="modal-title">Book equipment</h2>
                                <p>Choose a date and time to see the exact quantity available.</p>
                            </div>
                        </div><button class="icon-button modal-close" type="button" data-close-modal=""
                            aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12"></path>
                            </svg></button>
                    </div>
                    <div class="modal-body">
                        <div class="field-group"><label for="booking-equipment">Equipment type <span
                                    class="required">*</span></label><select id="booking-equipment" name="equipmentId"
                                required="">
                                <option value="eq-projector">Epson EB-2265U Projector — Projectors</option>
                                <option value="eq-hdmi" selected="">High-Speed HDMI Cable (3m) — HDMI Cables</option>
                                <option value="eq-extension">8-Way Surge Extension Board — Extension Boards</option>
                                <option value="eq-audio">Portable PA Audio System — Audio Systems</option>
                            </select></div>
                        <div class="form-grid two-columns">
                            <div class="field-group"><label for="booking-date">Booking date <span
                                        class="required">*</span></label><input id="booking-date" name="date"
                                    type="date" min="2026-11-08" value="2026-11-11" required=""></div>
                            <div class="field-group"><label for="booking-slot">Available time slot <span
                                        class="required">*</span></label><select id="booking-slot" name="slotId"
                                    required="">
                                    <option value="slot-1">8:00 AM – 9:00 AM — 18 available</option>
                                    <option value="slot-2">9:00 AM – 10:00 AM — 18 available</option>
                                    <option value="slot-3">10:00 AM – 11:00 AM — 18 available</option>
                                    <option value="slot-4">11:00 AM – 12:00 PM — 18 available</option>
                                    <option value="slot-5">12:00 PM – 1:00 PM — 18 available</option>
                                    <option value="slot-6">1:00 PM – 2:00 PM — 18 available</option>
                                    <option value="slot-7">2:00 PM – 3:00 PM — 18 available</option>
                                    <option value="slot-8" selected="">3:00 PM – 4:00 PM — 18 available</option>
                                    <option value="slot-9">4:00 PM – 5:00 PM — 18 available</option>
                                </select></div>
                        </div>
                        <div class="availability-panel">
                            <div><small>Total stock</small><strong id="booking-total-stock">18</strong></div>
                            <div><small>Already reserved</small><strong id="booking-reserved-stock">0</strong></div>
                            <div><small>Available to you</small><strong id="booking-available-stock">18</strong></div>
                        </div>
                        <div class="availability-message " id="availability-message"><svg class="icon "
                                viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 3 3 5-6"></path>
                            </svg><span>18 unit(s) can be requested for 11 Nov 2026 during 3:00 PM – 4:00 PM.</span>
                        </div>
                        <div class="field-group"><label for="booking-quantity">Quantity required <span
                                    class="required">*</span></label><input id="booking-quantity" name="quantity"
                                type="number" min="1" step="1" value="1" required="" max="18"></div>
                        <div class="field-group"><label for="booking-purpose">Purpose of booking <span
                                    class="required">*</span></label><input id="booking-purpose" name="purpose"
                                required="" minlength="4" placeholder="e.g. Database Systems group presentation"></div>
                        <div class="field-group"><label for="booking-notes">Additional notes <span
                                    class="muted-label">(optional)</span></label><textarea id="booking-notes"
                                name="notes"
                                placeholder="Room number, accessories or setup details staff should know"></textarea>
                        </div>
                        <div class="form-note"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 11v6M12 7h.01"></path>
                            </svg><span>Pending requests temporarily reserve stock. Requests expire automatically after
                                the selected slot if staff have not reviewed them.</span></div>
                    </div>
                    <div class="modal-footer"><button class="button button-outline" type="button"
                            data-close-modal="">Cancel</button><button class="button button-primary" id="submit-booking"
                            type="submit"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m5 12 4 4L19 6"></path>
                            </svg> Submit booking request</button></div>
                </form>
            </div>
        </div>
    </div>

</div>