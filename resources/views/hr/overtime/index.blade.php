@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Overtime / Undertime Management</span>
            <a href="{{ route('overtime.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> New Record
            </a>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="fs-12 fw-medium text-muted mb-2">Total Records</div>
                            <h3 class="mb-0">{{ $totalRecords }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="fs-12 fw-medium text-muted mb-2" style="color: #3b82f6;">Overtime</div>
                            <h3 class="mb-0" style="color: #3b82f6;">{{ $overtimeCount }}</h3>
                            <small class="text-muted">{{ number_format($totalOvertimeHours, 2) }} hrs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="fs-12 fw-medium text-muted mb-2" style="color: #f59e0b;">Undertime</div>
                            <h3 class="mb-0" style="color: #f59e0b;">{{ $undertimeCount }}</h3>
                            <small class="text-muted">{{ number_format($totalUndertimeHours, 2) }} hrs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="fs-12 fw-medium text-muted mb-2" style="color: #d97706;">Pending</div>
                            <h3 class="mb-0" style="color: #d97706;">{{ $pendingRecords }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('overtime.index') }}" class="d-flex gap-2 flex-wrap">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by employee..." value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
                        <select name="type" class="form-control form-control-sm" style="max-width: 120px;">
                            <option value="all">All Types</option>
                            <option value="overtime" {{ request('type') === 'overtime' ? 'selected' : '' }}>Overtime</option>
                            <option value="undertime" {{ request('type') === 'undertime' ? 'selected' : '' }}>Undertime</option>
                        </select>
                        <select name="status" class="form-control form-control-sm" style="max-width: 130px;">
                            <option value="all">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('overtime.index', array_merge(request()->all(), ['sort_by' => 'user_id', 'sort_order' => ($sortBy === 'user_id' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">
                                    Employee
                                    @if($sortBy === 'user_id')
                                        <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1" style="font-size: 0.875rem;"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('overtime.index', array_merge(request()->all(), ['sort_by' => 'date', 'sort_order' => ($sortBy === 'date' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">
                                    Date
                                    @if($sortBy === 'date')
                                        <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1" style="font-size: 0.875rem;"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Type</th>
                            <th>Hours</th>
                            <th>
                                <a href="{{ route('overtime.index', array_merge(request()->all(), ['sort_by' => 'status', 'sort_order' => ($sortBy === 'status' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">
                                    Status
                                    @if($sortBy === 'status')
                                        <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1" style="font-size: 0.875rem;"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimeRecords as $record)
                            <tr>
                                <td><strong>{{ $record->employee->first_name ?? 'N/A' }} {{ $record->employee->last_name ?? '' }}</strong></td>
                                <td class="text-muted" style="font-size:.82rem;">
                                    {{ $record->date->format('M d, Y') }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $typeColor = $record->type === 'overtime' ? '#3b82f6' : '#f59e0b';
                                        $typeBg = $record->type === 'overtime' ? '#dbeafe' : '#fef3c7';
                                    @endphp
                                    <span style="display:inline-block; background:{{ $typeBg }}; color:{{ $typeColor }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($record->type) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($record->hours, 2) }} hrs</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'approved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$record->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('overtime.show', $record) }}" class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('overtime.edit', $record) }}" class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('overtime.destroy', $record) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Delete this record?')">
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
                                    No overtime/undertime records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted" style="font-size: 0.875rem;">
                    Showing {{ $overtimeRecords->firstItem() ?? 0 }} to {{ $overtimeRecords->lastItem() ?? 0 }} of {{ $overtimeRecords->total() }} entries
                </div>
                <div>
                    {{ $overtimeRecords->render() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-statistic {
        border: 1px solid #e5e7eb;
        box-shadow: none;
    }

    .emp-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .emp-action-view {
        background: #f3f4f6;
        color: #6b7280;
    }

    .emp-action-view:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .emp-action-edit {
        background: #eff6ff;
        color: #3b82f6;
    }

    .emp-action-edit:hover {
        background: #dbeafe;
        color: #1e40af;
    }

    .emp-action-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .emp-action-danger:hover {
        background: #fecaca;
        color: #b91c1c;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/HR/overtime-management.js') }}"></script>
@endsection
