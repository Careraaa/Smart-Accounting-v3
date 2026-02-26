@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Employees</span>
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Employee
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Salary Rate</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }}</td>
                                <td>{{ $employee->last_name }}</td>
                                <td class="text-muted">{{ $employee->email }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->department }}</td>
                                <td>₱{{ number_format($employee->salary_rate, 2) }}</td>
                                <td class="text-center">
                                    @if ($employee->status === 'active')
                                        <span class="emp-badge emp-badge-active">Active</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('employees.show', $employee) }}"
                                            class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger"
                                                title="Delete"
                                                onclick="return confirm('Delete this employee?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="feather-users d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No employees found
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
/* Status badges */
.emp-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 3px 10px;
    border-radius: 20px;
}
.emp-badge-active {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}
.emp-badge-inactive {
    background: #fff5f5;
    color: #c8292a;
    border: 1px solid #fcd0d0;
}

/* Action icon buttons */
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

/* View — slate blue */
.emp-action-btn.emp-action-view:hover {
    background: #eff6ff;
    color: #3b82f6;
}

/* Edit — warm amber */
.emp-action-btn.emp-action-edit:hover {
    background: #fffbeb;
    color: #d97706;
}

/* Delete — muted rose */
.emp-action-btn.emp-action-danger:hover {
    background: #fff1f2;
    color: #e11d48;
}
</style>
@endsection