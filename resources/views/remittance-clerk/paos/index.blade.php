@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">PAO / Conductors List</h5>
                    <a href="{{ route('paos.create') }}" class="btn btn-primary btn-sm">Add PAO</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paos as $pao)
                                    <tr>
                                        <td>{{ $pao->name }}</td>
                                        <td>{{ $pao->contact_number }}</td>
                                        <td><span class="badge bg-soft-success text-success">{{ $pao->status }}</span></td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('paos.show', $pao) }}" class="avatar-text avatar-md text-info" title="View">
                                                <i class="feather feather-eye"></i>
                                            </a>
                                            <a href="{{ route('paos.edit', $pao) }}" class="avatar-text avatar-md text-warning" title="Edit">
                                                <i class="feather-edit"></i>
                                            </a>
                                            <form action="{{ route('paos.destroy', $pao) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="avatar-text avatar-md text-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
