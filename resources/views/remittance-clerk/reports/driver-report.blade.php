@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Driver Report</span>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.print.driver-report') }}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="feather-printer me-1"></i> Print
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>License Number</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td><strong>{{ $driver->name }}</strong></td>
                                <td>{{ $driver->license_number ?? 'N/A' }}</td>
                                <td>{{ $driver->contact_number ?? 'N/A' }}</td>
                                <td>{{ $driver->email ?? 'N/A' }}</td>
                                <td>{{ ucfirst($driver->gender ?? 'N/A') }}</td>
                                <td>{{ $driver->address ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="feather-user d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No drivers found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
