@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Cash Flow Statement Report</h5>
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
                            <label for="as_of_date" class="me-2">To:</label>
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
                <h5>CASH FLOW STATEMENT</h5>
                <p class="text-muted">For the Period {{ isset($fromDate) ? $fromDate->format('F d, Y') : 'N/A' }} to {{ isset($asOfDate) ? $asOfDate->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="cashFlowTable">
                    <thead class="table-light">
                        <tr>
                            <th>Cash Flow Category</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- OPERATING ACTIVITIES --}}
                        <tr class="table-warning">
                            <th colspan="2">CASH FLOWS FROM OPERATING ACTIVITIES</th>
                        </tr>
                        <tr>
                            <td>Net Cash from Operations</td>
                            <td class="text-end">₱{{ number_format($operatingCashFlow, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Net Operating Cash Flow</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($operatingCashFlow, 2) }}</strong></td>
                        </tr>

                        {{-- INVESTING ACTIVITIES --}}
                        <tr class="table-warning mt-3">
                            <th colspan="2">CASH FLOWS FROM INVESTING ACTIVITIES</th>
                        </tr>
                        <tr>
                            <td>Asset Changes</td>
                            <td class="text-end">₱{{ number_format($investingCashFlow, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Net Investing Cash Flow</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($investingCashFlow, 2) }}</strong></td>
                        </tr>

                        {{-- FINANCING ACTIVITIES --}}
                        <tr class="table-warning mt-3">
                            <th colspan="2">CASH FLOWS FROM FINANCING ACTIVITIES</th>
                        </tr>
                        <tr>
                            <td>Liability Changes</td>
                            <td class="text-end">₱{{ number_format($financingCashFlow, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Net Financing Cash Flow</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($financingCashFlow, 2) }}</strong></td>
                        </tr>

                        {{-- NET CHANGE IN CASH --}}
                        <tr class="table-light border-top-3">
                            <td><strong>NET CHANGE IN CASH</strong></td>
                            <td class="text-end"><strong class="text-{{ $netCashFlow >= 0 ? 'success' : 'danger' }}">₱{{ number_format($netCashFlow, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="alert alert-info" role="alert">
                        <strong>Cash Flow Summary</strong>
                        <ul class="mb-0 mt-2">
                            <li>Operating: ₱{{ number_format($operatingCashFlow, 2) }}</li>
                            <li>Investing: ₱{{ number_format($investingCashFlow, 2) }}</li>
                            <li>Financing: ₱{{ number_format($financingCashFlow, 2) }}</li>
                            <li><strong>Net Flow: ₱{{ number_format($netCashFlow, 2) }}</strong></li>
                        </ul>
                    </div>
                </div>
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
        const table = document.getElementById('cashFlowTable');
        let csv = 'Category,Amount\n';

        table.querySelectorAll('tbody tr').forEach(row => {
            let cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                let category = cells[0].textContent.trim();
                let amount = cells[1].textContent.trim();
                csv += `"${category}","${amount}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'cash-flow-statement-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
@endsection
