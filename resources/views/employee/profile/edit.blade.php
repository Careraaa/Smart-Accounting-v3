@extends('layouts.layout')

@push('styles')
<style>
@keyframes pe-fade { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.pe-card { animation:pe-fade 0.35s ease-out; }

.emp-section-title-form { font-size:0.9rem; font-weight:800; color:#111827; letter-spacing:-0.01em; margin:0 0 14px; }
.emp-divider { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:#9ca3af; border-bottom:1px solid #f3f4f6; padding-bottom:10px; margin:18px 0 14px; }
.emp-label { display:block; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#6b7280; margin-bottom:6px; }
.emp-label .req { color:#c8292a; }
.emp-label .opt { color:#9ca3af; font-weight:400; text-transform:none; letter-spacing:0; font-size:0.72rem; }
.emp-input, .emp-select, .emp-textarea { width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px; font-size:0.845rem; color:#111827; background:#fff; outline:none; transition:border-color 0.15s, box-shadow 0.15s; appearance:none; -webkit-appearance:none; }
.emp-input:focus, .emp-select:focus, .emp-textarea:focus { border-color:#c8292a; box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.emp-input:read-only, .emp-input[readonly] { background:#f9fafb; color:#6b7280; }
.emp-row { display:grid; gap:16px; }
.emp-row.cols-2 { grid-template-columns: 1fr 1fr; }
.emp-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
@media (max-width:768px) { .emp-row.cols-3 { grid-template-columns: 1fr 1fr; } }
@media (max-width:520px) { .emp-row.cols-2, .emp-row.cols-3 { grid-template-columns: 1fr; } }
.emp-select-wrap { position:relative; }
.emp-select-wrap .emp-chevron { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
.emp-select-wrap .emp-select { padding-right:36px; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Edit Profile</h1>
                <p class="text-sm text-gray-500 mt-1">Update your personal information</p>
            </div>
            <a href="{{ route('employee.profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                <div class="text-sm font-semibold text-rose-800">Please fix the highlighted fields.</div>
            </div>
        @endif

        {{-- Two-column --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-4 items-start">

            {{-- Form card --}}
            <div class="pe-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span class="text-sm font-extrabold text-gray-900">Personal Information</span>
                </div>
                <div class="p-5">
                    <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PATCH')

                        @include('partials.employee.personal', ['readOnly' => false, 'isEdit' => true])

                        <div class="flex items-center justify-between gap-3 flex-wrap mt-5 pt-5 border-t border-gray-100">
                            <a href="{{ route('employee.profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tips card --}}
            <div class="pe-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span class="text-sm font-extrabold text-gray-900">Tips</span>
                </div>
                <div class="p-5 text-sm text-gray-500 leading-relaxed space-y-4">
                    <div>
                        <div class="font-extrabold text-gray-900 mb-1">Keep it accurate</div>
                        Your profile details are used in payroll, attendance, and HR records.
                    </div>
                    <div>
                        <div class="font-extrabold text-gray-900 mb-1">Birthdate format</div>
                        Use the calendar picker to avoid invalid dates.
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const civilStatusSelect = document.getElementById('civil_status');
        if (civilStatusSelect) {
            civilStatusSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
