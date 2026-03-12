@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Account Settings</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Error!</strong> Please fix the following errors:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('settings.update-password') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">Current Password *</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                    <hr>
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Login Activity</h6>
                        <p class="text-muted">Last login: {{ auth()->user()->updated_at->format('F d, Y \a\t H:i A') }}</p>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.details') }}" class="btn btn-outline-secondary">Back to Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
