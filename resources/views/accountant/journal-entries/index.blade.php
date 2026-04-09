@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Journal Entries</span>
                <a href="{{ route('accountant.journal-entries.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus"></i> New Entry
                </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control" placeholder="Search by description...">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="Draft">Draft</option>
                        <option value="Pending">Pending Approval</option>
                        <option value="Approved">Approved</option>
                        <option value="Posted">Posted</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" id="dateFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <button onclick="filterEntries()" class="btn btn-primary w-100">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Entry #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Total Debit</th>
                            <th>Total Credit</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>
                                    <strong>JE-{{ str_pad($entry->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </td>
                                <td>{{ $entry->je_date->format('M d, Y') }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>₱{{ number_format($entry->journalEntryLines->sum('debit_amount'), 2) }}</td>
                                <td>₱{{ number_format($entry->journalEntryLines->sum('credit_amount'), 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $this->statusColor($entry->status) }}">
                                        {{ $entry->status }}
                                    </span>
                                </td>
                                <td>{{ $entry->createdBy->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('accountant.journal-entries.show', $entry) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="feather-eye"></i>
                                    </a>
                                    @if($entry->status === 'Draft')
                                        <a href="{{ route('accountant.journal-entries.edit', $entry) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    @endif
                                    @if($entry->status === 'Draft')
                                        <form action="{{ route('accountant.journal-entries.submit', $entry) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Submit for Approval">
                                                <i class="feather-send"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No journal entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function statusColor(status) {
        const colors = {
            'Draft': 'secondary',
            'Pending': 'warning',
            'Approved': 'info',
            'Posted': 'success',
            'Rejected': 'danger'
        };
        return colors[status] || 'secondary';
    }

    function filterEntries() {
        // Implement filtering logic
        alert('Filter functionality to be implemented');
    }
</script>
@endpush
@endsection
