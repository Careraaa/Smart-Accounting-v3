@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">New OT / UT Request</h1>
                <p class="empui-sub">Create an overtime or undertime entry for HR review.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-info"></i> Keep it concise</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.overtime-undertime.index') }}">
                    <i class="feather-arrow-left"></i>
                    Back to Requests
                </a>
            </div>
        </div>

        <div class="empui-card" style="max-width: 980px; margin: 0 auto;">
            <div class="empui-card-head">
                <p class="empui-card-title"><span class="empui-dot"></span> Record Details</p>
            </div>
            <div class="empui-card-body">
                <form action="{{ route('employee.overtime-undertime.store') }}" method="POST" id="overtimeForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="overtime"  {{ old('type') === 'overtime'  ? 'selected' : '' }}>Overtime</option>
                                <option value="undertime" {{ old('type') === 'undertime' ? 'selected' : '' }}>Undertime</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date') }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hours <span class="text-danger">*</span></label>
                            <input type="number" name="hours" class="form-control @error('hours') is-invalid @enderror"
                                   value="{{ old('hours') }}" placeholder="0.5" step="0.5" min="0.5" max="24" required>
                            <div class="empui-muted mt-1">Between 0.5 and 24 hours</div>
                            @error('hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                                      rows="4" required>{{ old('reason') }}</textarea>
                            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="submit" class="empui-btn">
                            <i class="feather-plus"></i>
                            Create Record
                        </button>
                        <a href="{{ route('employee.overtime-undertime.index') }}" class="empui-btn-sec">
                            <i class="feather-x"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection