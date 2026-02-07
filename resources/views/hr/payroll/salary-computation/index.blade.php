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
                        <div class="table-responsive">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th class="w-25">Employee</th>
                                        <th class="w-25">Period</th>
                                        <th class="w-15">Gross Pay</th>
                                        <th class="w-15">Net Pay</th>
                                        <th class="w-10 text-center">Status</th>
                                        <th class="w-10 text-center">Actions</th>
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
                                            <td class="text-center">
                                                @php
                                                    $statusStyles = [
                                                        'draft' => 'bg-secondary text-white',
                                                        'pending' => 'bg-soft-warning text-warning',
                                                        'submitted' => 'bg-soft-warning text-warning',
                                                        'approved' => 'bg-soft-info text-info',
                                                        'paid' => 'bg-success text-white',
                                                        'rejected' => 'bg-soft-danger text-danger',
                                                    ];
                                                @endphp
                                                <span
                                                    class="badge px-3 {{ $statusStyles[$payroll->status] ?? 'bg-secondary text-white' }}">
                                                    {{ ucfirst($payroll->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('payroll.show', $payroll) }}"
                                                        class="btn btn-outline-info btn-sm border-1 rounded">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('payroll.edit', $payroll) }}"
                                                        class="btn btn-outline-warning btn-sm border-1 rounded">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('payroll.destroy', $payroll) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm border-1 rounded"
                                                            onclick="return confirm('Are you sure you want to delete this payroll?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
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
    </div>
@endsection
