@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Payrolls</h5>
                        <a href="{{ route('payroll.create') }}" class="btn btn-primary">Create Payroll</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>Gross Pay</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        <td>{{ $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : 'N/A' }}
                                        </td>
                                        <td>{{ $payroll->payroll_period_start->format('M d, Y') }} -
                                            {{ $payroll->payroll_period_end->format('M d, Y') }}</td>
                                        <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                        <td>₱{{ number_format($payroll->net_pay, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'submitted' => 'info',
                                                    'approved' => 'primary',
                                                    'paid' => 'success',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$payroll->status] ?? 'secondary' }}">
                                                {{ ucfirst($payroll->status) }}
                                            </span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('payroll.show', $payroll) }}"
                                                class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('payroll.edit', $payroll) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('payroll.destroy', $payroll) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No payrolls found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
