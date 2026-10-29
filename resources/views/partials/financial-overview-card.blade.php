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
                    $financialChartW = 600; $financialChartH = 150; $financialChartPadX = 10; $financialChartPadTop = 10; $financialChartPadBottom = 24;
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
                    <line x1="{{ $financialChartPadX }}" y1="{{ $financialChartPadTop + $financialPlotH }}" x2="{{ $financialChartW - $financialChartPadX }}" y2="{{ $financialChartPadTop + $financialPlotH }}" stroke="currentColor" class="text-gray-200 dark:text-neutral-700" />
                    <path d="{{ $revenuePath }}" fill="none" stroke="#ee765f" stroke-width="2.5" stroke-linecap="round" />
                    <path d="{{ $incomePath }}" fill="none" stroke="#55b995" stroke-width="2.5" stroke-linecap="round" />
                    @foreach($financialTrend as $i => $point)
                        <text x="{{ $financialChartPadX + ($i * $financialStep) }}" y="{{ $financialChartH - 6 }}" text-anchor="middle" fill="currentColor" class="text-gray-400 dark:text-neutral-500" font-size="10">{{ $point['month'] }}</text>
                    @endforeach
                </svg>
            @else
                <div class="flex h-24 items-center justify-center text-xs text-gray-400">No trend data yet.</div>
            @endif
        </div>
        <div class="mt-4 flex items-center justify-between gap-3">
            <p class="text-[11px] text-gray-400 dark:text-neutral-500">Posted journal entries for the current month.</p>
            <a href="{{ route('accounting.income-statement.index', ['date_from' => $financialDateFrom, 'date_to' => $financialDateTo]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 dark:bg-neutral-100 px-3 py-2 text-[11px] font-semibold text-white dark:text-neutral-900 no-underline transition-colors hover:bg-gray-700 dark:hover:bg-neutral-300">View details</a>
        </div>
    </div>
</div>