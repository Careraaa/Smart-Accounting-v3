@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Topbar --}}
    <div style="animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;">
        <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">HR Configuration</h1>
        <p class="text-xs text-gray-400 font-semibold mt-0.5">Manage shifts, payroll cutoffs, and attendance settings</p>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm font-semibold" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif
    @if (session('success'))
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm font-semibold" style="animation:fadeSlideUp 0.35s ease both;">
        <svg class="w-4 h-4 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-100" style="animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;">
        <button type="button" class="tab-btn px-5 py-3 text-xs font-bold text-gray-900 border-b-2 border-gray-900 transition-colors hover:text-gray-900" data-tab="shifts">Shifts</button>
        <button type="button" class="tab-btn px-5 py-3 text-xs font-bold text-gray-400 border-b-2 border-transparent transition-colors hover:text-gray-600" data-tab="payroll">Payroll Cutoff</button>
        <button type="button" class="tab-btn px-5 py-3 text-xs font-bold text-gray-400 border-b-2 border-transparent transition-colors hover:text-gray-600" data-tab="attendance">Attendance Rules</button>
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- SHIFTS TAB --}}
    {{-- ════════════════════════════════ --}}
    <div id="tab-shifts" class="tab-panel space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" style="animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><path d="M12 1v6m0 6v6"/><path d="M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24"/><path d="M1 12h6m6 0h6"/><path d="M4.22 19.78l4.24-4.24m2.12-2.12l4.24-4.24"/></svg>
                    <h3 class="text-sm font-bold text-gray-900">Manage Shifts</h3>
                </div>
                <button onclick="openModal('addShiftModal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold transition-all hover:bg-black active:scale-[0.97] cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add New Shift
                </button>
            </div>

            @if($shifts->isEmpty())
            <div class="py-14 text-center">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p class="text-sm font-semibold text-gray-500">No shifts configured</p>
                <p class="text-xs text-gray-400 mt-1">Click "Add New Shift" to create your first shift</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Shift Name</th>
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Start Time</th>
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">End Time</th>
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Break Time</th>
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Work Hours</th>
                            <th class="px-4 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Status</th>
                            <th class="px-4 py-3 text-right text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($shifts as $shift)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $shift->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($shift->break_start && $shift->break_end)
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_start)->format('h:i A') }} – {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_end)->format('h:i A') }}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-[0.6rem] font-bold">{{ $shift->duration }} hrs</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($shift->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-[0.55rem] font-bold uppercase tracking-wide">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 border border-gray-200 text-gray-500 text-[0.55rem] font-bold uppercase tracking-wide">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="openEditShiftModal({{ $shift->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <form action="{{ route('settings.shift.destroy', $shift) }}" method="POST" data-sa-confirm="Are you sure you want to delete this shift?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- PAYROLL CUTOFF TAB --}}
    {{-- ════════════════════════════════ --}}
    <div id="tab-payroll" class="tab-panel hidden space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" style="animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;">
            <div class="flex items-center gap-2.5 mb-5">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <h3 class="text-sm font-bold text-gray-900">Payroll Cutoff Settings</h3>
            </div>

            @if($activeCutoff)
            <div class="flex items-start gap-3 px-4 py-3.5 bg-blue-50 border border-blue-100 rounded-xl mb-5">
                <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div class="text-xs text-blue-800 font-medium">
                    <strong>Current Setting:</strong>
                    @if((int)$activeCutoff->cutoff_day === 15)
                        Bi-monthly (15th of each month)
                    @elseif((int)$activeCutoff->cutoff_day === 30)
                        Monthly (30th of each month)
                    @elseif((int)$activeCutoff->cutoff_day <= 7)
                        Weekly (every 7 days)
                    @else
                        Cutoff on the {{ $activeCutoff->cutoff_day }}th of each month
                    @endif
                    <br>
                    <span class="text-blue-500">Next cutoff: {{ \App\Models\PayrollCutoffSchedule::getNextCutoffDate()?->format('F d, Y') ?? 'Not calculated' }}</span>
                </div>
            </div>
            @endif

            <form action="{{ route('settings.payroll-cutoff.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    @php $freq = $activeCutoff ? ((int)$activeCutoff->cutoff_day <= 7 ? 'weekly' : ((int)$activeCutoff->cutoff_day === 15 ? 'bi-monthly' : ((int)$activeCutoff->cutoff_day === 30 ? 'monthly' : 'custom'))) : 'bi-monthly'; @endphp
                    @foreach(['weekly' => ['Weekly', 'Every 7 days'], 'bi-monthly' => ['Bi-monthly', '15th of each month'], 'monthly' => ['Monthly', '30th of each month'], 'custom' => ['Custom', 'Specify cutoff day']] as $val => [$label, $desc])
                    <div class="cursor-pointer rounded-xl border-2 p-4 text-center transition-all hover:bg-gray-50 freq-card {{ $freq === $val ? 'border-gray-900 bg-gray-50' : 'border-gray-200' }}" data-freq="{{ $val }}">
                        <input type="radio" name="frequency" value="{{ $val }}" {{ $freq === $val ? 'checked' : '' }} class="sr-only">
                        <label class="text-xs font-bold text-gray-900 cursor-pointer">{{ $label }}</label>
                        <p class="text-[0.6rem] text-gray-400 mt-1">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>

                <input type="hidden" name="cutoff_day" id="cutoff_day_hidden" value="{{ old('cutoff_day', $activeCutoff?->cutoff_day ?? 15) }}">

                <div id="custom-cutoff-day" class="{{ $freq === 'custom' ? '' : 'hidden' }} mb-4">
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Cutoff Day (1-31) <span class="text-red-500">*</span></label>
                    <input type="number" id="cutoff_day_visible" min="1" max="31" value="{{ old('cutoff_day', $activeCutoff?->cutoff_day ?? 15) }}" class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>

                <div class="mb-4">
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Label (Optional)</label>
                    <input type="text" name="label" placeholder="e.g., Monthly Payroll" value="{{ old('label', $activeCutoff?->label ?? '') }}" class="w-full max-w-xs border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold transition-all hover:bg-black active:scale-[0.97] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Payroll Cutoff Settings
                </button>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- ATTENDANCE RULES TAB --}}
    {{-- ════════════════════════════════ --}}
    <div id="tab-attendance" class="tab-panel hidden space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" style="animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;">
            <div class="flex items-center gap-2.5 mb-5">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-bold text-gray-900">Attendance Rules</h3>
            </div>

            <form action="{{ route('settings.attendance-settings.update') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Grace Period (Minutes) <span class="text-red-500">*</span></label>
                    <input type="number" name="grace_period" min="0" max="30" value="{{ old('grace_period', $gracePeriod) }}" required class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 {{ $errors->has('grace_period') ? 'border-red-400 ring-2 ring-red-100' : '' }}">
                    <p class="text-[0.6rem] text-gray-400 mt-1">Additional time before marking as late</p>
                    @error('grace_period')
                    <p class="text-[0.6rem] text-red-500 mt-0.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold transition-all hover:bg-black active:scale-[0.97] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Attendance Settings
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════ --}}
{{-- ADD SHIFT MODAL --}}
{{-- ════════════════════════════════ --}}
<div id="addShiftModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Add New Shift</h5>
        </div>
        <form action="{{ route('settings.shift.store') }}" method="POST">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Shift Name</label>
                    <input type="text" name="name" placeholder="e.g., Morning Shift" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Start Time</label>
                    <input type="time" name="start_time" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">End Time</label>
                    <input type="time" name="end_time" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Break Start Time (Optional)</label>
                    <input type="time" name="break_start" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Break End Time (Optional)</label>
                    <input type="time" name="break_end" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-700 font-medium cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-200">
                    Active
                </label>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addShiftModal')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Add Shift</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════ --}}
{{-- EDIT SHIFT MODALS --}}
{{-- ════════════════════════════════ --}}
@foreach($shifts as $shift)
<div id="editShiftModal{{ $shift->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4)">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden" style="animation:scaleIn 0.2s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="px-5 py-4 border-b border-gray-50">
            <h5 class="text-sm font-bold text-gray-900">Edit Shift: {{ $shift->name }}</h5>
        </div>
        <form action="{{ route('settings.shift.update', $shift) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Shift Name</label>
                    <input type="text" name="name" value="{{ $shift->name }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Start Time</label>
                    <p class="text-[0.6rem] text-gray-400 mb-1">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}</p>
                    <input type="time" name="start_time" value="{{ substr($shift->start_time, 0, 5) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">End Time</label>
                    <p class="text-[0.6rem] text-gray-400 mb-1">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }}</p>
                    <input type="time" name="end_time" value="{{ substr($shift->end_time, 0, 5) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Break Start Time (Optional)</label>
                    @if($shift->break_start)
                        <p class="text-[0.6rem] text-gray-400 mb-1">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_start)->format('h:i A') }}</p>
                    @endif
                    <input type="time" name="break_start" value="{{ $shift->break_start ? substr($shift->break_start, 0, 5) : '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <div>
                    <label class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500 mb-1 block">Break End Time (Optional)</label>
                    @if($shift->break_end)
                        <p class="text-[0.6rem] text-gray-400 mb-1">Current: {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->break_end)->format('h:i A') }}</p>
                    @endif
                    <input type="time" name="break_end" value="{{ $shift->break_end ? substr($shift->break_end, 0, 5) : '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-700 font-medium cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $shift->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-200">
                    Active
                </label>
            </div>
            <div class="px-5 py-4 border-t border-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editShiftModal{{ $shift->id }}')" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-black transition-all cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// ── Tab switching ──
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name = this.dataset.tab;
        document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.add('hidden'); });
        document.getElementById('tab-' + name).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('text-gray-900', 'border-gray-900');
            b.classList.add('text-gray-400', 'border-transparent');
        });
        this.classList.remove('text-gray-400', 'border-transparent');
        this.classList.add('text-gray-900', 'border-gray-900');
    });
});

