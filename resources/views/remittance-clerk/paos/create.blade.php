@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Create New PAO</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('paos.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="conductor_id">Conductor ID *</label>
                                    <input type="text" class="form-control @error('conductor_id') is-invalid @enderror" name="conductor_id" value="{{ old('conductor_id') }}" required>
                                    @error('conductor_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contact_number">Contact Number *</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number') }}" required>
                                    @error('contact_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_of_hire">Date of Hire *</label>
                                    <input type="date" class="form-control @error('date_of_hire') is-invalid @enderror" name="date_of_hire" value="{{ old('date_of_hire') }}" required>
                                    @error('date_of_hire')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                        </div>
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Create PAO</button>
                            <a href="{{ route('paos.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
