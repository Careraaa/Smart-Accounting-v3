@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Attendance Records</h5>
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">Record Attendance</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                                    <td>{{ $attendance->date }}</td>
                                    <td>{{ $attendance->time_in ? $attendance->time_in->format('H:i') : 'N/A' }}</td>
                                    <td>{{ $attendance->time_out ? $attendance->time_out->format('H:i') : 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $attendance->status }}</span></td>
                                    <td>
                                        <a href="{{ route('attendance.edit', $attendance) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('attendance.destroy', $attendance) }}" method="POST" style="display:inline;">
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
