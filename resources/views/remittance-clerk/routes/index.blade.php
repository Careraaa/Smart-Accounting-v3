@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Routes List</h5>
                    <a href="{{ route('routes.create') }}" class="btn btn-primary">Add Route</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                                <tr>
                                    <td>{{ $route->route_name }}</td>
                                    <td>{{ $route->origin }}</td>
                                    <td>{{ $route->destination }}</td>
                                    <td><span class="badge bg-success">{{ $route->status }}</span></td>
                                    <td class="d-flex gap-2">
                                        <a href="{{ route('routes.show', $route) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('routes.edit', $route) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('routes.destroy', $route) }}" method="POST" class="d-inline">
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
