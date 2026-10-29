@php
    $dailyData = collect($dailyRemittanceTrend ?? []);
    $rcHasData = $dailyData->sum('count') > 0;
    $rcCount = $dailyData->count();
    $rcTotal = (float) $dailyData->sum('net');
    $rcAverage = $rcCount > 0 ? $rcTotal / $rcCount : 0;
    $rcMax = max(1, (float) $dailyData->max('net'));
    // Keep the chart calm when the card stretches across a wide dashboard.
    $rcW = 1200;
    $rcH = 180;
    $rcPadL = 58;
    $rcPadR = 20;
    $rcPadT = 10;
    $rcPadB = 26;
    $rcPlotW = $rcW - $rcPadL - $rcPadR;
    $rcPlotH = $rcH - $rcPadT - $rcPadB;
    $rcStep = $rcCount > 1 ? $rcPlotW / ($rcCount - 1) : 0;
    $rcCoords = [];

    foreach ($dailyData as $i => $day) {
        $x = $rcPadL + ($i * $rcStep);
        $y = $rcPadT + $rcPlotH - (((float) $day['net'] / $rcMax) * $rcPlotH);
        $rcCoords[] = [$x, $y];
    }

    $rcSmoothPath = function (array $coords): string {
        if (count($coords) === 0) {
            return '';
        }

        $path = 'M ' . round($coords[0][0], 1) . ' ' . round($coords[0][1], 1);
        for ($i = 1, $count = count($coords); $i < $count; $i++) {
            [$previousX, $previousY] = $coords[$i - 1];
            [$currentX, $currentY] = $coords[$i];
            $controlOffset = ($currentX - $previousX) / 3;
            $path .= ' C ' . round($previousX + $controlOffset, 1) . ' ' . round($previousY, 1)
                . ', ' . round($currentX - $controlOffset, 1) . ' ' . round($currentY, 1)
                . ', ' . round($currentX, 1) . ' ' . round($currentY, 1);
        }

        return $path;
    };

    $rcLinePath = $rcSmoothPath($rcCoords);
    $rcAreaPath = $rcLinePath && $rcCount > 1
        ? $rcLinePath . ' L ' . round($rcCoords[$rcCount - 1][0], 1) . ' ' . ($rcPadT + $rcPlotH)
            . ' L ' . round($rcCoords[0][0], 1) . ' ' . ($rcPadT + $rcPlotH) . ' Z'
        : '';
@endphp

