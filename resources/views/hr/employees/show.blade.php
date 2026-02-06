@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Employee Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>First Name:</strong>
                                <p class="fs-5">{{ $employee->first_name }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Last Name:</strong>
                                <p class="fs-5">{{ $employee->last_name }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong>
                                <p class="fs-5">{{ $employee->email }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Phone:</strong>
                                <p class="fs-5">{{ $employee->phone }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Position:</strong>
                                <p class="fs-5">{{ $employee->position }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Department:</strong>
                                <p class="fs-5">{{ $employee->department }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Date of Hire:</strong>
                                <p class="fs-5">{{ $employee->date_of_hire ? $employee->date_of_hire->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Salary Rate:</strong>
                                <p class="fs-5">₱{{ number_format($employee->salary_rate, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($employee->address)
                        <div class="mb-3">
                            <strong>Address:</strong>
                            <p class="fs-5">{{ $employee->address }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Status:</strong>
                        @php
                            $statusStyles = [
                                'active' => 'bg-soft-info text-info',
                                'inactive' => 'bg-soft-danger text-danger',
                            ];
                        @endphp
                        <p>
                            <span class="badge px-3 {{ $statusStyles[$employee->status] ?? 'bg-secondary text-dark' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </p>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete Employee</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
