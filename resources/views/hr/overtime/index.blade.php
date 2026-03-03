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
                                <div class="stat-label">Total Records</div>
                                <h3 class="mb-0">{{ $totalRecords }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#0284c7;">Overtime</div>
                                <h3 class="mb-0" style="color:#0284c7;">{{ $overtimeCount }}</h3>
                                <small class="text-muted">{{ number_format($totalOvertimeHours, 2) }} hrs</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#d97706;">Undertime</div>
                                <h3 class="mb-0" style="color:#d97706;">{{ $undertimeCount }}</h3>
                                <small class="text-muted">{{ number_format($totalUndertimeHours, 2) }} hrs</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-statistic">
                            <div class="card-body">
                                <div class="stat-label" style="color:#e11d48;">Pending</div>
                                <h3 class="mb-0" style="color:#e11d48;">{{ $pendingRecords }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('overtime.index') }}" class="d-flex gap-2 flex-wrap mb-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by employee..." value="{{ request('search') }}"
                        style="flex:1; min-width:200px;">
                    <select name="type" class="form-control form-control-sm" style="max-width:130px;">
                        <option value="all">All Types</option>
                        <option value="overtime" {{ request('type') === 'overtime' ? 'selected' : '' }}>Overtime</option>
                        <option value="undertime" {{ request('type') === 'undertime' ? 'selected' : '' }}>Undertime
                        </option>
                    </select>
                    <select name="status" class="form-control form-control-sm" style="max-width:130px;">
                        <option value="all">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                </form>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                @php
                                    $cols = [
                                        'user_id' => 'Employee',
                                        'date' => 'Date',
                                        'type' => 'Type',
                                        'hours' => 'Hours',
                                        'status' => 'Status',
                                    ];
                                @endphp
                                @foreach ($cols as $col => $label)
                                    <th class="sortable-header">
                                        <a href="{{ route('overtime.index', array_merge(request()->all(), ['sort_by' => $col, 'sort_order' => $sortBy === $col && $sortOrder === 'asc' ? 'desc' : 'asc'])) }}"
                                            class="sort-link">
                                            {{ $label }}
                                            @if ($sortBy === $col)
                                                <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1"
                                                    style="font-size:0.875rem;"></i>
                                            @else
                                                <i class="feather-arrow-up-down ms-1"
                                                    style="font-size:0.875rem; opacity:.3;"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                                <th class="sortable-header text-center">
                                    <div class="sort-link justify-content-center">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overtimeRecords as $record)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $record->employee->first_name ?? 'N/A' }}
                                            {{ $record->employee->last_name ?? '' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $record->date->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        @php $isOT = $record->type === 'overtime'; @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $isOT ? '#f0f9ff' : '#fffbeb' }};
                                               color:{{ $isOT ? '#0284c7' : '#d97706' }};
                                               border:1px solid {{ $isOT ? '#bae6fd' : '#fde68a' }};">
                                            {{ ucfirst($record->type) }}
                                        </span>
                                    </td>
                                    <td><strong>{{ number_format($record->hours, 2) }} hrs</strong></td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending' => [
                                                    'bg' => '#fffbeb',
                                                    'color' => '#d97706',
                                                    'border' => '#fde68a',
                                                ],
                                                'approved' => [
                                                    'bg' => '#f0fdf4',
                                                    'color' => '#16a34a',
                                                    'border' => '#bbf7d0',
                                                ],
                                                'rejected' => [
                                                    'bg' => '#fff1f2',
                                                    'color' => '#e11d48',
                                                    'border' => '#fcd0d0',
                                                ],
                                            ];
                                            $st = $statusMap[$record->status] ?? [
                                                'bg' => '#f4f5f7',
                                                'color' => '#9898a8',
                                                'border' => '#e8e8ef',
                                            ];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; border:1px solid {{ $st['border'] }};">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('overtime.show', $record) }}"
                                                class="emp-action-btn emp-action-view" title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('overtime.edit', $record) }}"
                                                class="emp-action-btn emp-action-edit" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form action="{{ route('overtime.destroy', $record) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="emp-action-btn emp-action-danger"
                                                    title="Delete" onclick="return confirm('Delete this record?')">
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
                                        No overtime / undertime records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($overtimeRecords->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 flex-wrap gap-2">

                        {{-- LEFT: Showing text --}}
                        <div class="small text-muted">
                            Showing
                            <strong>{{ $overtimeRecords->firstItem() }}</strong>
                            to
                            <strong>{{ $overtimeRecords->lastItem() }}</strong>
                            of
                            <strong>{{ $overtimeRecords->total() }}</strong>
                            entries
                        </div>

                        {{-- RIGHT: Pagination --}}
                        <div>
                            {{ $overtimeRecords->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .sortable-header {
            padding: 0 !important;
        }

        .sort-link {
            display: flex;
            align-items: center;
            padding: 12px;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s;
        }

        .sort-link:hover {
            background-color: #f5f5f5;
            color: #0066cc;
        }

        .emp-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
        }

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

        .emp-action-btn.emp-action-view:hover {
            background: #eff6ff;
            color: #3b82f6;
        }

        .emp-action-btn.emp-action-edit:hover {
            background: #fffbeb;
            color: #d97706;
        }

        .emp-action-btn.emp-action-danger:hover {
            background: #fff1f2;
            color: #e11d48;
        }

        .card-statistic {
            border: 1px solid #e8e8ef;
            box-shadow: none;
        }

        .stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #9898a8;
            margin-bottom: 6px;
        }
        /* --- Pagination --- */
        .pagination {
            margin: 0;
            gap: 2px;
        }

        .pagination .page-link {
            padding: 2px 8px;
            font-size: 0.75rem;
            border-radius: 4px !important;
            border: 1px solid #e8e8ef;
            color: #9898a8;
            background: #f4f5f7;
            line-height: 1.6;
            min-width: 28px;
            text-align: center;
        }

        .pagination .page-link:hover {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #bae6fd;
        }

        .pagination .page-item.active .page-link {
            background: #1c1c1e;
            border-color: #1c1c1e;
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            background: #f4f5f7;
            color: #d1d1db;
            border-color: #e8e8ef;
        }
    </style>

    @section('scripts')
        <script src="{{ asset('js/HR/overtime-management.js') }}"></script>
    @endsection
@endsection
