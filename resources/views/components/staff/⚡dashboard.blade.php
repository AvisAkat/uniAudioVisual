<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public string $demandMode = 'category';

    public function mount(): void
    {
        $this->authorizeStaff();
    }

    #[Computed]
    public function dashboardStats(): array
    {
        $counts = Booking::query()
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending', ['Pending'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved', ['Approved'])
            ->selectRaw('SUM(CASE WHEN DATE(booking_date) = ? THEN 1 ELSE 0 END) as today', [today()->toDateString()])
            ->first();

        $equipment = Equipment::query()
            ->selectRaw('COUNT(*) as types')
            ->selectRaw('COALESCE(SUM(total_quantity), 0) as total_units')
            ->first();

        $capacity = $this->nextSlotCapacity;

        return [
            'totalUnits' => (int) $equipment->total_units,
            'equipmentTypes' => (int) $equipment->types,
            'availableUnits' => $capacity['available'],
            'pending' => (int) $counts->pending,
            'approved' => (int) $counts->approved,
            'today' => (int) $counts->today,
        ];
    }

    #[Computed]
    public function demandChart(): array
    {
        $colors = ['var(--primary)', 'var(--accent)', 'var(--purple)', 'var(--info)', 'var(--success)'];
        $bookings = $this->upcomingBookingsQuery();

        if ($this->demandMode === 'week') {
            $firstDay = today();
            $groupedBookings = (clone $bookings)
                ->select('booking_date')
                ->selectRaw('SUM(quantity) as reserved_quantity')
                ->whereDate('booking_date', '<=', $firstDay->copy()->addDays(6)->toDateString())
                ->groupBy('booking_date')
                ->get()
                ->keyBy(fn (Booking $booking): string => Carbon::parse($booking->booking_date)->toDateString());

            $data = collect(range(0, 6))
                ->map(function (int $offset) use ($firstDay, $groupedBookings): array {
                    $date = $firstDay->copy()->addDays($offset);
                    $day = $date->toDateString();

                    return [
                        'label' => $date->format('D'),
                        'fullLabel' => $date->format('l, j M Y'),
                        'value' => (int) ($groupedBookings->get($day)?->reserved_quantity ?? 0),
                    ];
                })
                ->all();
        } else {
            $data = (clone $bookings)
                ->join('equipment', 'bookings.equipment_id', '=', 'equipment.id')
                ->selectRaw('equipment.category as label, SUM(bookings.quantity) as reserved_quantity')
                ->groupBy('equipment.category')
                ->orderByDesc('reserved_quantity')
                ->orderBy('equipment.category')
                ->get()
                ->map(fn (Booking $booking): array => [
                    'label' => (string) $booking->label,
                    'fullLabel' => (string) $booking->label,
                    'value' => (int) $booking->reserved_quantity,
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
            $bar['displayLabel'] = Str::limit($bar['label'], 12, '');
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

    #[Computed]
    public function demandInsight(): string
    {
        $chart = $this->demandChart;

        if ($chart['total'] === 0) {
            return $this->demandMode === 'week'
                ? 'No pending or approved units are scheduled in the next seven days.'
                : 'No upcoming pending or approved bookings are holding stock.';
        }

        $leadingBar = collect($chart['bars'])->sortByDesc('value')->first();
        $percentage = (int) round(($leadingBar['value'] / $chart['total']) * 100);

        return $this->demandMode === 'week'
            ? "{$leadingBar['fullLabel']} has the highest scheduled demand with {$leadingBar['value']} units ({$percentage}% of this week)."
            : "{$leadingBar['label']} has the highest scheduled demand with {$leadingBar['value']} units ({$percentage}% of upcoming reservations).";
    }

    #[Computed]
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
        $statusCounts = Booking::query()
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
                    'dasharray' => $visibleLength.' '.$circumference,
                    'dashoffset' => -$offset,
                ];
                $offset += $segmentLength;

                return $segment;
            })
            ->all();
    }

    #[Computed]
    public function statusTotal(): int
    {
        return (int) collect($this->statusBreakdown)->sum('value');
    }

    #[Computed]
    public function pendingBookings(): Collection
    {
        $this->authorizeStaff();

        return Booking::query()
            ->with(['user', 'equipment'])
            ->where('status', 'Pending')
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->limit(5)
            ->get();
    }

    #[Computed]
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
            $date->addDay();
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

        $items = Equipment::query()
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
                    'condition' => $equipment->condition,
                    'total' => $bookableTotal,
                    'reserved' => $reserved,
                    'available' => max(0, $bookableTotal - $reserved),
                    'percentage' => $bookableTotal > 0
                        ? min(100, (int) round(($reserved / $bookableTotal) * 100))
                        : 0,
                ];
            });

        return [
            'date' => $date,
            'slot' => $slot,
            'available' => (int) $items->sum('available'),
            'total' => (int) $items->sum('total'),
            'allGood' => $items->isNotEmpty() && $items->every(fn (array $item): bool => $item['condition'] === 'Good'),
            'items' => $items,
        ];
    }

    #[Computed]
    public function recentActivity(): Collection
    {
        $this->authorizeStaff();

        return ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    public function setDemandMode(string $mode): void
    {
        $this->authorizeStaff();

        if (! in_array($mode, ['category', 'week'], true)) {
            abort(404);
        }

        $this->demandMode = $mode;
        unset($this->demandChart, $this->demandInsight);
    }

    public function bookingReference(Booking $booking): string
    {
        return 'AVB-'.Carbon::parse($booking->booking_date)->format('ymd').'-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
    }

    public function formattedTime(string $time): string
    {
        return Carbon::parse($time)->format('g:i A');
    }

    private function upcomingBookingsQuery(): Builder
    {
        $now = now();

        return Booking::query()
            ->whereIn('bookings.status', ['Pending', 'Approved'])
            ->where(function (Builder $query) use ($now): void {
                $query->whereDate('bookings.booking_date', '>', $now->toDateString())
                    ->orWhere(function (Builder $query) use ($now): void {
                        $query->whereDate('bookings.booking_date', $now->toDateString())
                            ->where('bookings.start_time', '>', $now->format('H:i:s'));
                    });
            });
    }

    private function bookingSlots(): array
    {
        return [
            'slot-1' => ['label' => '7:30 AM – 10:30 AM', 'start' => '07:30:00', 'end' => '10:30:00'],
            'slot-2' => ['label' => '11:00 AM – 2:00 PM', 'start' => '11:00:00', 'end' => '14:00:00'],
            'slot-3' => ['label' => '2:15 PM – 5:15 PM', 'start' => '14:15:00', 'end' => '17:15:00'],
        ];
    }

    private function authorizeStaff(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['staff', 'admin'], true), 403);
    }
};
?>

