@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Payrolls</span>
            <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Create Payroll
            </a>
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
                                            'draft'     => ['bg' => '#f4f5f7', 'color' => '#9898a8'],
                                            'pending'   => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'submitted' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'approved'  => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                            'paid'      => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                            'rejected'  => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                        ];
                                        $st = $statusMap[$payroll->status] ?? $statusMap['draft'];
                                    @endphp
                                    <span style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('payroll.show', $payroll) }}" class="emp-action-btn emp-action-view" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('payroll.edit', $payroll) }}" class="emp-action-btn emp-action-edit" title="Edit">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Delete this payroll?')">
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
                                    No payrolls found
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
.emp-action-btn.emp-action-view:hover   { background: #eff6ff; color: #3b82f6; }
.emp-action-btn.emp-action-edit:hover   { background: #fffbeb; color: #d97706; }
.emp-action-btn.emp-action-danger:hover { background: #fff1f2; color: #e11d48; }
</style>
@endsection