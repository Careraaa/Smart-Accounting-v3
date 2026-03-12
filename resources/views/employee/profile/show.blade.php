@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">My Personal Information</span>
            <div>
                <a href="{{ route('employee.profile.edit') }}" class="btn btn-primary btn-sm me-2">
                    <i class="feather-edit me-1"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @include('partials.employee.personal', ['readOnly' => true])

        </div>
    </div>
</div>
@endsection
