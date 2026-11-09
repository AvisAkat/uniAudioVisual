<?php

use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public string $chartMode = 'category';

    public function mount(): void
    {
        $this->authorizeStudent();
    }

    public function dashboardStats(): array
    {
        $counts = $this->studentBookings()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending', ['Pending'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved', ['Approved'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed', ['Completed'])
            ->first();

        return [
            'total' => (int) ($counts->total ?? 0),
            'pending' => (int) ($counts->pending ?? 0),
            'approved' => (int) ($counts->approved ?? 0),
            'completed' => (int) ($counts->completed ?? 0),
        ];
    }

    public function volumeChart(): array
    {
        $colors = ['var(--primary)', 'var(--accent)', 'var(--purple)', 'var(--info)', 'var(--success)'];

        if ($this->chartMode === 'month') {
            $months = [];
            $firstMonth = now()->startOfMonth()->subMonths(5);
            $selects = [];
            $bindings = [];

            for ($offset = 0; $offset < 6; $offset++) {
                $month = $firstMonth->copy()->addMonths($offset);
                $nextMonth = $month->copy()->addMonth();
                $alias = 'month_' . $offset;

                $months[] = [
                    'label' => $month->format('M'),
                    'fullLabel' => $month->format('F Y'),
                    'alias' => $alias,
                ];
                $selects[] = "SUM(CASE WHEN created_at >= ? AND created_at < ? THEN 1 ELSE 0 END) as {$alias}";
                $bindings[] = $month->toDateTimeString();
                $bindings[] = $nextMonth->toDateTimeString();
            }

            $counts = $this->studentBookings()
                ->selectRaw(implode(', ', $selects), $bindings)
                ->first();

            $data = collect($months)->map(
                fn (array $month): array => [
                    'label' => $month['fullLabel'],
                    'displayLabel' => $month['label'],
                    'value' => (int) ($counts->{$month['alias']} ?? 0),
                ]
            )->all();
        } else {
            $data = $this->studentBookings()
                ->join('equipment', 'bookings.equipment_id', '=', 'equipment.id')
                ->selectRaw('equipment.category as label, COUNT(bookings.id) as value')
                ->groupBy('equipment.category')
                ->orderByDesc('value')
                ->orderBy('equipment.category')
                ->get()
                ->map(fn (Booking $booking): array => [
                    'label' => (string) $booking->label,
                    'displayLabel' => Str::limit((string) $booking->label, 12, ''),
                    'value' => (int) $booking->value,
                ])
                ->all();
        }

        $maximumValue = max(1, max(array_column($data, 'value') ?: [0]));
        $step = max(1, (int) ceil($maximumValue / 4));
        $axisMaximum = $step * 4;
        $plotWidth = 508;
        $columnWidth = count($data) > 0 ? $plotWidth / count($data) : $plotWidth;
        $barWidth = min(44, $columnWidth * 0.42);

        foreach ($data as $index => &$bar) {
            $bar['color'] = $colors[$index % count($colors)];
            $bar['x'] = 38 + ($columnWidth * $index) + (($columnWidth - $barWidth) / 2);
            $bar['center'] = $bar['x'] + ($barWidth / 2);
            $bar['width'] = $barWidth;
            $bar['height'] = ($bar['value'] / $axisMaximum) * 184;
            $bar['y'] = 208 - $bar['height'];
            $bar['delay'] = $index * 70;
        }
        unset($bar);

        $ticks = [];

        for ($index = 0; $index <= 4; $index++) {
            $ticks[] = [
                'value' => $axisMaximum - ($step * $index),
                'y' => 24 + ($index * 46),
            ];
        }

        return [
            'bars' => $data,
            'ticks' => $ticks,
            'total' => array_sum(array_column($data, 'value')),
        ];
    }

    public function volumeInsight(): string
    {
        $chart = $this->volumeChart;

        if ($chart['total'] === 0) {
            return $this->chartMode === 'month'
                ? 'No booking activity in the last six months.'
                : 'No booking activity to group by equipment category yet.';
        }

        $leadingBar = collect($chart['bars'])->sortByDesc('value')->first();
        $percentage = (int) round(($leadingBar['value'] / $chart['total']) * 100);

        return $this->chartMode === 'month'
            ? "{$leadingBar['label']} had {$leadingBar['value']} requests ({$percentage}% of recent activity)."
            : "{$leadingBar['label']} accounts for {$leadingBar['value']} " . Str::plural('booking', $leadingBar['value']) . " ({$percentage}% of your history).";
    }

    public function statusBreakdown(): array
    {
        $statusOrder = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'];
        $statusColors = [
            'pending' => 'var(--warning)',
            'approved' => 'var(--success)',
            'rejected' => 'var(--danger)',
            'cancelled' => 'var(--muted-light)',
            'completed' => 'var(--primary)',
            'expired' => 'var(--purple)',
        ];
        $statusCounts = $this->studentBookings()
            ->select('status')
            ->selectRaw('COUNT(*) as booking_count')
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->mapWithKeys(fn (Booking $booking): array => [(string) $booking->status => (int) $booking->booking_count]);

        $statuses = collect($statusOrder)
            ->filter(fn (string $status): bool => $statusCounts->has($status))
            ->concat($statusCounts->keys()->diff($statusOrder))
            ->values();
        $total = (int) $statusCounts->sum();
        $circumference = 2 * pi() * 82;
        $offset = 0.0;
        $gap = $statuses->count() > 1 ? 2.5 : 0;

        return $statuses
            ->map(function (string $status) use ($statusCounts, $total, $circumference, $gap, &$offset, $statusColors): array {
                $value = (int) $statusCounts->get($status);
                $segmentLength = $total > 0 ? ($value / $total) * $circumference : 0;
                $visibleLength = max(0, $segmentLength - $gap);
                $segment = [
                    'label' => $status,
                    'value' => $value,
                    'percentage' => $total > 0 ? (int) round(($value / $total) * 100) : 0,
                    'color' => $statusColors[Str::lower($status)] ?? 'var(--info)',
                    'dasharray' => $visibleLength . ' ' . $circumference,
                    'dashoffset' => -$offset,
                ];
                $offset += $segmentLength;

                return $segment;
            })
            ->all();
    }

    public function recentBookings(): Collection
    {
        return $this->studentBookings()
            ->with('equipment')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    public function nextReservation(): ?Booking
    {
        $now = now();

        return $this->studentBookings()
            ->with('equipment')
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function (Builder $query) use ($now): void {
                $query->whereDate('booking_date', '>', $now->toDateString())
                    ->orWhere(function (Builder $query) use ($now): void {
                        $query->whereDate('booking_date', $now->toDateString())
                            ->where('start_time', '>', $now->format('H:i:s'));
                    });
            })
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->first();
    }

    public function nextSlotCapacity(): array
    {
        $now = now();
        $slots = $this->bookingSlots();
        $date = $now->copy();
        $slotKey = null;

        foreach ($slots as $key => $slot) {
            if ($slot['start'] > $now->format('H:i:s')) {
                $slotKey = $key;

                break;
            }
        }

        if ($slotKey === null) {
            $date = $date->addDay();
            $slotKey = array_key_first($slots);
        }

        $slot = $slots[$slotKey];
        $reservedByEquipment = Booking::query()
            ->whereDate('booking_date', $date->toDateString())
            ->where('start_time', $slot['start'])
            ->where('end_time', $slot['end'])
            ->whereIn('status', ['Pending', 'Approved'])
            ->selectRaw('equipment_id, SUM(quantity) as reserved_quantity')
            ->groupBy('equipment_id')
            ->pluck('reserved_quantity', 'equipment_id');

        $equipmentItems = Equipment::query()
            ->orderBy('name')
            ->get(['id', 'name', 'total_quantity', 'available_quantity', 'condition'])
            ->map(function (Equipment $equipment) use ($reservedByEquipment): array {
                $bookableTotal = $equipment->condition === 'Good'
                    ? min($equipment->total_quantity, $equipment->available_quantity)
                    : 0;
                $reserved = (int) $reservedByEquipment->get($equipment->id, 0);

                return [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'total' => $equipment->total_quantity,
                    'reserved' => $reserved,
                    'percentage' => $bookableTotal > 0
                        ? min(100, (int) round(($reserved / $bookableTotal) * 100))
                        : 0,
                ];
            });

        return [
            'date' => $date,
            'slot' => $slot,
            'items' => $equipmentItems,
        ];
    }

    public function setChartMode(string $mode): void
    {
        $this->authorizeStudent();

        if (! in_array($mode, ['category', 'month'], true)) {
            abort(404);
        }

        $this->chartMode = $mode;
        unset($this->volumeChart, $this->volumeInsight);
    }

    private function bookingSlots(): array
    {
        return [
            'slot-1' => ['label' => '7:30 AM – 10:30 AM', 'start' => '07:30:00', 'end' => '10:30:00'],
            'slot-2' => ['label' => '11:00 AM – 2:00 PM', 'start' => '11:00:00', 'end' => '14:00:00'],
            'slot-3' => ['label' => '2:15 PM – 5:15 PM', 'start' => '14:15:00', 'end' => '17:15:00'],
        ];
    }

    private function studentBookings(): Builder
    {
        return Booking::query()->where('user_id', auth()->id());
    }

    private function authorizeStudent(): void
    {
        abort_unless(auth()->user()?->role === 'student', 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-overview" aria-label="Dashboard overview">
        <div id="overview-content">
            <section class="welcome-banner">
                <div class="welcome-copy">
                    <span class="eyebrow eyebrow-light">Student workspace</span>
                    <h2>Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 18 ? 'afternoon' : 'evening') }}, {{ Str::before(trim(auth()->user()->name), ' ') }}.</h2>
                    <p>Your equipment requests, live stock and collection schedule are all in one place. Reserve ahead and skip the department queue.</p>
                    <div class="welcome-actions">
                        <a class="button button-primary" href="{{ route('student.browse_equipment') }}">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                            Book equipment
                        </a>
                        <a class="button button-outline" href="{{ route('student.check_availability') }}">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                            Check a time slot
                        </a>
                    </div>
                </div>
                <div class="welcome-side">
                    <span class="welcome-side-label">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                        Next reservation
                    </span>
                    @if ($this->nextReservation)
                        @php
                            $nextReservation = $this->nextReservation;
                            $nextReservationReference = 'AVB-' . $nextReservation->booking_date->format('ymd') . '-' . str_pad((string) $nextReservation->id, 4, '0', STR_PAD_LEFT);
                            $nextReservationTime = Carbon::parse($nextReservation->start_time)->format('g:i A') . ' – ' . Carbon::parse($nextReservation->end_time)->format('g:i A');
                        @endphp
                        <strong>{{ $this->nextReservation->equipment?->name ?? 'Equipment unavailable' }}</strong>
                        <p>
                            {{ $nextReservation->booking_date->format('l, j M Y') }}<br>
                            {{ $nextReservationTime }}
                        </p>
                        <div class="mini-meta">{{ $nextReservationReference }} · {{ $nextReservation->status }}</div>
                    @else
                        <strong>No upcoming reservations</strong>
                        <p>Approved and pending future bookings will appear here.</p>
                        <div class="mini-meta">Your next reservation</div>
                    @endif
                </div>
            </section>

            <section class="stats-grid" aria-label="Booking summary">
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon blue"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path></svg></span>
                        <span class="stat-trend">Lifetime</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['total'] }}</div>
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-footer">All requests in your history</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon yellow"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                        <span class="stat-trend">Needs review</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['pending'] }}</div>
                    <div class="stat-label">Pending Requests</div>
                    <div class="stat-footer">Awaiting department review</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon green"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-6"></path></svg></span>
                        <span class="stat-trend">Ready</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['approved'] }}</div>
                    <div class="stat-label">Approved Bookings</div>
                    <div class="stat-footer">Confirmed reservations</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon purple"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 14-4-4 4-4M5 10h9a5 5 0 0 1 5 5v5"></path></svg></span>
                        <span class="stat-trend">Returned</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['completed'] }}</div>
                    <div class="stat-label">Completed Bookings</div>
                    <div class="stat-footer">Equipment successfully returned</div>
                </article>
            </section>

            <section class="charts-grid">
                <article class="card chart-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Booking volume</h2>
                            <p>{{ $chartMode === 'category' ? 'Your requests grouped by equipment category' : 'Your requests created over the last six months' }}</p>
                        </div>
                        <div class="chart-modes" role="group" aria-label="Booking volume data view">
                            <button
                                class="chart-mode {{ $chartMode === 'category' ? 'is-active' : '' }}"
                                type="button"
                                wire:click="setChartMode('category')"
                                aria-pressed="{{ $chartMode === 'category' ? 'true' : 'false' }}"
                            >By equipment</button>
                            <button
                                class="chart-mode {{ $chartMode === 'month' ? 'is-active' : '' }}"
                                type="button"
                                wire:click="setChartMode('month')"
                                aria-pressed="{{ $chartMode === 'month' ? 'true' : 'false' }}"
                            >By month</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-canvas">
                            <svg class="chart-svg bar-chart is-rendered" viewBox="0 0 560 250" role="img" aria-label="Bar chart of your bookings {{ $chartMode === 'category' ? 'by equipment category' : 'by month' }}">
                                @foreach ($this->volumeChart['ticks'] as $tick)
                                    <line class="chart-gridline" x1="38" y1="{{ $tick['y'] }}" x2="546" y2="{{ $tick['y'] }}"></line>
                                    <text class="chart-axis-label" x="29" y="{{ $tick['y'] + 4 }}" text-anchor="end">{{ $tick['value'] }}</text>
                                @endforeach
                                <line class="chart-baseline" x1="38" y1="208" x2="546" y2="208"></line>
                                @foreach ($this->volumeChart['bars'] as $bar)
                                    <g class="bar-group" wire:key="volume-bar-{{ $chartMode }}-{{ $loop->index }}" style="--bar-color: {{ $bar['color'] }}">
                                        <rect class="bar-hit" x="{{ 38 + ($loop->index * (508 / max(1, count($this->volumeChart['bars'])))) }}" y="24" width="{{ 508 / max(1, count($this->volumeChart['bars'])) }}" height="184"></rect>
                                        <rect class="bar-rect" x="{{ $bar['x'] }}" y="{{ $bar['y'] }}" width="{{ $bar['width'] }}" height="{{ $bar['height'] }}" rx="7" fill="{{ $bar['color'] }}" style="transition-delay: {{ $bar['delay'] }}ms"></rect>
                                        <text class="bar-value" x="{{ $bar['center'] }}" y="{{ max(18, $bar['y'] - 9) }}" text-anchor="middle" style="transition-delay: {{ $bar['delay'] + 280 }}ms">{{ $bar['value'] }}</text>
                                        <text class="chart-axis-label bar-label" x="{{ $bar['center'] }}" y="234" text-anchor="middle">{{ $bar['displayLabel'] }}</text>
                                    </g>
                                @endforeach
                            </svg>
                        </div>
                        <p class="chart-insight">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l2-7 4 14 2-7h6"></path></svg>
                            <span>{{ $this->volumeInsight }}</span>
                        </p>
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
                        <div class="chart-canvas donut-canvas">
                            <svg class="chart-svg donut-chart is-rendered" viewBox="0 0 236 236" role="img" aria-label="Donut chart of your bookings by status">
                                <circle class="donut-track" cx="118" cy="118" r="82" fill="none" stroke-width="30"></circle>
                                <g transform="rotate(-90 118 118)">
                                    @foreach ($this->statusBreakdown as $segment)
                                        <circle
                                            class="donut-segment"
                                            wire:key="status-segment-{{ Str::slug($segment['label']) }}"
                                            cx="118"
                                            cy="118"
                                            r="82"
                                            fill="none"
                                            stroke="{{ $segment['color'] }}"
                                            stroke-width="30"
                                            stroke-dasharray="{{ $segment['dasharray'] }}"
                                            stroke-dashoffset="{{ $segment['dashoffset'] }}"
                                        ></circle>
                                    @endforeach
                                </g>
                                <text class="donut-total" x="118" y="116" text-anchor="middle">{{ $this->dashboardStats['total'] }}</text>
                                <text class="donut-caption" x="118" y="136" text-anchor="middle">Requests</text>
                            </svg>
                            <ul class="chart-legend">
                                @forelse ($this->statusBreakdown as $segment)
                                    <li class="legend-row" wire:key="status-legend-{{ Str::slug($segment['label']) }}">
                                        <span class="legend-swatch" style="background: {{ $segment['color'] }}"></span>
                                        <span class="legend-label">{{ $segment['label'] }}</span>
                                        <span class="legend-value">{{ $segment['value'] }}</span>
                                        <span class="legend-pct">{{ $segment['percentage'] }}%</span>
                                    </li>
                                @empty
                                    <li class="legend-row"><span class="legend-label">No booking requests yet</span></li>
                                @endforelse
                            </ul>
                        </div>
                        @php
                            $leadingStatus = collect($this->statusBreakdown)->sortByDesc('value')->first();
                        @endphp
                        <p class="chart-insight">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path></svg>
                            <span>{{ $leadingStatus ? "{$leadingStatus['label']} is your most common outcome with {$leadingStatus['value']} bookings." : 'Your booking outcomes will appear here.' }}</span>
                        </p>
                    </div>
                </article>
            </section>

            <div class="dashboard-grid">
                <section class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Recent booking activity</h2>
                            <p>Your latest requests and status changes</p>
                        </div>
                        <a class="text-button" href="{{ route('student.my_bookings') }}">View all bookings</a>
                    </div>
                    <div class="info-list">
                        @forelse ($this->recentBookings as $booking)
                            @php
                                $activityClass = match (Str::lower($booking->status)) {
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => '',
                                };
                                $bookingReference = 'AVB-' . $booking->booking_date->format('ymd') . '-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
                                $formattedTimeSlot = Carbon::parse($booking->start_time)->format('g:i A') . ' – ' . Carbon::parse($booking->end_time)->format('g:i A');
                            @endphp
                            <div class="info-list-item" wire:key="recent-booking-{{ $booking->id }}">
                                <span class="activity-symbol {{ $activityClass }}">
                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                        @if ($booking->status === 'Rejected')
                                            <path d="M18 6 6 18M6 6l12 12"></path>
                                        @else
                                            <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                            <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path>
                                        @endif
                                    </svg>
                                </span>
                                <div class="info-list-copy">
                                    <strong>{{ $booking->equipment?->name ?? 'Equipment unavailable' }} · {{ $booking->quantity }} unit(s)</strong>
                                    <p>{{ $booking->purpose ?: 'No purpose provided' }}</p>
                                    <small>{{ $bookingReference }} · {{ $booking->booking_date->format('j M Y') }} · {{ $formattedTimeSlot }}</small>
                                </div>
                                <div class="info-list-meta">
                                    <span class="status-badge status-{{ Str::slug($booking->status) }}">{{ $booking->status }}</span>
                                    <small style="display: block; margin-top: 5px; color: var(--muted-light); font-size: 8px">{{ $booking->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <h3>No booking activity yet</h3>
                                <p>Your equipment requests will appear here after you make a booking.</p>
                                <a class="button button-primary button-small" href="{{ route('student.browse_equipment') }}">Browse equipment</a>
                            </div>
                        @endforelse
                    </div>
                </section>

                <div class="dashboard-stack">
                    <section class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Next-slot capacity</h2>
                                <p>{{ $this->nextSlotCapacity['date']->format('j M Y') }} · {{ $this->nextSlotCapacity['slot']['label'] }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="capacity-list">
                                @forelse ($this->nextSlotCapacity['items'] as $item)
                                    <div wire:key="capacity-equipment-{{ $item['id'] }}">
                                        <div class="capacity-item-top">
                                            <strong>{{ $item['name'] }}</strong>
                                            <span>{{ $item['reserved'] }}/{{ $item['total'] }} reserved</span>
                                        </div>
                                        <div class="progress-track">
                                            <div class="progress-bar" style="width: {{ $item['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <p>No equipment is currently listed in the inventory.</p>
                                    </div>
                                @endforelse
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
                                <li><span>1</span><div><strong>Reserve the exact slot.</strong> Stock in another time slot does not affect your booking.</div></li>
                                <li><span>2</span><div><strong>Pending holds stock.</strong> Staff approval keeps the same units reserved.</div></li>
                                <li><span>3</span><div><strong>Cancel before start.</strong> Release equipment you no longer need.</div></li>
                                <li><span>4</span><div><strong>Return at slot end.</strong> Staff record the return and close your booking.</div></li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</div>
