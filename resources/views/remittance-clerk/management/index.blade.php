@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Manage Vehicles</span>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Vehicle
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Plate Number</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Operator</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td><strong>{{ $vehicle->plate_number }}</strong></td>
                                <td class="text-muted">{{ $vehicle->route->origin ?? 'N/A' }}</td>
                                <td class="text-muted">{{ $vehicle->route->destination ?? 'N/A' }}</td>
                                <td>{{ $vehicle->operator }}</td>
                                <td class="text-center">
                                    @php $status = strtolower($vehicle->status ?? 'active'); @endphp
                                    @if ($status === 'active')
                                        <span class="emp-badge emp-badge-active">Active</span>
                                    @elseif ($status === 'pending')
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('vehicles.show', $vehicle) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Delete this vehicle?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="feather-truck d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No vehicles found
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