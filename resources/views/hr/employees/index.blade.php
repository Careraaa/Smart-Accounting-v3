@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Employees</h5>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">Add Employee</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th class="w-15">First Name</th>
                                    <th class="w-15">Last Name</th>
                                    <th class="w-20">Email</th>
                                    <th class="w-10">Position</th>
                                    <th class="w-10">Department</th>
                                    <th class="w-10">Salary Rate</th>
                                    <th class="w-10 text-center">Status</th>
                                    <th class="w-10 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->first_name }}</td>
                                        <td>{{ $employee->last_name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->position }}</td>
                                        <td>{{ $employee->department }}</td>
                                        <td>₱{{ number_format($employee->salary_rate, 2) }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusStyles = [
                                                    'active' => 'bg-soft-info text-info',
                                                    'inactive' => 'bg-soft-danger text-danger',
                                                ];
                                            @endphp
                                            <span class="badge px-3 {{ $statusStyles[$employee->status] ?? 'bg-secondary text-dark' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center d-flex justify-content-center gap-2">
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="feather feather-eye"></i>
                                            </a>
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="feather feather-edit-3"></i>
                                            </a>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="feather feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No employees found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
