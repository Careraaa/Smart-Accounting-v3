@extends('layouts.layout')

@push('styles')
    @include('hr.employees._styles')
@endpush

@section('content')
    @include('hr.employees._form', ['employee' => $employee, 'isEdit' => true])
@endsection

@push('scripts')
    @include('hr.employees._scripts')
@endpush

