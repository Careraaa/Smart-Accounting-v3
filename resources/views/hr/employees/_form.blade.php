@php
    /** @var \App\Models\User $employee */
    /** @var bool $isEdit */
@endphp

<div class="emp-page" data-global-datepicker="off">

    @if ($isEdit && empty($employee->gender))
        <div class="emp-alert error">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
            </svg>
            This employee has missing required information from older records (Gender). Please complete it before saving.
        </div>
    @endif

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

