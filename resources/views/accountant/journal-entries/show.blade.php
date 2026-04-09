@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Journal Entry JE-{{ str_pad($entry->id, 6, '0', STR_PAD_LEFT) }}
                </h5>
                <a href="{{ route('accountant.journal-entries.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Entry Date</p>
                    <h5>{{ $entry->je_date->format('M d, Y') }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <h5>
                        <span class="badge bg-{{ statusColor($entry->status) }}">
                            {{ $entry->status }}
                        </span>
                    </h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Created By</p>
                    <h5>{{ $entry->createdBy->name ?? 'N/A' }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Created Date</p>
                    <h5>{{ $entry->created_at->format('M d, Y') }}</h5>
                </div>
            </div>

            <hr>

            <div class="mb-4">
                <h6 class="mb-2">Description</h6>
                <p>{{ $entry->description }}</p>
            </div>

            <hr>

            <h6 class="mb-3">Journal Entry Lines</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>GL Account</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Cost Center</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entry->journalEntryLines as $line)
                            <tr>
                                <td>
                                    <strong>{{ $line->glAccount->code }}</strong> - {{ $line->glAccount->name }}
                                </td>
                                <td class="text-end">
                                    {{ $line->debit_amount > 0 ? '₱' . number_format($line->debit_amount, 2) : '-' }}
                                </td>
                                <td class="text-end">
                                    {{ $line->credit_amount > 0 ? '₱' . number_format($line->credit_amount, 2) : '-' }}
                                </td>
                                <td>
                                    {{ $line->costCenter->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No lines in this entry.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end">
                                <strong>₱{{ number_format($entry->journalEntryLines->sum('debit_amount'), 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <strong>₱{{ number_format($entry->journalEntryLines->sum('credit_amount'), 2) }}</strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <hr>

            @if($entry->status === 'Draft')
                <div class="alert alert-info mb-3">
                    <strong>Draft Entry:</strong> This entry can still be edited or submitted for approval.
                </div>
            @elseif($entry->status === 'Pending')
                <div class="alert alert-warning mb-3">
                    <strong>Pending Approval:</strong> Waiting for supervisor approval.
                </div>
            @elseif($entry->status === 'Approved')
                <div class="alert alert-success mb-3">
                    <strong>Approved:</strong> Entry has been approved and is ready to be posted.
                </div>
            @elseif($entry->status === 'Posted')
                <div class="alert alert-success mb-3">
                    <strong>Posted:</strong> Entry has been posted to the GL.
                    <br>
                    <small>Posted by: {{ $entry->postedBy->name ?? 'N/A' }} on {{ $entry->posted_at?->format('M d, Y H:i') }}</small>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap">
                @if($entry->status === 'Draft')
                    <a href="{{ route('accountant.journal-entries.edit', $entry) }}" class="btn btn-warning">
                        <i class="feather-edit-2"></i> Edit
                    </a>
                    <form action="{{ route('accountant.journal-entries.submit', $entry) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-send"></i> Submit for Approval
                        </button>
                    </form>
                @endif

                @if($entry->status === 'Pending' && auth()->user()->hasRole('supervisor'))
                    <form action="{{ route('accountant.journal-entries.approve', $entry) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check-circle"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('accountant.journal-entries.reject', $entry) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="feather-x-circle"></i> Reject
                        </button>
                    </form>
                @endif

                @if($entry->status === 'Approved')
                    <form action="{{ route('accountant.journal-entries.post', $entry) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="feather-upload"></i> Post to GL
                        </button>
                    </form>
                @endif

                @if($entry->status === 'Posted')
                    <form action="{{ route('accountant.journal-entries.reversal', $entry) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Create reversal entry?')">
                            <i class="feather-rotate-ccw"></i> Create Reversal
                        </button>
                    </form>
                @endif

                <button onclick="printEntry()" class="btn btn-secondary">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printEntry() {
        window.print();
    }
</script>
@endpush
@endsection
