@extends('layouts.layout')
@section('title', 'Edit Bonus')

@push('styles')
@include('hr.bonuses._form-styles')
@endpush

@section('content')
<div class="bn-form-page">
    @if(session('success'))
    <div class="bn-flash error" style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="bn-flash error">
        <strong>Please fix the errors:</strong>
        <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="bn-form-topbar">
        <div>
            <h1 class="bn-form-title">Edit Bonus</h1>
            <p class="bn-form-sub">Update <strong>{{ $bonus->name }}</strong></p>
        </div>
        <a href="{{ route('bonuses.index') }}" class="bn-btn-sec">Back</a>
    </div>

    <div class="bn-form-wrap {{ $bonus->isThirteenthMonthPay() ? 'wide' : '' }}">
        @if($bonus->is_system_generated && auth()->user()->role !== 'superadmin')
        <div class="bn-system-note">This is a mandatory system bonus. Only administrators can change core settings.</div>
        @endif

        <form method="POST" action="{{ route('bonuses.update', $bonus) }}" id="bonusForm">
            @csrf @method('PUT')
            <div class="bn-card">
                <div class="bn-card-header"><p class="bn-card-title">Bonus Configuration</p></div>
                <div class="bn-card-body">
                    @include('hr.bonuses._form-fields', ['bonus' => $bonus])
                </div>
                <div class="bn-card-footer">
                    <button type="submit" class="bn-btn-submit">Update Bonus</button>
                    <a href="{{ route('bonuses.index') }}" class="bn-btn-cancel">Cancel</a>
                </div>
            </div>
        </form>

        @if($bonus->isThirteenthMonthPay() && $thirteenthMonthData)
            @include('hr.bonuses._thirteenth-month-pay-panel', [
                'bonus' => $bonus,
                'calendarYear' => $thirteenthMonthData['calendarYear'],
                'records' => $thirteenthMonthData['records'],
                'editingRecord' => $thirteenthMonthData['editingRecord'] ?? null,
            ])
        @endif
    </div>
</div>
@endsection

