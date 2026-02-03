@extends('layouts.app')

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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Conductor ID</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paos as $pao)
                                <tr>
                                    <td>{{ $pao->name }}</td>
                                    <td>{{ $pao->conductor_id }}</td>
                                    <td>{{ $pao->contact_number }}</td>
                                    <td>{{ $pao->email }}</td>
                                    <td><span class="badge bg-success">{{ $pao->status }}</span></td>
                                    <td>
                                        <a href="{{ route('paos.show', $pao) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('paos.edit', $pao) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('paos.destroy', $pao) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
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
@endsection
