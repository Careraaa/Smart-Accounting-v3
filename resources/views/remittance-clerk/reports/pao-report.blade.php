@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">PAO Report</span>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.print.pao-report') }}" class="btn btn-sm btn-primary" target="_blank">
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
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paos as $pao)
                            <tr>
                                <td><strong>{{ $pao->name }}</strong></td>
                                <td>{{ $pao->contact_number ?? 'N/A' }}</td>
                                <td>{{ $pao->email ?? 'N/A' }}</td>
                                <td>{{ ucfirst($pao->gender ?? 'N/A') }}</td>
                                <td>{{ $pao->address ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="feather-users d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No PAOs found
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
