@extends('layouts.layout')

@push('styles')
    @include('employee.profile._styles')
@endpush

@section('content')
<div class="pf-page pf-wrap">
    <div class="pf-backdrop"><div class="pf-grid"></div></div>
    <div class="pf-content">

    <div class="pf-topbar">
        <div>
            <h1 class="pf-topbar-title">Edit Profile</h1>
            <p class="pf-topbar-sub">Update your personal information</p>
        </div>
        <div class="pf-topbar-actions">
            <a href="{{ route('employee.profile.show') }}" class="pf-btn-sec">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="prl-flash error" style="margin-bottom:18px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
            </svg>
            Please fix the highlighted fields.
        </div>
    @endif

    <div class="pf-two-col">
        <div class="pf-card">
            <div class="pf-card-head">
                <p class="pf-card-title"><span class="pf-dot"></span> Personal Information</p>
            </div>
            <div class="pf-card-body">
                <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    @include('partials.employee.personal', ['readOnly' => false, 'isEdit' => true])

                    <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:18px;">
                        <a href="{{ route('employee.profile.show') }}" class="pf-btn-sec">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" class="pf-btn-primary">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="pf-card">
            <div class="pf-card-head">
                <p class="pf-card-title"><span class="pf-dot"></span> Tips</p>
            </div>
            <div class="pf-card-body" style="color:#6b7280;font-size:0.82rem;line-height:1.7;">
                <div style="margin-bottom:10px;">
                    <div style="font-weight:800;color:#111827;margin-bottom:4px;">Keep it accurate</div>
                    Your profile details are used in payroll, attendance, and HR records.
                </div>
                <div>
                    <div style="font-weight:800;color:#111827;margin-bottom:4px;">Birthdate format</div>
                    Use the calendar picker to avoid invalid dates.
                </div>
            </div>
        </div>
    </div>

</div></div>

<script>
    // Initialize spouse name visibility on load
    document.addEventListener('DOMContentLoaded', function () {
        const civilStatusSelect = document.getElementById('civil_status');
        if (civilStatusSelect) {
            civilStatusSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