<div class="sad-fade-up kpi-card overflow-hidden">
    <div class="px-5 pt-5 pb-1 flex items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.17em] text-gray-400 dark:text-neutral-500">Money movement</p>
            <h2 class="mt-1 text-[23px] font-semibold tracking-tight text-gray-900 dark:text-neutral-100">Daily remittance trend</h2>
        </div>
        <span class="rounded-full border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800 px-2.5 py-1 text-[10px] font-semibold tracking-[0.08em] text-gray-500 dark:text-neutral-400">PHP</span>
    </div>

    <div class="grid grid-cols-1 gap-2.5 px-5 pt-4 sm:grid-cols-2">
        <div class="group flex min-w-0 items-start gap-3 rounded-2xl border border-gray-100 dark:border-neutral-800 bg-gray-50/80 dark:bg-neutral-800/60 px-3.5 py-3 transition-colors hover:bg-white dark:hover:bg-neutral-800 sm:px-4 sm:py-3.5">
            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-[#ee765f] shadow-[0_0_12px_rgba(238,118,95,0.35)]"></span>
            <div class="min-w-0">
                <p class="truncate text-[11px] font-medium uppercase tracking-[0.13em] text-gray-500 dark:text-neutral-400">Total remitted</p>
                <p class="mt-1 text-[21px] font-semibold tracking-tight text-gray-900 dark:text-neutral-100 sm:text-[23px]">₱{{ number_format($rcTotal, 2) }}</p>
            </div>
        </div>
        <div class="group flex min-w-0 items-start gap-3 rounded-2xl border border-gray-100 dark:border-neutral-800 bg-gray-50/80 dark:bg-neutral-800/60 px-3.5 py-3 transition-colors hover:bg-white dark:hover:bg-neutral-800 sm:px-4 sm:py-3.5">
            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-[#55b995] shadow-[0_0_12px_rgba(85,185,149,0.35)]"></span>
            <div class="min-w-0">
                <p class="truncate text-[11px] font-medium uppercase tracking-[0.13em] text-gray-500 dark:text-neutral-400">Daily average</p>
                <p class="mt-1 text-[21px] font-semibold tracking-tight text-gray-900 dark:text-neutral-100 sm:text-[23px]">₱{{ number_format($rcAverage, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3 px-5">
        <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-neutral-400"><span class="h-px w-3.5 bg-[#ee765f]"></span> Remitted per day</span>
        <span class="text-[10px] font-semibold uppercase tracking-[0.08em] text-gray-400 dark:text-neutral-500">Last 7 days</span>
    </div>

    <div class="mx-5 mt-2.5 rounded-2xl border border-gray-100 dark:border-neutral-800 bg-gray-50/70 dark:bg-neutral-900/50 px-1.5 pb-1 pt-4 sm:px-2">
        @if($rcHasData && $rcCount > 1)
            <svg viewBox="0 0 {{ $rcW }} {{ $rcH }}" class="h-auto w-full" role="img" aria-label="Net remittance across {{ $rcCount }} days, totalling {{ number_format($rcTotal, 2) }} pesos">
                <defs>
                    <linearGradient id="dailyRemittanceFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#ee765f" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="#ee765f" stop-opacity="0" />
                    </linearGradient>
                    <filter id="dailyRemittanceLineShadow" x="-20%" y="-30%" width="140%" height="170%" color-interpolation-filters="sRGB">
                        <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#ee765f" flood-opacity="0.28" />
                    </filter>
                </defs>
                @foreach([0, 0.5, 1] as $gridRatio)
                    <line x1="{{ $rcPadL }}" y1="{{ $rcPadT + ($rcPlotH * $gridRatio) }}" x2="{{ $rcW - $rcPadR }}" y2="{{ $rcPadT + ($rcPlotH * $gridRatio) }}" stroke="currentColor" stroke-dasharray="2 5" class="text-gray-300 dark:text-neutral-700" />
                    <text x="{{ $rcPadL - 8 }}" y="{{ $rcPadT + ($rcPlotH * $gridRatio) + 4 }}" text-anchor="end" class="fill-gray-400 dark:fill-neutral-500" font-size="10">₱{{ number_format($rcMax * (1 - $gridRatio) / 1000, 1) }}k</text>
                @endforeach
                <path d="{{ $rcAreaPath }}" fill="url(#dailyRemittanceFill)" />
                <path d="{{ $rcLinePath }}" fill="none" stroke="#ee765f" stroke-width="2" stroke-linecap="round" filter="url(#dailyRemittanceLineShadow)" />
                @foreach($dailyData as $i => $day)
                    @if($i % 2 === 0 || $i === $rcCount - 1)
                        <text x="{{ $rcPadL + ($i * $rcStep) }}" y="{{ $rcH - 7 }}" text-anchor="middle" class="fill-gray-400 dark:fill-neutral-500" font-size="10">{{ \Carbon\Carbon::parse($day['date'])->format('M d') }}</text>
                    @endif
                @endforeach
                <g class="daily-remittance-hover" aria-hidden="true" style="display:none">
                    <line class="daily-remittance-hover-line" y1="{{ $rcPadT }}" y2="{{ $rcPadT + $rcPlotH }}" stroke="currentColor" stroke-dasharray="3 4" />
                    <circle class="daily-remittance-hover-dot" r="4" fill="#ee765f" stroke="white" stroke-width="1.5" />
                    <rect class="daily-remittance-hover-box" width="150" height="54" rx="10" fill="white" stroke="#e4e3dc" />
                    <text class="daily-remittance-hover-date" x="12" y="17" font-size="9" font-weight="700" letter-spacing="1.2" fill="#969791" />
                    <text class="daily-remittance-hover-value" x="12" y="39" font-size="11" fill="#d96650" />
                </g>
                <rect x="{{ $rcPadL }}" y="{{ $rcPadT }}" width="{{ $rcPlotW }}" height="{{ $rcPlotH }}" fill="transparent" class="daily-remittance-chart-overlay" />
            </svg>
            <script>
                (() => {
                    const chart = document.currentScript.previousElementSibling;
                    const overlay = chart.querySelector('.daily-remittance-chart-overlay');
                    const hover = chart.querySelector('.daily-remittance-hover');
                    const points = @json($dailyData->values());
                    const xs = points.map((_, i) => {{ $rcPadL }} + (i * {{ $rcStep }}));
                    const ys = @json(array_map(fn($day) => $rcPadT + $rcPlotH - (((float) $day['net'] / $rcMax) * $rcPlotH), $dailyData->all()));
                    const show = (index) => {
                        const x = xs[index];
                        const boxX = Math.min(Math.max(x - 75, {{ $rcPadL }}), {{ $rcW }} - 162);
                        const boxY = Math.max(14, ys[index] - 68);
                        hover.style.display = '';
                        hover.querySelector('.daily-remittance-hover-line').setAttribute('x1', x);
                        hover.querySelector('.daily-remittance-hover-line').setAttribute('x2', x);
                        hover.querySelector('.daily-remittance-hover-dot').setAttribute('cx', x);
                        hover.querySelector('.daily-remittance-hover-dot').setAttribute('cy', ys[index]);
                        hover.querySelector('.daily-remittance-hover-box').setAttribute('x', boxX);
                        hover.querySelector('.daily-remittance-hover-box').setAttribute('y', boxY);
                        hover.querySelector('.daily-remittance-hover-date').setAttribute('transform', `translate(${boxX}, ${boxY})`);
                        hover.querySelector('.daily-remittance-hover-value').setAttribute('transform', `translate(${boxX}, ${boxY})`);
                        hover.querySelector('.daily-remittance-hover-date').textContent = points[index].date.replace(/^(\d{4})-/, '').toUpperCase();
                        hover.querySelector('.daily-remittance-hover-value').textContent = `Remitted  ₱${Number(points[index].net).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                    };
                    overlay.addEventListener('mousemove', (event) => {
                        const box = overlay.getBoundingClientRect();
                        const value = ((event.clientX - box.left) / box.width) * (xs[xs.length - 1] - xs[0]) + xs[0];
                        show(Math.max(0, Math.min(xs.length - 1, Math.round((value - xs[0]) / {{ $rcStep }}))));
                    });
                    overlay.addEventListener('mouseleave', () => { hover.style.display = 'none'; });
                })();
            </script>
        @else
            <div class="flex h-[205px] items-center justify-center text-xs text-gray-400 dark:text-neutral-500">No finalized remittances in the last 7 days.</div>
        @endif
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 px-5 pb-5 text-[11px] text-gray-500 dark:text-neutral-400">
        <span>Approved and completed remittances</span>
        <a href="{{ route('remittances.index') }}" class="inline-flex items-center gap-1 font-semibold text-gray-600 no-underline transition-colors hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white">View details <span aria-hidden="true">⌄</span></a>
    </div>
</div>
