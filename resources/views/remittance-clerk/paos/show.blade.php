@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">PAO Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('paos.edit', $pao) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('paos.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Name</label>
                                <p class="fs-5">{{ $pao->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Conductor ID</label>
                                <p class="fs-5">{{ $pao->conductor_id }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Contact Number</label>
                                <p class="fs-5">{{ $pao->contact_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="fs-5">{{ $pao->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p><span class="badge bg-success">{{ $pao->status }}</span></p>
                            </div>
                        </div>
                    </div>

                    @if($pao->address)
                        <div class="mb-3">
                            <label class="form-label text-muted">Address</label>
                            <p class="fs-5">{{ $pao->address }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex gap-2">
                        <form action="{{ route('paos.destroy', $pao) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this PAO?')">Delete PAO</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
