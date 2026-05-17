@extends('layouts.layout')
@section('title', 'Create Bonus')

@push('styles')
@include('hr.bonuses._form-styles')
@endpush

@section('content')
<div class="bn-form-page">
    @if($errors->any())
    <div class="bn-flash error">
        <strong>Please fix the errors:</strong>
        <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="bn-form-topbar">
        <div>
            <h1 class="bn-form-title">Create Bonus</h1>
            <p class="bn-form-sub">Define a new bonus for payroll processing</p>
        </div>
        <a href="{{ route('bonuses.index') }}" class="bn-btn-sec">Back</a>
    </div>

    <div class="bn-form-wrap">
        <form method="POST" action="{{ route('bonuses.store') }}" id="bonusForm">
            @csrf
            <div class="bn-card">
                <div class="bn-card-header"><p class="bn-card-title">Bonus Configuration</p></div>
                <div class="bn-card-body">
                    @include('hr.bonuses._form-fields')
                </div>
                <div class="bn-card-footer">
                    <button type="submit" class="bn-btn-submit">Save Bonus</button>
                    <a href="{{ route('bonuses.index') }}" class="bn-btn-cancel">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

