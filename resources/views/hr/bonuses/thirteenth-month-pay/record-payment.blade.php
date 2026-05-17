@extends('layouts.layout')
@section('title', 'Record 13th Month Payment')

@push('styles')
@include('hr.bonuses._form-styles')
@endpush

@section('content')
<div class="bn-form-page">
    <div class="bn-form-topbar">
        <div>
            <h1 class="bn-form-title">Record Payment</h1>
            <p class="bn-form-sub">{{ $thirteenthMonthPay->user?->name }} · Remaining ₱{{ number_format($thirteenthMonthPay->amount_remaining, 2) }}</p>
        </div>
        <a href="{{ route('payroll.thirteenth-month-pay.show', $thirteenthMonthPay) }}" class="bn-btn-sec">Back</a>
    </div>

    <div class="bn-form-wrap">
        <form method="POST" action="{{ route('payroll.thirteenth-month-pay.record-payment.store', $thirteenthMonthPay) }}">
            @csrf
            <div class="bn-card">
                <div class="bn-card-body">
                    <div class="bn-field">
                        <label class="bn-label">Amount to Pay (₱) <span class="req">*</span></label>
                        <input type="number" name="amount_paid" class="bn-input" step="0.01" min="0.01"
                            max="{{ $thirteenthMonthPay->amount_remaining }}" value="{{ $thirteenthMonthPay->amount_remaining }}" required>
                    </div>
                    <div class="bn-field">
                        <label class="bn-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" class="bn-input" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="bn-field">
                        <label class="bn-label">Notes</label>
                        <textarea name="notes" class="bn-textarea">{{ old('notes', $thirteenthMonthPay->notes) }}</textarea>
                    </div>
                </div>
                <div class="bn-card-footer">
                    <button type="submit" class="bn-btn-submit">Record Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

