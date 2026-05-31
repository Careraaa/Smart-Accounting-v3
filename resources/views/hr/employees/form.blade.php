@extends('layouts.layout')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

        .emp-page {
            font-family: 'Sora', sans-serif;
        }

        /* ── Topbar ─────────────────────────────────────────────────── */
        .emp-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .emp-topbar-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.02em;
            margin: 0 0 2px;
        }

        .emp-topbar-sub {
            font-size: 0.78rem;
            color: #9ca3af;
            margin: 0;
        }

        .emp-btn-sec {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .emp-btn-sec:hover {
            border-color: #c8292a;
            color: #c8292a;
            background: #fff5f5;
        }

        /* ── Tab card shell ─────────────────────────────────────────── */
        .emp-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── Tab nav ────────────────────────────────────────────────── */
        .emp-tab-nav {
            display: flex;
            overflow-x: auto;
            border-bottom: 1px solid #e5e7eb;
            background: #f8f9fb;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .emp-tab-nav::-webkit-scrollbar {
            display: none;
        }

        .emp-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 13px 18px;
            font-family: 'Sora', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            color: #6b7280;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            white-space: nowrap;
            transition: color 0.13s, border-color 0.13s;
            margin-bottom: -1px;
        }

        .emp-tab-btn:hover {
            color: #111827;
        }

        .emp-tab-btn.active {
            color: #c8292a;
            border-bottom-color: #c8292a;
            background: #fff;
        }

        .emp-tab-num {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 0.62rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.13s, color 0.13s;
            flex-shrink: 0;
        }

        .emp-tab-btn.active .emp-tab-num {
            background: #c8292a;
            color: #fff;
        }

        .emp-tab-btn.done .emp-tab-num {
            background: #16a34a;
            color: #fff;
        }

        /* ── Tab content ────────────────────────────────────────────── */
        .emp-tab-body {
            padding: 28px 28px 8px;
        }

        .emp-tab-pane {
            display: none;
        }

        .emp-tab-pane.active {
            display: block;
        }

        /* ── Form nav footer ────────────────────────────────────────── */
        .emp-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 28px;
            border-top: 1px solid #f3f4f6;
            background: #fafafa;
        }

        .emp-form-footer-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .emp-btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 0.845rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
            text-decoration: none;
        }

        .emp-btn-prev {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .emp-btn-prev:hover {
            border-color: #c8292a;
            color: #c8292a;
            background: #fff5f5;
        }

        .emp-btn-next {
            background: #111827;
            color: #fff;
        }

        .emp-btn-next:hover {
            background: #000;
            color: #fff;
        }

        .emp-btn-submit {
            background: #c8292a;
            color: #fff;
            box-shadow: 0 4px 14px rgba(200, 41, 42, 0.35);
        }

        .emp-btn-submit:hover {
            background: #a81f20;
            color: #fff;
        }

        /* ── Shared form field styles (used by all partials) ────────── */
        .emp-section-title-form {
            font-size: 1rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.01em;
            margin: 0 0 20px;
        }

        .emp-section-sub {
            font-size: 0.78rem;
            color: #9ca3af;
            margin-top: -14px;
            margin-bottom: 18px;
        }

        .emp-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .emp-label .req {
            color: #c8292a;
        }

        .emp-label .opt {
            color: #9ca3af;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            font-size: 0.72rem;
        }

        .emp-input,
        .emp-select,
        .emp-textarea {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.845rem;
            font-family: 'Sora', sans-serif;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            appearance: none;
            -webkit-appearance: none;
        }

        .emp-input:focus,
        .emp-select:focus,
        .emp-textarea:focus {
            border-color: #c8292a;
            box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
        }

        .emp-input::placeholder,
        .emp-textarea::placeholder {
            color: #9ca3af;
        }

        .emp-input:read-only,
        .emp-input[readonly] {
            background: #f9fafb;
            color: #6b7280;
        }

        .emp-input.is-invalid,
        .emp-select.is-invalid,
        .emp-textarea.is-invalid {
            border-color: #ef4444 !important;
        }

        .emp-invalid {
            display: block;
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 4px;
        }

        /* Select wrapper with chevron */
        .emp-select-wrap {
            position: relative;
        }

        .emp-select-wrap .emp-chevron {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .emp-select-wrap .emp-select {
            padding-right: 36px;
        }

        /* Input with prefix */
        .emp-input-group {
            display: flex;
            align-items: stretch;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .emp-input-group:focus-within {
            border-color: #c8292a;
            box-shadow: 0 0 0 3px rgba(200, 41, 42, 0.08);
        }

        .emp-input-group-text {
            display: flex;
            align-items: center;
            padding: 0 12px;
            background: #f9fafb;
            color: #9ca3af;
            font-size: 0.845rem;
            border-right: 1px solid #e5e7eb;
            white-space: nowrap;
            font-family: 'DM Mono', monospace;
        }

        .emp-input-group .emp-input {
            border: none;
            border-radius: 0;
            box-shadow: none !important;
        }

        /* Hint text */
        .emp-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 5px;
        }

        /* Section divider */
        .emp-divider {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #9ca3af;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 10px;
            margin: 24px 0 18px;
        }

        /* Checkbox row */
        .emp-check-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .emp-check-input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #e5e7eb;
            accent-color: #c8292a;
            cursor: pointer;
        }

        .emp-check-label {
            font-size: 0.845rem;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }

        /* Dynamic rows (beneficiaries, skills, etc.) */
        .emp-dynamic-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 12px;
        }

        .emp-dynamic-row {
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            position: relative;
        }

        .emp-dynamic-remove {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #fecaca;
            color: #c8292a;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.13s;
            padding: 0;
        }

        .emp-dynamic-remove:hover {
            background: #fff0f0;
        }

        .emp-dynamic-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .emp-dynamic-add:hover {
            border-color: #c8292a;
            color: #c8292a;
            background: #fff5f5;
        }

        /* Alert boxes */
        .emp-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .emp-alert.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .emp-alert.info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .emp-alert.error {
            background: #fff0f0;
            border: 1px solid #fecaca;
            color: #c8292a;
        }

        /* Attachment card in form */
        .emp-att-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: border-color 0.13s;
        }

        .emp-att-form-card.has-file {
            border-color: #16a34a;
        }

        .emp-att-form-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .emp-att-preview {
            width: 100%;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .emp-att-file-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .emp-att-file-row a {
            font-size: 0.78rem;
            color: #374151;
            text-decoration: none;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .emp-att-file-row a:hover {
            color: #c8292a;
        }

        .emp-att-time {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        /* Errors summary */
        .emp-errors-card {
            background: #fff0f0;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .emp-errors-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #c8292a;
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .emp-errors-list {
            margin: 0;
            padding-left: 18px;
            font-size: 0.8rem;
            color: #b91c1c;
            line-height: 1.7;
        }

        /* Grid helpers */
        .emp-row {
            display: grid;
            gap: 16px;
        }

        .emp-row.cols-2 {
            grid-template-columns: 1fr 1fr;
        }

        .emp-row.cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .emp-row.cols-4 {
            grid-template-columns: 1fr 1fr 1fr 1fr;
        }

        @media (max-width:768px) {

            .emp-row.cols-3,
            .emp-row.cols-4 {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width:520px) {

            .emp-row.cols-2,
            .emp-row.cols-3,
            .emp-row.cols-4 {
                grid-template-columns: 1fr;
            }
        }

        .emp-field {
            /* just a container */
        }

        /* Username preview box */
        .emp-preview-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 14px;
            font-family: 'DM Mono', monospace;
            font-size: 0.845rem;
            color: #6b7280;
            min-height: 42px;
        }
    </style>
@endpush

@section('content')
    @php $isEdit = isset($employee) && $employee->id; @endphp

    <div class="emp-page">

        {{-- Topbar --}}
        <div class="emp-topbar">
            <div>
                <h1 class="emp-topbar-title">{{ $isEdit ? 'Edit Employee' : 'Add New Employee' }}</h1>
                <p class="emp-topbar-sub">
                    {{ $isEdit ? 'Update the employee profile and information' : 'Fill out each section to create a new employee record' }}
                </p>
            </div>
            <a href="{{ route('employees.index') }}" class="emp-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        {{-- Errors summary --}}
        @if ($errors->any())
            <div class="emp-errors-card">
                <p class="emp-errors-title">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
                    </svg>
                    Please fix the following errors
                </p>
                <ul class="emp-errors-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form card --}}
        <div class="emp-form-card">

            {{-- Tab navigation --}}
            @php
                $tabs = [
                    'Personal Info',
                    'Employment',
                    'Account',
                    'Government Numbers',
                    'Work Experience',
                    'Special Skills',
                    'Beneficiaries',
                    'Character References',
                    'Attachments',
                ];
            @endphp
            <nav class="emp-tab-nav" id="empTabNav">
                @foreach ($tabs as $i => $tab)
                    <button type="button" class="emp-tab-btn {{ $i === 0 ? 'active' : '' }}"
                        data-tab="{{ $i }}">
                        <span class="emp-tab-num">{{ $i + 1 }}</span>
                        {{ $tab }}
                    </button>
                @endforeach
            </nav>

            {{-- Form --}}
            <form action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}" method="POST"
                enctype="multipart/form-data" novalidate id="empForm">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="emp-tab-body">
                    <div class="emp-tab-pane active" data-pane="0">@include('partials.employee.personal')</div>
                    <div class="emp-tab-pane" data-pane="1">@include('partials.employee.employment_hr') @include('partials.employee.employment')</div>
                    <div class="emp-tab-pane" data-pane="2">@include('partials.employee.account')</div>
                    <div class="emp-tab-pane" data-pane="3">@include('partials.employee.government')</div>
                    <div class="emp-tab-pane" data-pane="4">@include('partials.employee.work_experience')</div>
                    <div class="emp-tab-pane" data-pane="5">@include('partials.employee.skills')</div>
                    <div class="emp-tab-pane" data-pane="6">@include('partials.employee.beneficiaries')</div>
                    <div class="emp-tab-pane" data-pane="7">@include('partials.employee.references')</div>
                    <div class="emp-tab-pane" data-pane="8">@include('partials.employee.attachments')</div>
                </div>

                <div class="emp-form-footer">
                    <button type="button" class="emp-btn-nav emp-btn-prev" id="empPrev" style="visibility:hidden;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Previous
                    </button>
                    <div class="emp-form-footer-right">
                        <button type="button" class="emp-btn-nav emp-btn-next" id="empNext">
                            Next
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                        <button type="submit" class="emp-btn-nav emp-btn-submit" id="empSubmit" style="display:none;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $isEdit ? 'Update Employee' : 'Add Employee' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const totalTabs = {{ count($tabs) }};
            let current = 0;

            const tabs = document.querySelectorAll('.emp-tab-btn');
            const panes = document.querySelectorAll('.emp-tab-pane');
            const prev = document.getElementById('empPrev');
            const next = document.getElementById('empNext');
            const submit = document.getElementById('empSubmit');

            function goTo(idx) {
                tabs[current].classList.remove('active');
                panes[current].classList.remove('active');
                if (idx > current) tabs[current].classList.add('done');
                else tabs[idx].classList.remove('done');

                current = idx;
                tabs[current].classList.add('active');
                panes[current].classList.add('active');

                prev.style.visibility = current === 0 ? 'hidden' : 'visible';
                next.style.display = current === totalTabs - 1 ? 'none' : 'inline-flex';
                submit.style.display = current === totalTabs - 1 ? 'inline-flex' : 'none';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                // Reinitialize datepickers when tab changes
                if (typeof initGlobalDatepickers === "function") {
                    setTimeout(initGlobalDatepickers, 50);
                }
            }

            tabs.forEach((btn, i) => btn.addEventListener('click', () => goTo(i)));
            prev.addEventListener('click', () => {
                if (current > 0) goTo(current - 1);
            });
            next.addEventListener('click', () => {
                if (current < totalTabs - 1) goTo(current + 1);
            });

            // If validation failed, open the first tab containing an invalid field.
            const firstInvalidPane = document.querySelector('.emp-tab-pane .is-invalid')?.closest('.emp-tab-pane');
            if (firstInvalidPane) {
                const paneIndex = Number(firstInvalidPane.getAttribute('data-pane'));
                if (!Number.isNaN(paneIndex) && paneIndex >= 0 && paneIndex < totalTabs) {
                    goTo(paneIndex);
                }
            }
        })();
    </script>
@endpush
