@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Statutory Deductions</span>
            <div class="d-flex gap-3">
                <a href="https://mpm.ph/pagibig-hdmf-table/" target="_blank" class="stat-ref-link">
                    <i class="feather-external-link me-1"></i>Pag-IBIG Table
                </a>
                <a href="https://www.sss.gov.ph/sss-contribution-table/" target="_blank" class="stat-ref-link">
                    <i class="feather-external-link me-1"></i>SSS Table
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($deductions->count())
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Min Salary</th>
                                <th>Max Salary</th>
                                <th>Employee Share</th>
                                <th>Employer Share</th>
                                <th>% Employee</th>
                                <th>% Employer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deductions as $deduction)
                                <tr>
                                    <td><strong>{{ $deduction->name }}</strong></td>
                                    <td>₱{{ number_format($deduction->min_salary, 2) }}</td>
                                    <td>₱{{ number_format($deduction->max_salary, 2) }}</td>
                                    <td>{{ $deduction->employee_share !== null ? '₱' . number_format($deduction->employee_share, 2) : '—' }}</td>
                                    <td>{{ $deduction->employer_share !== null ? '₱' . number_format($deduction->employer_share, 2) : '—' }}</td>
                                    <td>{{ $deduction->percentage_employee !== null ? $deduction->percentage_employee . '%' : '—' }}</td>
                                    <td>{{ $deduction->percentage_employer !== null ? $deduction->percentage_employer . '%' : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="feather-sliders d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                    No statutory deductions found.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-ref-link {
    font-size: 0.78rem;
    font-weight: 600;
    color: #9898a8;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: color 0.13s;
}
.stat-ref-link:hover { color: #c8292a; }
</style>
@endsection