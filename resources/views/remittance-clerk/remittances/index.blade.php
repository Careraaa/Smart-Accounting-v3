@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Daily Remittances</span>
            <a href="{{ route('remittances.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Remittance
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th class="text-end">Net Remittance</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances as $remittance)
                            <tr>
                                <td class="text-muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                                <td><strong>{{ $remittance->route->route_name }}</strong></td>
                                <td>{{ $remittance->vehicle->plate_number }}</td>
                                <td class="text-end">₱{{ number_format($remittance->net_remittance, 2) }}</td>
                                <td class="text-center">
                                    @if ($remittance->status === 'approved')
                                        <span class="emp-badge emp-badge-approved">Approved</span>
                                    @elseif ($remittance->status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('remittances.show', $remittance) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('remittances.edit', $remittance) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('remittances.destroy', $remittance) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this remittance?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="feather-file-text d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No remittances found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.emp-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 3px 10px;
    border-radius: 20px;
}
.emp-badge-active   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.emp-badge-inactive { background: #fff5f5; color: #c8292a; border: 1px solid #fcd0d0; }
.emp-badge-pending  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.emp-badge-approved { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
.emp-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: #f4f5f7;
    border: none;
    color: #9898a8;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.13s, color 0.13s;
    padding: 0;
}
.emp-action-btn.emp-action-view:hover   { background: #eff6ff; color: #3b82f6; }
.emp-action-btn.emp-action-edit:hover   { background: #fffbeb; color: #d97706; }
.emp-action-btn.emp-action-danger:hover { background: #fff1f2; color: #e11d48; }
</style>
@endsection