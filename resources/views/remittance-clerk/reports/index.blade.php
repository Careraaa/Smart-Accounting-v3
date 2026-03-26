@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: #1c1c1e;">Reports</h3>
        <p class="text-muted mb-0">Generate and view remittance & operations reports</p>
    </div>

    {{-- Report Type Section --}}
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <i class="feather-layers me-2" style="font-size: 1.2rem; color: #dc2626;"></i>
            <h5 class="mb-0 fw-bold">Report Type</h5>
        </div>

        <div class="row">
            {{-- Remittance Report Card --}}
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc2626;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="feather-trending-up" style="font-size: 2rem; color: #dc2626;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Remittance Report</h5>
                        <p class="text-muted small mb-3">Daily, weekly, and monthly remittance records</p>
                        <a href="{{ route('reports.remittance-report') }}" class="btn btn-sm w-100" style="background-color: #dc2626; color: white; border: none;">
                            <i class="feather-file-text me-1"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            {{-- Driver Report Card --}}
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0369a1;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="feather-user" style="font-size: 2rem; color: #0369a1;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Driver Report</h5>
                        <p class="text-muted small mb-3">All driver information and details</p>
                        <a href="{{ route('reports.driver-report') }}" class="btn btn-sm w-100" style="background-color: #0369a1; color: white; border: none;">
                            <i class="feather-file-text me-1"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            {{-- PAO Report Card --}}
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #7c3aed;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="feather-users" style="font-size: 2rem; color: #7c3aed;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">PAO Report</h5>
                        <p class="text-muted small mb-3">All PAO information and details</p>
                        <a href="{{ route('reports.pao-report') }}" class="btn btn-sm w-100" style="background-color: #7c3aed; color: white; border: none;">
                            <i class="feather-file-text me-1"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            {{-- Vehicle & Route Report Card --}}
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #16a34a;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="feather-truck" style="font-size: 2rem; color: #16a34a;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Vehicle & Route Report</h5>
                        <p class="text-muted small mb-3">List of vehicles and routes in operation</p>
                        <a href="{{ route('reports.vehicle-route-report') }}" class="btn btn-sm w-100" style="background-color: #16a34a; color: white; border: none;">
                            <i class="feather-file-text me-1"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    
    .btn:hover {
        opacity: 0.9;
    }
</style>
@endsection
