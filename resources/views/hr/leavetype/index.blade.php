@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="card-title mb-0">Leave Types</span>
                <p class="text-muted small mt-1 mb-0">Manage leave types and policies</p>
            </div>
            <a href="{{ route('leave-type.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Leave Type
            </a>
        </div>

        <div class="card-body">

            {{-- Filter Buttons --}}
            <div class="mb-3 d-flex gap-2">
                <a href="{{ route('leave-type.index', ['status' => 'all']) }}" 
                   class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="feather-list me-1"></i> All Types
                </a>
                <a href="{{ route('leave-type.index', ['status' => 'active']) }}" 
                   class="btn btn-sm {{ $status === 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="feather-check-circle me-1"></i> Active
                </a>
            </div>

            {{-- Alert Messages --}}
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="feather-alert-circle me-2"></i>
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20%;">LEAVE TYPE</th>
                            <th style="width: 15%;">DAYS ALLOWED</th>
                            <th style="width: 15%;">CARRY OVER</th>
                            <th style="width: 35%;">DESCRIPTION</th>
                            <th style="width: 5%; text-align: center;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $leaveType)
                            <tr>
                                <td>
                                    <strong>{{ $leaveType->name }}</strong>
                                    @if($leaveType->abbreviation)
                                        <br>
                                        <small class="text-muted">{{ $leaveType->abbreviation }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span style="background-color: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-weight: 500;">
                                        {{ $leaveType->days_allowed }} days
                                    </span>
                                </td>
                                <td>
                                    @if($leaveType->carry_over)
                                        <span style="color: #16a34a; font-weight: 600;">
                                            <i class="feather-check me-1"></i> Yes
                                        </span>
                                    @else
                                        <span style="color: #dc2626; font-weight: 600;">
                                            <i class="feather-x me-1"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($leaveType->description, 50) }}</small>
                                </td>
                                <td style="text-align: center;">
                                    <div class="emp-action-btns">
                                        <a href="{{ route('leave-type.edit', $leaveType) }}" 
                                           class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('leave-type.destroy', $leaveType) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" 
                                                    title="Delete" onclick="return confirm('Are you sure you want to delete this leave type?')">
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
                                    No leave types found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($leaveTypes->hasPages())
                <div class="mt-4">
                    {{ $leaveTypes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .emp-action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .emp-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        transition: all 0.3s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .emp-action-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .emp-action-edit:hover {
        color: #1f2937;
        border-color: #9ca3af;
    }

    .emp-action-danger:hover {
        color: #dc2626;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .card-statistic {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
    }

    .card-statistic .card-body {
        padding: 20px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
</style>
@endsection
