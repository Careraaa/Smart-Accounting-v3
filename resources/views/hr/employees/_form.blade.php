<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:border-red-300 dark:hover:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-150 active:scale-[0.92]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                {{ ($isEdit ?? false) ? 'Edit Employee' : 'Add New Employee' }}
            </h1>
            @if(isset($employee) && $employee->employee_code)
                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700">
                    {{ $employee->employee_code }}
                </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold no-underline cursor-pointer transition-all duration-150 hover:border-red-400 dark:hover:border-red-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97] whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </a>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-5 py-4 mb-5">
            <div class="text-xs font-bold text-red-500 dark:text-red-400 mb-2 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Please fix the following {{ $errors->count() > 1 ? 'errors' : 'error' }}
            </div>
            <ul class="m-0 pl-4 text-xs text-red-600 dark:text-red-400 leading-relaxed">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info Alert --}}
    <div class="flex items-start gap-2.5 px-4 py-3.5 rounded-xl text-xs font-medium mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Please fill out all required fields marked with <span class="text-red-500 dark:text-red-400">*</span>. You can navigate between tabs using the navigation below.</span>
    </div>

    {{-- Form Shell --}}
    <form method="POST" action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden transition-colors duration-200">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Tab Navigation --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 overflow-x-auto tab-nav-scroll">
            <div class="flex gap-1 min-w-max py-3" id="tabNav">
                @php
                    $tabDefs = [
                        'personal'        => ['Personal Info',    '1', 'rose',    'rose-500',    'rose-400'],
                        'employment'      => ['Employment',       '2', 'orange',  'orange-500',  'orange-400'],
                        'account'         => ['Account',          '3', 'amber',   'amber-500',   'amber-400'],
                        'government'      => ["Gov't Numbers",    '4', 'emerald','emerald-500', 'emerald-400'],
                        'work_experience' => ['Experience',       '5', 'teal',    'teal-500',    'teal-400'],
                        'skills'          => ['Skills',           '6', 'cyan',    'cyan-500',    'cyan-400'],
                        'beneficiaries'   => ['Beneficiaries',    '7', 'blue',    'blue-500',    'blue-400'],
                        'references'      => ['References',       '8', 'violet',  'violet-500',  'violet-400'],
                    ];
                @endphp
                @foreach($tabDefs as $key => [$label, $num, $color, $c500, $c400])
                    <button type="button" data-tab="{{ $key }}" data-color="{{ $color }}"
                        class="tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 border-b-2
                            {{ $loop->first ? 'active text-'.$c500.' dark:text-'.$c400.' border-'.$c500.' dark:border-'.$c400.' bg-'.$color.'-50 dark:bg-'.$color.'-900/20' : 'text-gray-500 dark:text-gray-400 border-transparent' }}
                            hover:text-{{ $c500 }} dark:hover:text-{{ $c400 }} hover:bg-{{ $color }}-50 dark:hover:bg-{{ $color }}-900/20 active:scale-[0.96]">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md text-[0.65rem] font-bold transition-colors duration-150
                            {{ $loop->first ? 'bg-'.$c500.' dark:bg-'.$c400.' text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                            {{ $num }}
                        </span>
                        <span class="hidden sm:inline">{{ $label }}</span>
                        <span class="inline sm:hidden">{{ explode(' ', $label)[0] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Tab Panes --}}
        <div>
            @php $tabKeys = ['personal', 'employment', 'account', 'government', 'work_experience', 'skills', 'beneficiaries', 'references']; @endphp
            @foreach($tabKeys as $key)
                <div data-tab-pane="{{ $key }}"
                    class="p-4 sm:p-6 lg:p-8 {{ $loop->first ? '' : 'hidden' }}">
                    @include('partials.employee.' . $key)
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-3">
            <button type="button" data-direction="prev"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-400 dark:hover:border-red-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97] whitespace-nowrap opacity-50 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Previous
            </button>
            <div class="flex items-center gap-2">
                <button type="button" data-direction="next"
                    class="inline-flex items-center gap-1.5 px-5 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-400 dark:hover:border-red-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97] whitespace-nowrap">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="submit" data-primary-submit
                    class="inline-flex items-center gap-1.5 px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-bold cursor-pointer transition-all duration-150 hover:scale-[1.02] active:scale-[0.97] shadow-sm whitespace-nowrap border border-red-500 hover:border-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Employee
                </button>
            </div>
        </div>
    </form>
</div>

@include('hr.employees._scripts')
