@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">GL Ledger - {{ $account->account_code }} {{ $account->account_name }}</h5>
                <a href="{{ route('accountant.gl.show', $account) }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" id="fromDate" class="form-control" value="{{ old('from_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" id="toDate" class="form-control" value="{{ old('to_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button onclick="filterLedger()" class="btn btn-primary w-100">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button onclick="printLedger()" class="btn btn-secondary w-100">
                        <i class="feather-printer"></i> Print
                    </button>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Journal Entry</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-light">
                            <td colspan="3"><strong>Opening Balance</strong></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"><strong>₱{{ number_format($account->opening_balance, 2) }}</strong></td>
                        </tr>
                        @php
                            $runningBalance = $account->opening_balance;
                        @endphp
                        @forelse($entries as $entry)
                            @php
                                if ($account->account_type === 'Asset') {
                                    $runningBalance += $entry->debit_amount - $entry->credit_amount;
                                } else {
                                    $runningBalance -= $entry->debit_amount - $entry->credit_amount;
                                }
                            @endphp
                            <tr>
                                <td>{{ $entry->journalEntry->je_date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('accountant.journal-entries.show', $entry->journalEntry) }}">
                                        JE-{{ str_pad($entry->journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td>{{ $entry->journalEntry->description }}</td>
                                <td class="text-end">{{ $entry->debit_amount > 0 ? '₱' . number_format($entry->debit_amount, 2) : '-' }}</td>
                                <td class="text-end">{{ $entry->credit_amount > 0 ? '₱' . number_format($entry->credit_amount, 2) : '-' }}</td>
                                <td class="text-end"><strong>₱{{ number_format($runningBalance, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No transactions in this date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterLedger() {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const accountId = '{{ $account->id }}';

        if (!fromDate || !toDate) {
            alert('Please select both dates');
            return;
        }

        window.location.href = `{{ route('accountant.gl.ledger', $account) }}?from_date=${fromDate}&to_date=${toDate}`;
    }

    function printLedger() {
        window.print();
    }
</script>
@endpush
@endsection
