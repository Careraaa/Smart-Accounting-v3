@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Statutory Deductions</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            This table is based on official government sources.
                            <a href="https://mpm.ph/pagibig-hdmf-table/" target="_blank">Pag-IBIG / HDMF Table</a> |
                            <a href="https://www.sss.gov.ph/sss-contribution-table/" target="_blank">SSS Contribution
                                Table</a>
                        </p>

                        <div class="table-responsive">
                            @if ($deductions->count())
                                <table class="table table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th class="w-15">Name</th>
                                            <th class="w-10">Min Salary</th>
                                            <th class="w-10">Max Salary</th>
                                            <th class="w-10">Employee Share</th>
                                            <th class="w-10">Employer Share</th>
                                            <th class="w-10">% Employee</th>
                                            <th class="w-10">% Employer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductions as $deduction)
                                            <tr>
                                                <td>{{ $deduction->name }}</td>
                                                <td>₱{{ number_format($deduction->min_salary, 2) }}</td>
                                                <td>₱{{ number_format($deduction->max_salary, 2) }}</td>
                                                <td>
                                                    {{ $deduction->employee_share !== null ? '₱' . number_format($deduction->employee_share, 2) : '-' }}
                                                </td>
                                                <td>
                                                    {{ $deduction->employer_share !== null ? '₱' . number_format($deduction->employer_share, 2) : '-' }}
                                                </td>
                                                <td>
                                                    {{ $deduction->percentage_employee !== null ? $deduction->percentage_employee . '%' : '-' }}
                                                </td>
                                                <td>
                                                    {{ $deduction->percentage_employer !== null ? $deduction->percentage_employer . '%' : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center text-muted">No statutory deductions found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
