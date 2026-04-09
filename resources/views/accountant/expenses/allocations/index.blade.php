@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Expense Allocations</h5>
            <a href="{{ route('accountant.expenses.allocations.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus"></i> Add Allocation
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Journal Entry Line</th>
                            <th>Cost Center</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Percentage</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allocations as $allocation)
                            <tr>
                                <td>
                                    @if($allocation->journalEntryLine)
                                        JE Line #{{ $allocation->journalEntryLine->id }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($allocation->costCenter)
                                        <strong>{{ $allocation->costCenter->name }}</strong>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">₱{{ number_format($allocation->allocated_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($allocation->allocation_percentage, 2) }}%</td>
                                <td>
                                    <small>{{ $allocation->notes ?: '—' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('accountant.expenses.allocations.show', $allocation) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('accountant.expenses.allocations.edit', $allocation) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <form action="{{ route('accountant.expenses.allocations.destroy', $allocation) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this allocation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="feather-inbox d-block mb-2" style="font-size:2rem; opacity:.3;"></i>
                                    No expense allocations found. <a href="{{ route('accountant.expenses.allocations.create') }}">Create one now</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($allocations->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $allocations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
