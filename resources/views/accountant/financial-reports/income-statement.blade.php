@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Income Statement Report</h5>
                <div>
                    <button onclick="printReport()" class="btn btn-secondary btn-sm">
                        <i class="feather-printer"></i> Print
                    </button>
                    <button onclick="exportExcel()" class="btn btn-success btn-sm">
                        <i class="feather-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Date Filter Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" class="form-inline" id="filterForm">
                        <div class="form-group me-3">
                            <label for="from_date" class="me-2">From:</label>
                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" 
                                   value="{{ isset($fromDate) ? $fromDate->format('Y-m-d') : now()->startOfYear()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group me-3">
                            <label for="as_of_date" class="me-2">As Of:</label>
                            <input type="date" name="as_of_date" id="as_of_date" class="form-control form-control-sm" 
                                   value="{{ isset($asOfDate) ? $asOfDate->format('Y-m-d') : now()->format('Y-m-d') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mb-4">
                <h5>INCOME STATEMENT</h5>
                <p class="text-muted">For the Period {{ isset($fromDate) ? $fromDate->format('F d, Y') : 'N/A' }} to {{ isset($asOfDate) ? $asOfDate->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="incomeStatementTable">
                    <thead class="table-light">
                        <tr>
                            <th>Account Code</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">% of Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- REVENUES --}}
                        <tr class="table-warning">
                            <th colspan="4">REVENUES</th>
                        </tr>
                        @forelse($revenueDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td style="padding-left: 2rem;">{{ $item['account']->name }}</td>
                                <td class="text-end">₱{{ number_format($item['amount'], 2) }}</td>
                                <td class="text-end">{{ $totalRevenue != 0 ? number_format(($item['amount'] / $totalRevenue) * 100, 2) : '0.00' }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No revenue accounts found for this period</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="2"><strong>Total Revenues</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalRevenue, 2) }}</strong></td>
                            <td class="text-end"><strong>100.00%</strong></td>
                        </tr>

                        {{-- EXPENSES --}}
                        <tr class="table-warning">
                            <th colspan="4">EXPENSES</th>
                        </tr>
                        @forelse($expenseDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td style="padding-left: 2rem;">{{ $item['account']->name }}</td>
                                <td class="text-end">₱{{ number_format($item['amount'], 2) }}</td>
                                <td class="text-end">{{ $totalRevenue != 0 ? number_format(($item['amount'] / $totalRevenue) * 100, 2) : '0.00' }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No expense accounts found for this period</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="2"><strong>Total Expenses</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalExpenses, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ $totalRevenue != 0 ? number_format(($totalExpenses / $totalRevenue) * 100, 2) : '0.00' }}%</strong></td>
                        </tr>

                        {{-- NET INCOME --}}
                        <tr class="table-light border-top-3">
                            <td colspan="2"><strong>NET INCOME (LOSS)</strong></td>
                            <td class="text-end"><strong class="text-{{ $netIncome >= 0 ? 'success' : 'danger' }}">₱{{ number_format($netIncome, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($netIncomePercent, 2) }}%</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printReport() {
        window.print();
    }

    function exportExcel() {
        const table = document.getElementById('incomeStatementTable');
        let csv = 'Description,Amount,%\n';

        table.querySelectorAll('tbody tr').forEach(row => {
            let cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                let description = cells[0].textContent.trim();
                let amount = cells[1].textContent.trim();
                let percentage = cells[2] ? cells[2].textContent.trim() : '';
                csv += `"${description}","${amount}","${percentage}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'income-statement-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
@endsection
