@php
    $financialRevenue = (float) ($financialOverview['netRevenue'] ?? 0);
    $financialNetIncome = (float) ($financialOverview['netIncome'] ?? 0);
    $financialExpense = (float) ($financialOverview['netExpenses'] ?? 0);
    $financialDateFrom = $financialOverview['dateFrom'] ?? now()->startOfMonth()->toDateString();
    $financialDateTo = $financialOverview['dateTo'] ?? now()->toDateString();
    $financialTrend = collect($financialTrend ?? []);
    $financialChartMax = max(1, (float) $financialTrend->max('revenue'), (float) $financialTrend->max(fn($point) => max($point['netIncome'], 0)));
@endphp

<div class="sad-slide-bounce kpi-card overflow-hidden" style="animation-delay:0.1s">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-gray-400 dark:text-neutral-500">Performance snapshot</p>
            <h2 class="mt-1 text-[16px] font-semibold tracking-tight text-gray-900 dark:text-neutral-100">Financial Overview</h2>
        </div>
        <span class="rounded-full border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800 px-2.5 py-1 text-[10px] font-semibold text-gray-500 dark:text-neutral-400">
            {{ \Carbon\Carbon::parse($financialDateFrom)->format('M d') }} – {{ \Carbon\Carbon::parse($financialDateTo)->format('M d, Y') }}
        </span>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-100 dark:border-neutral-800 bg-gray-50/70 dark:bg-neutral-800/70 px-3.5 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-neutral-500">Revenue</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-neutral-100 tabular-nums">₱{{ number_format($financialRevenue, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 dark:border-neutral-800 bg-gray-50/70 dark:bg-neutral-800/70 px-3.5 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-neutral-500">Expenses</p>
                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-neutral-100 tabular-nums">₱{{ number_format($financialExpense, 2) }}</p>
            </div>
            <div class="rounded-xl border {{ $financialNetIncome >= 0 ? 'border-emerald-100 bg-emerald-50/60 dark:border-emerald-900/60 dark:bg-emerald-950/40' : 'border-rose-100 bg-rose-50/60 dark:border-rose-900/60 dark:bg-rose-950/40' }} px-3.5 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-neutral-500">{{ $financialNetIncome >= 0 ? 'Net income' : 'Net loss' }}</p>
                <p class="mt-1 text-lg font-semibold {{ $financialNetIncome >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }} tabular-nums">₱{{ number_format(abs($financialNetIncome), 2) }}</p>
            </div>
        </div>
        <div class="mt-4 rounded-xl border border-gray-100 dark:border-neutral-800 bg-gray-50/60 dark:bg-neutral-900/50 px-2 pt-3 pb-1">
            <div class="flex items-center justify-between px-2 text-[10px] text-gray-400 dark:text-neutral-500">
                <span class="flex items-center gap-1.5"><span class="h-0.5 w-3 rounded bg-[#ee765f]"></span>Revenue</span>
                <span class="flex items-center gap-1.5"><span class="h-0.5 w-3 rounded bg-[#55b995]"></span>Net income</span>
                <span>Last 6 months</span>
            </div>
            @if($financialTrend->count() > 1)
                @php
                    // Use a wider viewBox so the chart does not look zoomed in on wide dashboards.
                    $financialChartW = 1200; $financialChartH = 180; $financialChartPadX = 20; $financialChartPadTop = 10; $financialChartPadBottom = 26;
                    $financialPlotW = $financialChartW - ($financialChartPadX * 2);
                    $financialPlotH = $financialChartH - $financialChartPadTop - $financialChartPadBottom;
                    $financialStep = $financialPlotW / ($financialTrend->count() - 1);
                    $revenueCoords = []; $incomeCoords = [];
                    foreach ($financialTrend as $i => $point) {
                        $x = $financialChartPadX + ($i * $financialStep);
                        $revenueCoords[] = [$x, $financialChartPadTop + $financialPlotH - (($point['revenue'] / $financialChartMax) * $financialPlotH)];
                        $incomeCoords[] = [$x, $financialChartPadTop + $financialPlotH - ((max($point['netIncome'], 0) / $financialChartMax) * $financialPlotH)];
                    }
                    $smoothPath = function (array $coords): string {
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
                    $revenuePath = $smoothPath($revenueCoords);
                    $incomePath = $smoothPath($incomeCoords);
                @endphp
                <svg viewBox="0 0 {{ $financialChartW }} {{ $financialChartH }}" class="mt-2 h-auto w-full" role="img" aria-label="Revenue and net income for the last six months">
                    <defs>
                        <filter id="financialRevenueLineShadow" x="-20%" y="-30%" width="140%" height="170%" color-interpolation-filters="sRGB">
                            <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#ee765f" flood-opacity="0.28" />
                        </filter>
                        <filter id="financialIncomeLineShadow" x="-20%" y="-30%" width="140%" height="170%" color-interpolation-filters="sRGB">
                            <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#55b995" flood-opacity="0.24" />
                        </filter>
                    </defs>
                    <line x1="{{ $financialChartPadX }}" y1="{{ $financialChartPadTop + $financialPlotH }}" x2="{{ $financialChartW - $financialChartPadX }}" y2="{{ $financialChartPadTop + $financialPlotH }}" stroke="currentColor" class="text-gray-200 dark:text-neutral-700" />
                    {{-- Draw a visible soft line under each graph. The wider, faint path keeps the shadow visible across browsers. --}}
                    <path d="{{ $revenuePath }}" fill="none" stroke="#ee765f" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" opacity="0.18" transform="translate(0 2)" />
                    <path d="{{ $incomePath }}" fill="none" stroke="#55b995" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" opacity="0.16" transform="translate(0 2)" />
                    <path d="{{ $revenuePath }}" fill="none" stroke="#ee765f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" filter="url(#financialRevenueLineShadow)" />
                    <path d="{{ $incomePath }}" fill="none" stroke="#55b995" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" filter="url(#financialIncomeLineShadow)" />
                    @foreach($financialTrend as $i => $point)
                        @php
                            $financialX = $financialChartPadX + ($i * $financialStep);
                            $financialRevenueY = $financialChartPadTop + $financialPlotH - (($point['revenue'] / $financialChartMax) * $financialPlotH);
                            $financialIncomeY = $financialChartPadTop + $financialPlotH - ((max($point['netIncome'], 0) / $financialChartMax) * $financialPlotH);
                        @endphp
                        <text x="{{ $financialX }}" y="{{ $financialChartH - 6 }}" text-anchor="middle" fill="currentColor" class="text-gray-400 dark:text-neutral-500" font-size="9">{{ $point['month'] }}</text>
                        <circle cx="{{ $financialX }}" cy="{{ $financialRevenueY }}" r="3" fill="#ee765f" stroke="white" stroke-width="1.5" class="financial-chart-point" data-index="{{ $i }}" />
                        <circle cx="{{ $financialX }}" cy="{{ $financialIncomeY }}" r="3" fill="#55b995" stroke="white" stroke-width="1.5" class="financial-chart-point" data-index="{{ $i }}" />
                    @endforeach
                    <g class="financial-chart-hover" aria-hidden="true" style="display:none">
                        <line class="financial-hover-line" y1="{{ $financialChartPadTop }}" y2="{{ $financialChartPadTop + $financialPlotH }}" stroke="currentColor" stroke-dasharray="3 4" />
                        <circle class="financial-hover-revenue" r="4" fill="#ee765f" stroke="white" stroke-width="1.5" />
                        <circle class="financial-hover-income" r="4" fill="#55b995" stroke="white" stroke-width="1.5" />
                        <g class="financial-hover-popup">
                            <rect class="financial-hover-box" width="150" height="54" rx="10" fill="white" stroke="#e4e3dc" />
                            <text class="financial-hover-month" x="12" y="16" font-size="9" font-weight="700" letter-spacing="1.2" fill="#969791" />
                            <text class="financial-hover-revenue-text" x="12" y="35" font-size="10" fill="#d96650" />
                            <text class="financial-hover-income-text" x="12" y="50" font-size="10" fill="#3d9b78" />
                        </g>
                    </g>
                    <rect x="{{ $financialChartPadX }}" y="{{ $financialChartPadTop }}" width="{{ $financialPlotW }}" height="{{ $financialPlotH }}" fill="transparent" class="financial-chart-overlay" />
                </svg>
                <script>
                    (() => {
                        const chart = document.currentScript.previousElementSibling;
                        const overlay = chart.querySelector('.financial-chart-overlay');
                        const hover = chart.querySelector('.financial-chart-hover');
                        const points = @json($financialTrend->values());
                        const xs = points.map((_, i) => {{ $financialChartPadX }} + (i * {{ $financialStep }}));
                        const revenueYs = @json(array_map(fn($point) => $financialChartPadTop + $financialPlotH - (($point['revenue'] / $financialChartMax) * $financialPlotH), $financialTrend->all()));
                        const incomeYs = @json(array_map(fn($point) => $financialChartPadTop + $financialPlotH - ((max($point['netIncome'], 0) / $financialChartMax) * $financialPlotH), $financialTrend->all()));
                        const show = (index) => {
                            const x = xs[index];
                            const boxX = Math.min(Math.max(x - 75, {{ $financialChartPadX }}), {{ $financialChartW }} - 160);
                            const inverseScale = {{ $financialChartW }} / chart.getBoundingClientRect().width;
                            const popup = hover.querySelector('.financial-hover-popup');
                            hover.style.display = '';
                            hover.querySelector('.financial-hover-line').setAttribute('x1', x);
                            hover.querySelector('.financial-hover-line').setAttribute('x2', x);
                            hover.querySelector('.financial-hover-revenue').setAttribute('cx', x);
                            hover.querySelector('.financial-hover-revenue').setAttribute('cy', revenueYs[index]);
                            hover.querySelector('.financial-hover-income').setAttribute('cx', x);
                            hover.querySelector('.financial-hover-income').setAttribute('cy', incomeYs[index]);
                            const popupScale = Math.max(1, inverseScale);
                            popup.setAttribute('transform', `translate(${boxX / popupScale}, ${18 / popupScale}) scale(${popupScale})`);
                            hover.querySelector('.financial-hover-month').textContent = points[index].month.toUpperCase();
                            hover.querySelector('.financial-hover-revenue-text').textContent = `Revenue  ₱${Number(points[index].revenue).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                            hover.querySelector('.financial-hover-income-text').textContent = `Net income  ₱${Number(points[index].netIncome).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                            // Keep popup text tied to the popup group so it stays readable at every card width.
                        };
                        overlay.addEventListener('mousemove', (event) => {
                            const box = overlay.getBoundingClientRect();
                            const value = ((event.clientX - box.left) / box.width) * (xs[xs.length - 1] - xs[0]) + xs[0];
                            show(Math.max(0, Math.min(xs.length - 1, Math.round((value - xs[0]) / {{ $financialStep }}))));
                        });
                        overlay.addEventListener('mouseleave', () => { hover.style.display = 'none'; });
                    })();
                </script>
            @else
                <div class="flex h-24 items-center justify-center text-xs text-gray-400">No trend data yet.</div>
            @endif
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 px-5 pb-5 text-[11px] text-gray-500 dark:text-neutral-400">
            <span>Posted journal entries for the current month.</span>
            <a href="{{ route('accounting.income-statement.index', ['date_from' => $financialDateFrom, 'date_to' => $financialDateTo]) }}" class="inline-flex items-center gap-1 font-semibold text-gray-600 no-underline transition-colors hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white">View details <span aria-hidden="true">⌄</span></a>
        </div>
    </div>
</div>