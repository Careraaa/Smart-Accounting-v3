@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="card-title mb-0">Payroll Reports</span>
                <p class="text-muted small mt-1 mb-0">Approved payroll batches report</p>
            </div>
            <a href="{{ route('reports.print.payroll-report') }}" class="btn btn-sm btn-primary" target="_blank">
                <i class="feather-printer me-1"></i> Print
            </a>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <div class="row mb-4 pb-3 border-bottom">
                <div class="col-md-3">
                    <label class="form-label">Period</label>
                    <select id="period" class="form-select" onchange="updateReport()">
                        <option value="weekly"  {{ $period === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly"  {{ $period === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="col-md-3" id="weekSelectWrap" style="display: {{ $period === 'weekly' ? 'block' : 'none' }};">
                    <label class="form-label">Week Number</label>
                    <select id="week" class="form-select" onchange="updateReport()">
                        @for ($i = 1; $i <= 52; $i++)
                            <option value="{{ $i }}" {{ $week == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3" id="monthSelectWrap" style="display: {{ $period === 'monthly' ? 'block' : 'none' }};">
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
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Batches</small>
                            <h5 class="mb-0 mt-2" style="color: #8B3A62;">{{ count($batchData) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Gross</small>
                            <h5 class="mb-0 mt-2" style="color: #0369a1;">₱{{ number_format(collect($batchData)->sum('total_gross'), 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Deductions</small>
                            <h5 class="mb-0 mt-2" style="color: #ea580c;">₱{{ number_format(collect($batchData)->sum('total_deductions'), 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Net Pay</small>
                            <h5 class="mb-0 mt-2" style="color: #16a34a;">₱{{ number_format(collect($batchData)->sum('total_net'), 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th class="text-end">Employees</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batchData as $batch)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($batch['period_start'])->format('M d') }} –
                                    {{ \Carbon\Carbon::parse($batch['period_end'])->format('M d, Y') }}
                                </td>
                                <td class="text-end">{{ $batch['count'] }}</td>
                                <td class="text-end">₱{{ number_format($batch['total_gross'], 2) }}</td>
                                <td class="text-end text-muted">₱{{ number_format($batch['total_deductions'], 2) }}</td>
                                <td class="text-end">
                                    <strong style="color: #16a34a;">₱{{ number_format($batch['total_net'], 2) }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No approved payroll batches found for this period
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    const periodSelect = document.getElementById('period');

    function updateReport() {
        const period = periodSelect.value;
        const week  = document.getElementById('week').value;
        const month = document.getElementById('month').value;
        const year  = document.getElementById('year').value;

        let url = '{{ route("reports.payroll") }}?period=' + period + '&year=' + year;
        if (period === 'weekly')  url += '&week='  + week;
        if (period === 'monthly') url += '&month=' + month;

        window.location.href = url;
    }

    function toggleFilters() {
        const period = periodSelect.value;
        document.getElementById('weekSelectWrap').style.display  = period === 'weekly'  ? 'block' : 'none';
        document.getElementById('monthSelectWrap').style.display = period === 'monthly' ? 'block' : 'none';
    }

    periodSelect.addEventListener('change', toggleFilters);
</script>
@endsection