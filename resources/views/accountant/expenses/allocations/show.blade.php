@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Expense Allocation</h5>
                <div>
                    <a href="{{ route('accountant.expenses.allocations.edit', $allocation) }}" class="btn btn-warning btn-sm">
                        <i class="feather-edit"></i> Edit
                    </a>
                    <a href="{{ route('accountant.expenses.allocations.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Allocated Amount</p>
                    <h5>₱{{ number_format($allocation->allocated_amount, 2) }}</h5>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Allocation Percentage</p>
                    <h5>{{ number_format($allocation->allocation_percentage, 2) }}%</h5>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Cost Center</p>
                    <h5>{{ $allocation->costCenter->cost_center_name }}</h5>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Details</h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Journal Entry Line:</strong>
                        <a href="{{ route('accountant.journal-entries.show', $allocation->journalEntryLine->journalEntry) }}">
                            JE-{{ str_pad($allocation->journalEntryLine->journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                    </p>
                    <p class="mb-2">
                        <strong>GL Account:</strong> {{ $allocation->journalEntryLine->glAccount->account_code }} - {{ $allocation->journalEntryLine->glAccount->account_name }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Cost Center Code:</strong> {{ $allocation->costCenter->cost_center_code }}
                    </p>
                    <p class="mb-2">
                        <strong>Created:</strong> {{ $allocation->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>

            @if($allocation->notes)
                <hr>
                <h6 class="mb-3">Notes</h6>
                <p>{{ $allocation->notes }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
