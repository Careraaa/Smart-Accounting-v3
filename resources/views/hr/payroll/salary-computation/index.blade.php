@extends('layouts.layout')

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Payrolls</span>
                <div class="d-flex gap-2">

                    {{-- Batch Generate --}}
                    <a href="{{ route('payroll.salary-computation.batch-generate') }}" class="prl-btn-batch">
                        <i class="feather-layers me-1"></i>
                        Batch Generate
                    </a>

                    {{-- Create Payroll --}}
                    <a href="{{ route('payroll.salary-computation.create') }}" class="prl-btn-create">
                        <i class="feather-plus me-1"></i>
                        Create Payroll
                    </a>

                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="sortable-header">
                                    <div class="sort-link">Employee</div>
                                </th>
                                @php
                                    $headers = [
                                        'payroll_period_start' => 'Period',
                                        'gross_pay' => 'Gross Pay',
                                        'net_pay' => 'Net Pay',
                                        'status' => 'Status',
                                    ];
                                @endphp

                                @foreach ($headers as $column => $label)
                                    <th class="sortable-header @if ($column === 'status') text-center @endif"
                                        data-column="{{ $column }}">
                                        <a href="{{ route('payroll.salary-computation.index', ['sort_by' => $column, 'sort_order' => $sortBy === $column && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                            class="sort-link">
                                            {{ $label }}
                                            @if ($sortBy === $column)
                                                <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1"
                                                    style="font-size: 0.875rem;"></i>
                                            @else
                                                <i class="feather-arrow-up-down ms-1"
                                                    style="font-size: 0.875rem; opacity: 0.3;"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endforeach

                                <th class="sortable-header text-center">
                                    <div class="sort-link justify-content-center">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $payroll->user ? $payroll->user->first_name . ' ' . $payroll->user->last_name : 'N/A' }}
                                        </strong>

                                    </td>
                                    <td class="text-muted" style="font-size:.82rem;">
                                        {{ $payroll->payroll_period_start->format('M d, Y') }} –
                                        {{ $payroll->payroll_period_end->format('M d, Y') }}
                                    </td>
                                    <td>₱{{ number_format($payroll->gross_pay, 2) }}</td>
                                    <td><strong>₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'draft' => ['bg' => '#f4f5f7', 'color' => '#9898a8'],
                                                'pending' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                                'submitted' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                                'approved' => ['bg' => '#f0f9ff', 'color' => '#0284c7'],
                                                'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                                                'rejected' => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                            ];
                                            $st = $statusMap[$payroll->status] ?? $statusMap['draft'];
                                        @endphp
                                        <span
                                            style="display:inline-block; background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.4px; text-transform:uppercase;">
                                            {{ ucfirst($payroll->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('payroll.salary-computation.show', $payroll) }}"
                                                class="emp-action-btn emp-action-view" title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('payroll.salary-computation.edit', $payroll) }}"
                                                class="emp-action-btn emp-action-edit" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form action="{{ route('payroll.salary-computation.destroy', $payroll) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="emp-action-btn emp-action-danger"
                                                    title="Delete" onclick="return confirm('Delete this payroll?')">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
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
@endsection
