@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Payroll Approval</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Period</th>
                            <th>Gross Pay</th>
                            <th>Net Pay</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                            <tr>
                                <td>
                                    <strong>{{ $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : 'N/A' }}</strong>
                                </td>
                                <td class="text-muted" style="font-size:.82rem;">
                                    {{ $payroll->payroll_period_start->format('M d, Y') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}
                                </td>
                                <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'pending'  => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'approved' => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                            'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$payroll->status] ?? ['bg' => '#f4f5f7', 'color' => '#9898a8'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('payroll-approval.show', $payroll) }}"
                                            class="emp-action-btn emp-action-view" title="Review">
                                            <i class="feather-eye"></i>
                                        </a>
                                        @if ($payroll->status === 'pending')
                                            <form action="{{ route('payroll-approval.approve', $payroll) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="emp-action-btn emp-action-approve" title="Approve"
                                                    onclick="return confirm('Approve this payroll?')">
                                                    <i class="feather-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('payroll-approval.reject', $payroll) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="emp-action-btn emp-action-danger" title="Reject"
                                                    onclick="return confirm('Reject this payroll?')">
                                                    <i class="feather-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="feather-check-circle d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No payrolls pending approval
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection