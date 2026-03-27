@extends('layouts.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payroll Reports</h5>
                <p class="text-muted small mb-0">Approved payroll batches report</p>
            </div>
            <div class="card-body">
                @if(count($batchData) > 0)
                    <div class="row mb-4">
                        @foreach ($batchData as $batch)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-1">{{ \Carbon\Carbon::parse($batch['period_start'])->format('F d, Y') }} - {{ \Carbon\Carbon::parse($batch['period_end'])->format('F d, Y') }}</h6>
                                        <small class="text-muted">Approved payroll batch</small>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Employees</span><span>{{ $batch['count'] }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Gross</span><span>₱{{ number_format($batch['total_gross'], 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Deductions</span><span>₱{{ number_format($batch['total_deductions'], 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Net</span><span>₱{{ number_format($batch['total_net'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        No approved payroll batches found for reports.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection