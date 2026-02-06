@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Payroll Approval</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                                    <td>{{ $payroll->payroll_period_start->format('M d, Y') }} - {{ $payroll->payroll_period_end->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                    <td>₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                    <td>₱{{ number_format($payroll->net_pay, 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $payroll->status }}</span></td>
                                    <td>
                                        <a href="{{ route('payroll-approval.show', $payroll) }}" class="btn btn-info btn-sm">Review</a>
                                        <form action="{{ route('payroll-approval.approve', $payroll) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this payroll?')">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payroll pending approval</td>
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