// ── Frequency card selector ──
document.querySelectorAll('.freq-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('.freq-card').forEach(function(c) {
            c.classList.remove('border-gray-900', 'bg-gray-50');
            c.classList.add('border-gray-200');
        });
        this.classList.remove('border-gray-200');
        this.classList.add('border-gray-900', 'bg-gray-50');
        this.querySelector('input[type="radio"]').checked = true;

        var hiddenInput = document.getElementById('cutoff_day_hidden');
        var visibleInput = document.getElementById('cutoff_day_visible');
        var freq = this.dataset.freq;
        var vals = { weekly: 7, 'bi-monthly': 15, monthly: 30 };
        if (vals[freq] !== undefined) {
            hiddenInput.value = vals[freq];
            if (visibleInput) visibleInput.value = vals[freq];
            document.getElementById('custom-cutoff-day').classList.add('hidden');
        } else {
            if (visibleInput) visibleInput.focus();
            document.getElementById('custom-cutoff-day').classList.remove('hidden');
            if (visibleInput) {
                visibleInput.oninput = function() { hiddenInput.value = this.value; };
            }
        }
    });
});

// ── Modal helpers ──
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openEditShiftModal(id) {
    openModal('editShiftModal' + id);
}
// Close modals on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(function(el) {
    if (el.id === 'addShiftModal' || el.id.startsWith('editShiftModal')) {
        el.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    }
});
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').forEach(function(m) {
            m.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }
});
</script>
@endpush
@endsection