<div>
    <section class="app-view is-entering" id="view-overview" aria-label="Dashboard overview">
        <div id="overview-content">
            <section class="welcome-banner">
                <div class="welcome-copy">
                    <span class="eyebrow eyebrow-light">Department operations</span>
                    <h2>Welcome, {{ Str::before(trim(auth()->user()->name), ' ') }}.</h2>
                    <p>
                        {{ $this->dashboardStats['pending'] }} request(s) need review. Monitor capacity, prevent scheduling conflicts and keep every return accounted for.
                    </p>
                    <div class="welcome-actions">
                        <a class="button button-primary" href="{{ route('staff.booking_request') }}">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <path d="M14 2v6h6M8 13h8M8 17h5"></path>
                            </svg>
                            Review requests
                        </a>
                        <a class="button button-outline" href="{{ route('staff.time_slot') }}">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path>
                            </svg>
                            Open calendar
                        </a>
                    </div>
                </div>
                <div class="welcome-side">
                    <span class="welcome-side-label">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 12h4l2-7 4 14 2-7h6"></path>
                        </svg>
                        Live stock position
                    </span>
                    <strong>{{ $this->nextSlotCapacity['available'] }} of {{ $this->nextSlotCapacity['total'] }} units available</strong>
                    <p>{{ $this->nextSlotCapacity['date']->format('j M Y') }}<br>{{ $this->nextSlotCapacity['slot']['label'] }}</p>
                    <div class="mini-meta">
                        {{ $this->nextSlotCapacity['allGood'] ? 'All catalogue items are in good condition' : 'Some catalogue items are unavailable' }}
                    </div>
                </div>
            </section>

            <section class="stats-grid five-cards" aria-label="Department summary">
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon blue"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 8 4-8 4-8-4 8-4Z M4 6v6l8 4 8-4V6M4 12v6l8 4 8-4v-6M12 10v12"></path></svg></span>
                        <span class="stat-trend">Units</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['totalUnits'] }}</div>
                    <div class="stat-label">Total Equipment</div>
                    <div class="stat-footer">{{ $this->dashboardStats['equipmentTypes'] }} catalogue equipment type(s)</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon green"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-6"></path></svg></span>
                        <span class="stat-trend">Live</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['availableUnits'] }}</div>
                    <div class="stat-label">Available Equipment</div>
                    <div class="stat-footer">For the next bookable slot</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon yellow"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg></span>
                        <span class="stat-trend">Action</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['pending'] }}</div>
                    <div class="stat-label">Pending Requests</div>
                    <div class="stat-footer">Awaiting staff review</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon purple"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"></path></svg></span>
                        <span class="stat-trend">Active</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['approved'] }}</div>
                    <div class="stat-label">Approved Bookings</div>
                    <div class="stat-footer">Confirmed reservations</div>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top">
                        <span class="stat-icon red"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"></path></svg></span>
                        <span class="stat-trend">Today</span>
                    </div>
                    <div class="stat-value">{{ $this->dashboardStats['today'] }}</div>
                    <div class="stat-label">Today's Bookings</div>
                    <div class="stat-footer">{{ today()->format('j M Y') }}</div>
                </article>
            </section>

            <section class="charts-grid">
                <article class="card chart-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Demand forecast</h2>
                            <p>Units held by upcoming pending and approved bookings</p>
                        </div>
                        <div class="chart-modes" role="group" aria-label="Demand forecast data view">
                            <button class="chart-mode {{ $demandMode === 'category' ? 'is-active' : '' }}" type="button" wire:click="setDemandMode('category')" aria-pressed="{{ $demandMode === 'category' ? 'true' : 'false' }}">Units reserved</button>
                            <button class="chart-mode {{ $demandMode === 'week' ? 'is-active' : '' }}" type="button" wire:click="setDemandMode('week')" aria-pressed="{{ $demandMode === 'week' ? 'true' : 'false' }}">Next 7 days</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-canvas">
                            <svg class="chart-svg bar-chart is-rendered" viewBox="0 0 560 250" role="img" aria-label="{{ $demandMode === 'week' ? 'Reserved units by day for the next seven days' : 'Reserved units by equipment category' }}">
                                @foreach ($this->demandChart['ticks'] as $tick)
                                    <line wire:key="demand-grid-{{ $loop->index }}" class="chart-gridline" x1="38" y1="{{ $tick['y'] }}" x2="546" y2="{{ $tick['y'] }}"></line>
                                    <text class="chart-axis-label" x="29" y="{{ $tick['y'] + 4 }}" text-anchor="end">{{ $tick['value'] }}</text>
                                @endforeach
                                <line class="chart-baseline" x1="38" y1="208" x2="546" y2="208"></line>
                                @foreach ($this->demandChart['bars'] as $bar)
                                    <g wire:key="demand-bar-{{ $loop->index }}" class="bar-group" style="--bar-color: {{ $bar['color'] }}">
                                        <title>{{ $bar['fullLabel'] }}: {{ $bar['value'] }} reserved unit(s)</title>
                                        <rect class="bar-hit" x="{{ 38 + ($loop->index * (508 / max(1, count($this->demandChart['bars'])))) }}" y="24" width="{{ 508 / max(1, count($this->demandChart['bars'])) }}" height="184"></rect>
                                        <rect class="bar-rect" x="{{ $bar['x'] }}" y="{{ $bar['y'] }}" width="{{ $bar['width'] }}" height="{{ $bar['height'] }}" rx="7" fill="{{ $bar['color'] }}" style="transition-delay:{{ $bar['delay'] }}ms"></rect>
                                        <text class="bar-value" x="{{ $bar['center'] }}" y="{{ max(16, $bar['y'] - 9) }}" text-anchor="middle" style="transition-delay:{{ $bar['delay'] + 280 }}ms">{{ $bar['value'] }}</text>
                                        <text class="chart-axis-label bar-label" x="{{ $bar['center'] }}" y="234" text-anchor="middle">{{ $bar['displayLabel'] }}</text>
                                    </g>
                                @endforeach
                            </svg>
                        </div>
                        <p class="chart-insight">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 8 4-8 4-8-4 8-4Z M4 6v6l8 4 8-4V6M4 12v6l8 4 8-4v-6M12 10v12"></path></svg>
                            <span>{{ $this->demandInsight }}</span>
                        </p>
                    </div>
                </article>

                <article class="card chart-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Request pipeline</h2>
                            <p>All department bookings by current status</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-canvas donut-canvas">
                            <svg class="chart-svg donut-chart is-rendered" viewBox="0 0 236 236" role="img" aria-label="Donut chart of all bookings by status">
                                <circle class="donut-track" cx="118" cy="118" r="82" fill="none" stroke-width="30"></circle>
                                <g transform="rotate(-90 118 118)">
                                    @foreach ($this->statusBreakdown as $segment)
                                        <circle wire:key="status-segment-{{ Str::slug($segment['label']) }}" class="donut-segment" cx="118" cy="118" r="82" fill="none" stroke="{{ $segment['color'] }}" stroke-width="30" stroke-dasharray="{{ $segment['dasharray'] }}" stroke-dashoffset="{{ $segment['dashoffset'] }}">
                                            <title>{{ $segment['label'] }}: {{ $segment['value'] }} booking(s), {{ $segment['percentage'] }}%</title>
                                        </circle>
                                    @endforeach
                                </g>
                                <text class="donut-total" x="118" y="116" text-anchor="middle">{{ $this->statusTotal }}</text>
                                <text class="donut-caption" x="118" y="139" text-anchor="middle">bookings</text>
                            </svg>
                            <ul class="chart-legend">
                                @forelse ($this->statusBreakdown as $index => $segment)
                                    <li class="legend-row" wire:key="staff-status-{{ Str::slug($segment['label']) }}">
                                        <span class="legend-swatch" style="background:{{ $segment['color'] }}"></span>
                                        <span class="legend-label">{{ $segment['label'] }}</span>
                                        <span class="legend-value">{{ $segment['value'] }}</span>
                                        <span class="legend-pct">{{ $segment['percentage'] }}%</span>
                                    </li>
                                @empty
                                    <li class="legend-row"><span class="legend-label">No booking records yet</span></li>
                                @endforelse
                            </ul>
                        </div>
                        @php($leadingStatus = collect($this->statusBreakdown)->sortByDesc('value')->first())
                        <p class="chart-insight">
                            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6M8 13h8M8 17h5"></path></svg>
                            <span>
                                @if ($leadingStatus)
                                    {{ $leadingStatus['label'] }} leads with {{ $leadingStatus['value'] }} booking(s), which is {{ $leadingStatus['percentage'] }}% of the total.
                                @else
                                    Booking status activity will appear here.
                                @endif
                            </span>
                        </p>
                    </div>
                </article>
            </section>

            <div class="dashboard-grid">
                <section class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Requests awaiting review</h2>
                            <p>Ordered by reservation date and start time</p>
                        </div>
                        <a class="text-button" href="{{ route('staff.booking_request') }}">Manage all requests</a>
                    </div>
                    <div class="info-list">
                        @forelse ($this->pendingBookings as $booking)
                            <div class="info-list-item" wire:key="staff-pending-booking-{{ $booking->id }}">
                                <span class="activity-symbol warning">
                                    <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                </span>
                                <div class="info-list-copy">
                                    <strong>{{ $booking->user?->name ?? 'Unknown student' }} · {{ $booking->equipment?->name ?? 'Equipment unavailable' }}</strong>
                                    <p>{{ $booking->quantity }} unit(s) for {{ $booking->purpose ?: 'No purpose provided' }}</p>
                                    <small>{{ $this->bookingReference($booking) }} · {{ $booking->booking_date->format('j M Y') }} · {{ $this->formattedTime($booking->start_time) }} – {{ $this->formattedTime($booking->end_time) }}</small>
                                </div>
                                <div class="table-actions">
                                    <a class="button button-outline" href="{{ route('staff.booking_request', ['search' => $this->bookingReference($booking)]) }}">Review request</a>
                                </div>
                            </div>
                        @empty
                            <div class="info-list-item">
                                <div class="info-list-copy">
                                    <strong>No pending requests</strong>
                                    <p>New student booking requests will appear here.</p>
                                </div>
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
                            <a class="text-button" href="{{ route('staff.time_slot') }}">Open calendar</a>
                        </div>
                        <div class="card-body">
                            <div class="capacity-list">
                                @forelse ($this->nextSlotCapacity['items'] as $item)
                                    <div wire:key="staff-capacity-{{ $item['id'] }}">
                                        <div class="capacity-item-top">
                                            <strong>{{ $item['name'] }}</strong>
                                            <span>{{ $item['reserved'] }}/{{ $item['total'] }} reserved · {{ $item['available'] }} available</span>
                                        </div>
                                        <div class="progress-track">
                                            <div class="progress-bar" style="width:{{ $item['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p>No equipment has been added to the inventory.</p>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Recent activity</h2>
                                <p>Latest department audit events</p>
                            </div>
                        </div>
                        <div class="info-list">
                            @forelse ($this->recentActivity as $activity)
                                <div class="info-list-item" wire:key="staff-activity-{{ $activity->id }}">
                                    <span class="activity-symbol">
                                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l2-7 4 14 2-7h6"></path></svg>
                                    </span>
                                    <div class="info-list-copy">
                                        <strong>{{ $activity->action }}</strong>
                                        <p>{{ $activity->description ?: 'No description provided.' }}</p>
                                        <small>{{ $activity->user?->name ?? 'System / deleted user' }} · {{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="info-list-item">
                                    <div class="info-list-copy">
                                        <strong>No activity recorded yet</strong>
                                        <p>Recent department changes will appear here.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</div>
