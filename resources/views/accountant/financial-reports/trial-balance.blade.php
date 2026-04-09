@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Trial Balance Report</span>
                <div>
                    <button onclick="printReport()" class="btn btn-secondary btn-sm">
                        <i class="feather-printer"></i> Print
                    </button>
                </div>

        </div>
        <div class="card-body">
            <!-- Date Filter Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" class="form-inline" id="filterForm">
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
                <h5>TRIAL BALANCE</h5>
                <p class="text-muted">As of {{ isset($asOfDate) ? $asOfDate->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="trialBalanceTable">
                    <thead class="table-light">
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trialBalanceDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td>{{ $item['account']->name }}</td>
                                <td class="text-end">{{ $item['debit'] > 0 ? '₱' . number_format($item['debit'], 2) : '' }}</td>
                                <td class="text-end">{{ $item['credit'] > 0 ? '₱' . number_format($item['credit'], 2) : '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No accounts found</td>
                            </tr>
                        @endforelse
                        
                        <tr class="table-light border-top-3">
                            <th colspan="2"><strong>TOTALS</strong></th>
                            <td class="text-end"><strong>₱{{ number_format($totalDebits, 2) }}</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalCredits, 2) }}</strong></td>
                        </tr>

                        <tr class="table-{{ $isBalanced ? 'success' : 'danger' }}">
                            <td colspan="4" class="text-center">
                                @if($isBalanced)
                                    <span class="badge bg-success">✓ Trial Balance in Balance</span>
                                @else
                                    <span class="badge bg-danger">✗ Out of Balance - Difference: ₱{{ number_format(abs($totalDebits - $totalCredits), 2) }}</span>
                                @endif
                            </td>
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
        const table = document.getElementById('trialBalanceTable');
        let csv = 'Account Code,Account Name,Debit,Credit\n';

        table.querySelectorAll('tbody tr').forEach(row => {
            let cells = row.querySelectorAll('td');
            if (cells.length >= 4 && !row.classList.contains('table-light') && !row.classList.contains('table-success') && !row.classList.contains('table-danger')) {
                let code = cells[0].textContent.trim();
                let name = cells[1].textContent.trim();
                let debit = cells[2].textContent.trim();
                let credit = cells[3].textContent.trim();
                csv += `"${code}","${name}","${debit}","${credit}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'trial-balance-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
@endsection
