@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profile Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 150px; height: 150px; font-size: 60px; font-weight: normal;">
                                {{ auth()->user()->getFirstLetter() }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <p class="fs-5 fw-semibold">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="fs-5 fw-semibold">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Role</label>
                                <p class="fs-5 fw-semibold"><span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p class="fs-5 fw-semibold"><span class="badge bg-success">Active</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-muted">Member Since</label>
                                <p class="fs-5 fw-semibold">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                        <a href="{{ route('settings.account') }}" class="btn btn-outline-secondary">Account Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
