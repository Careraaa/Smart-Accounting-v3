@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Remittance Report</h5>
                <p class="remui-subtitle mb-0">Filter weekly/monthly/yearly performance and print summaries.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.print.remittance-report', ['period' => $period, 'week' => $week, 'month' => $month, 'year' => $year]) }}" class="emp-action-btn emp-action-view" target="_blank">
                    <i class="feather-printer"></i><span>Print</span>
                </a>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Report</span>
            </div>
            <div class="card-body">
            {{-- Filter Section --}}
            <div class="row mb-4 pb-3 border-bottom">
                <div class="col-md-3">
                    <label class="form-label">Period</label>
                    <select id="period" class="form-select" onchange="updateReport()">
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="col-md-3" id="weekSelect" style="display: {{ $period === 'weekly' ? 'block' : 'none' }};">
                    <label class="form-label">Week Number</label>
                    <select id="week" class="form-select" onchange="updateReport()">
                        @for ($i = 1; $i <= 52; $i++)
                            <option value="{{ $i }}" {{ $week == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3" id="monthSelect" style="display: {{ $period === 'monthly' ? 'block' : 'none' }};">
                    <label class="form-label">Month</label>
                    <select id="month" class="form-select" onchange="updateReport()">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select id="year" class="form-select" onchange="updateReport()">
                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Statistics Section --}}
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Collection</small>
                            <h5 class="mb-0 mt-2" style="color: #16a34a;">₱{{ number_format($totalCollection, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Expenses</small>
                            <h5 class="mb-0 mt-2" style="color: #ea580c;">₱{{ number_format($totalExpenses, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Net Remittance</small>
                            <h5 class="mb-0 mt-2" style="color: #0369a1;">₱{{ number_format($totalNetRemittance, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Short Remittances</small>
                            <h5 class="mb-0 mt-2" style="color: #dc2626;">{{ $shortRemittances }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Remittances Table --}}
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Collection</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-end">Net Remittance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedRemittances = $remittances->groupBy(function($item) {
                                return $item->remittance_date->format('Y-m-d');
                            })->map(function($group) {
                                return [
                                    'remittance_date' => $group->first()->remittance_date,
                                    'total_collection' => $group->sum('total_collection'),
                                    'total_expenses' => $group->sum('total_expenses'),
                                    'net_remittance' => $group->sum('net_remittance'),
                                    'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
                                ];
                            })->sortBy('remittance_date')->values();
                        @endphp
                        @forelse($groupedRemittances as $remittance)
                            <tr>
                                <td>{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                                <td class="text-end">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                                <td class="text-end">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                                <td class="text-end">
                                    @if ($remittance['is_short_remittance'])
                                        <strong style="color: #dc2626;">₱{{ number_format($remittance['net_remittance'], 2) }}</strong>
                                    @else
                                        <strong style="color: #16a34a;">₱{{ number_format($remittance['net_remittance'], 2) }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No remittances found for this period
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    const periodSelect = document.getElementById('period');
    const weekSelect = document.getElementById('weekSelect');
    const monthSelect = document.getElementById('monthSelect');

    function updateReport() {
        const period = periodSelect.value;
        const week = document.getElementById('week').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        let url = '{{ route("reports.remittance-report") }}?period=' + period + '&year=' + year;
        
        if (period === 'weekly') {
            url += '&week=' + week;
        } else if (period === 'monthly') {
            url += '&month=' + month;
        }

        window.location.href = url;
    }

    function toggleFilters() {
        const period = periodSelect.value;
        weekSelect.style.display = period === 'weekly' ? 'block' : 'none';
        monthSelect.style.display = period === 'monthly' ? 'block' : 'none';
    }

    periodSelect.addEventListener('change', toggleFilters);
</script>
@endsection
