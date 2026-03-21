@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Profile Avatar (First Letter) -->
                        <div class="mb-4 text-center">
                            <div class="mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 150px; height: 150px; font-size: 60px; font-weight: normal;">
                                    {{ auth()->user()->getFirstLetter() }}
                                </div>
                            </div>
                            <small class="form-text text-muted d-block">Profile Avatar (First Letter of Name)</small>
                        </div>

                        <hr>

                        <!-- Full Name -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <!-- Username -->
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', auth()->user()->username) }}" required>
                            @error('username')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('profile.details') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>@endsection
