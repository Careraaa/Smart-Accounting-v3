@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Drivers List</h5>
                    <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm">Add Driver</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>License Number</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $driver)
                                <tr>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->license_number }}</td>
                                    <td>{{ $driver->contact_number }}</td>
                                    <td>{{ $driver->email }}</td>
                                    <td><span class="badge bg-success">{{ $driver->status }}</span></td>
                                    <td>
                                        <a href="{{ route('drivers.show', $driver) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('drivers.destroy', $driver) }}" method="POST" style="display:inline;">
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
