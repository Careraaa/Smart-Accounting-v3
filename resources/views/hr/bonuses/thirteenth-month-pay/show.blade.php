@extends('layouts.layout')
@section('title', '13th Month Pay Details')

@push('styles')
@include('hr.bonuses._form-styles')
@include('hr.bonuses.thirteenth-month-pay._computation-styles')
@endpush

@section('content')
<div class="bn-form-page">
    <div class="bn-form-topbar">
        <div>
            <h1 class="bn-form-title">{{ $thirteenthMonthPay->user?->name }}</h1>
            <p class="bn-form-sub">13th Month Pay · {{ $thirteenthMonthPay->calendar_year }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('payroll.thirteenth-month-pay.index', ['year' => $thirteenthMonthPay->calendar_year]) }}" class="bn-btn-sec">Back</a>
            @if($thirteenthMonthPay->status !== 'paid')
            <a href="{{ route('payroll.thirteenth-month-pay.record-payment', $thirteenthMonthPay) }}" class="bn-btn-submit" style="text-decoration:none;padding:9px 16px;">Record Payment</a>
            @endif
        </div>
    </div>

    <div class="tmp-stats">
        <div class="tmp-stat">
            <div class="tmp-stat-label">13th Month Pay</div>
            <div class="tmp-stat-value">₱{{ number_format($thirteenthMonthPay->thirteenth_month_pay, 2) }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">Amount Paid</div>
            <div class="tmp-stat-value">₱{{ number_format($thirteenthMonthPay->amount_paid, 2) }}</div>
        </div>
        <div class="tmp-stat">
            <div class="tmp-stat-label">Remaining</div>
            <div class="tmp-stat-value">₱{{ number_format($thirteenthMonthPay->amount_remaining, 2) }}</div>
        </div>
    </div>

    @include('hr.bonuses.thirteenth-month-pay._computation-breakdown', ['breakdown' => $breakdown])

    <form method="POST" action="{{ route('payroll.thirteenth-month-pay.recompute', $thirteenthMonthPay) }}" style="margin-top:16px;">
        @csrf
        <button type="submit" class="bn-btn-sec">Recompute from Payroll</button>
    </form>
</div>
@endsection
