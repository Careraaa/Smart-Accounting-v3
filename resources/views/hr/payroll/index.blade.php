@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Payroll</h5>
                    <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm">Create Payroll</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Basic Salary</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                                    <td>{{ $payroll->payroll_period_start->format('M d, Y') }} - {{ $payroll->payroll_period_end->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                    <td>₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                    <td>₱{{ number_format($payroll->net_pay, 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $payroll->status }}</span></td>
                                    <td>
                                        <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('payroll.generatePayslip', $payroll) }}" class="btn btn-success btn-sm">Payslip</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
