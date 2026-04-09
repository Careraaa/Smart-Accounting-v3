@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Balance Sheet Report</h5>
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
                <h5>BALANCE SHEET</h5>
                <p class="text-muted">As of {{ isset($asOfDate) ? $asOfDate->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="balanceSheetTable">
                    <thead class="table-light">
                        <tr>
                            <th>Account Code</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- ASSETS --}}
                        <tr class="table-warning">
                            <th colspan="3">ASSETS</th>
                        </tr>
                        @forelse($assetDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td style="padding-left: 2rem;">{{ $item['account']->name }}</td>
                                <td class="text-end">₱{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No asset accounts found</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="2"><strong>Total Assets</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalAssets, 2) }}</strong></td>
                        </tr>

                        {{-- LIABILITIES --}}
                        <tr class="table-warning">
                            <th colspan="3">LIABILITIES</th>
                        </tr>
                        @forelse($liabilityDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td style="padding-left: 2rem;">{{ $item['account']->name }}</td>
                                <td class="text-end">₱{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No liability accounts found</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="2"><strong>Total Liabilities</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalLiabilities, 2) }}</strong></td>
                        </tr>

                        {{-- EQUITY --}}
                        <tr class="table-warning">
                            <th colspan="3">EQUITY</th>
                        </tr>
                        @forelse($equityDetails as $item)
                            <tr>
                                <td>{{ $item['account']->code }}</td>
                                <td style="padding-left: 2rem;">{{ $item['account']->name }}</td>
                                <td class="text-end">₱{{ number_format($item['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No equity accounts found</td>
                            </tr>
                        @endforelse
                        <tr class="table-light">
                            <td colspan="2"><strong>Total Equity</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalEquity, 2) }}</strong></td>
                        </tr>

                        {{-- TOTALS --}}
                        <tr class="table-light border-top-3">
                            <td colspan="2"><strong>TOTAL LIABILITIES & EQUITY</strong></td>
                            <td class="text-end"><strong>₱{{ number_format($totalLiabilities + $totalEquity, 2) }}</strong></td>
                        </tr>

                        @php
                            $difference = $totalAssets - ($totalLiabilities + $totalEquity);
                        @endphp
                        <tr class="table-{{ abs($difference) < 0.01 ? 'success' : 'danger' }}">
                            <td colspan="3" class="text-center">
                                @if(abs($difference) < 0.01)
                                    <span class="badge bg-success">✓ Balance Sheet in Balance</span>
                                @else
                                    <span class="badge bg-danger">✗ Out of Balance - Difference: ₱{{ number_format(abs($difference), 2) }}</span>
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
        const table = document.getElementById('balanceSheetTable');
        let csv = 'Description,Amount\n';

        table.querySelectorAll('tbody tr').forEach(row => {
            let cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                let description = cells[0].textContent.trim();
                let amount = cells[1] ? cells[1].textContent.trim() : '';
                csv += `"${description}","${amount}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'balance-sheet-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
    }
</script>
@endpush
@endsection
