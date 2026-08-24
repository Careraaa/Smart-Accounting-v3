@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.modal-card { animation:scaleIn 0.25s cubic-bezier(0.16,1,0.3,1) both; }
.modal-overlay { animation:fadeSlideUp 0.2s ease-out both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.sticky-actions { position: sticky; top: 65px; z-index: 50; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="flash-bar flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @case('info') bg-blue-50 text-blue-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    {{-- Back + header --}}
    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to batches
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                {{ \Carbon\Carbon::parse($batch->period_start)->format('F Y') }}
                <span class="font-normal text-gray-400">&mdash;</span>
                {{ \Carbon\Carbon::parse($batch->period_start)->format('d') <= 15 ? '1st' : '2nd' }}
            </h1>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">
                {{ \Carbon\Carbon::parse($batch->period_start)->format('M d') }} &ndash; {{ \Carbon\Carbon::parse($batch->period_end)->format('M d, Y') }}
            </p>
        </div>
        @php
            $statusInfo = match($batch->status) {
                'draft' => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
                'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400','text'=>'text-violet-600','bg'=>'bg-violet-50'],
                'approved' => ['label'=>'Approved','dot'=>'bg-emerald-400','text'=>'text-emerald-600','bg'=>'bg-emerald-50'],
                'rejected' => ['label'=>'Rejected','dot'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
                default => ['label'=>'Draft','dot'=>'bg-amber-300','text'=>'text-amber-500','bg'=>'bg-amber-50'],
            };
        @endphp
        <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $statusInfo['dot'] }}"></span>
            {{ $statusInfo['label'] }}
        </span>
    </div>

    {{-- Stats --}}
    @php
        $stats = [
            ['label'=>'Employees','value'=>$batch->employee_count,'accent'=>'rose'],
            ['label'=>'Gross Pay','value'=>'₱'.number_format($batch->total_gross_pay ?? 0,2),'accent'=>'blue'],
            ['label'=>'Net Pay','value'=>'₱'.number_format($batch->total_net_pay,2),'accent'=>'emerald'],
        ];
    @endphp
    <div class="grid grid-cols-3 gap-3">
        @foreach($stats as $s)
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">{{ $s['label'] }}</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Actions --}}
    <div class="fade-up sticky-actions bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5">
                <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="w-3.5 h-3.5 rounded border-gray-300 text-gray-900 focus:ring-2 focus:ring-gray-300 cursor-pointer">
                <label for="selectAll" class="text-xs text-gray-500 cursor-pointer select-none">All</label>
            </div>
            <span class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-300 bg-gray-50 px-2 py-1 rounded-md selected-count" id="selectedCount">0 selected</span>
        </div>
        <div class="flex items-center gap-2">
            @if($batch->isEditable())
            <button type="button" onclick="document.getElementById('deleteBatchModal').classList.remove('hidden')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-red-500 border border-red-200 transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">Delete Batch</button>
            @endif
            <button type="button" onclick="showDeleteModal()" id="deleteSelectedBtn" disabled class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-400 border border-gray-200 transition-all cursor-not-allowed">Remove</button>
            @if($batch->isEditable())
            <button type="button" onclick="prepareSelected()" id="prepareSelectedBtn" disabled class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-400 border border-gray-200 transition-all cursor-not-allowed">Prepare</button>
            <span class="w-px h-5 bg-gray-200"></span>
            <button type="button" onclick="document.getElementById('finalizeModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Finalize &amp; Submit
            </button>
            @endif
        </div>
    </div>

    <form id="prepareForm" method="POST" action="{{ route('payroll.batch.prepare-all', $batch) }}" class="hidden">
        @csrf
        <div id="prepareInputs"></div>
    </form>

    {{-- Add employee panel --}}
    <div id="addEmployeePanel" class="fade-up">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Add Employees</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                {{-- Add Employee --}}
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-2">By Individual</p>
                    <form action="{{ route('payroll.batch.add-employee', $batch) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="user_id" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white" required>
                            <option value="">Select employee&hellip;</option>
                            @foreach($availableEmployees->sortBy(fn($e) => [$e->last_name, $e->first_name]) as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer whitespace-nowrap">Add</button>
                    </form>
                </div>
                {{-- Add by Department --}}
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-2">By Department</p>
                    <form action="{{ route('payroll.batch.add-department', $batch) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="department" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white" required>
                            <option value="">Select department&hellip;</option>
                            <option value="all">All Departments</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer whitespace-nowrap">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="w-10 px-4 py-3">

                        </th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employee</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Basic Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Addl. Earnings</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Gross Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Deductions</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Net Pay</th>
                        <th class="text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($batch->payrolls as $detail)
                    @php $u = $detail->user; @endphp
                    <tr onclick="window.location='{{ route('payroll.batch.edit-employee', [$batch, $detail->id]) }}'" class="transition-colors hover:bg-gray-50/40 cursor-pointer">
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <input type="checkbox" name="selected_ids[]" value="{{ $detail->id }}" onchange="updateSelectedCount()" class="w-3.5 h-3.5 rounded border-gray-300 text-gray-900 focus:ring-2 focus:ring-gray-300 cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $u->last_name }}, {{ $u->first_name }}</p>
                                <p class="text-[0.55rem] text-gray-400 truncate font-mono">{{ $u->position ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($detail->basic_salary ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-emerald-500 tabular-nums">₱{{ number_format(($detail->total_allowances ?? 0) + ($detail->total_bonuses ?? 0) + ($detail->holiday_pay ?? 0) + ($detail->holiday_ot_pay ?? 0),2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-gray-900 tabular-nums">₱{{ number_format($detail->gross_pay ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-semibold text-red-500 tabular-nums">₱{{ number_format($detail->total_deductions ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-left text-xs font-bold text-gray-900 tabular-nums">₱{{ number_format($detail->net_pay,2) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $ds = match($detail->status) {
                                    'pending' => ['label'=>'Pending','dot'=>'bg-amber-400'],
                                    'prepared' => ['label'=>'Prepared','dot'=>'bg-blue-400'],
                                    'submitted' => ['label'=>'Submitted','dot'=>'bg-violet-400'],
                                    'completed' => ['label'=>'Completed','dot'=>'bg-emerald-400'],
                                    'on-hold' => ['label'=>'Hold','dot'=>'bg-sky-400'],
                                    default => ['label'=>'Pending','dot'=>'bg-amber-400'],
                                };
                                if($detail->employee_payroll_status === 'completed') { $ds = ['label'=>'Done','dot'=>'bg-emerald-400']; }
                                elseif($detail->employee_payroll_status === 'on-hold') { $ds = ['label'=>'Hold','dot'=>'bg-sky-400']; }
                            @endphp
                            <span class="inline-flex items-center gap-1 text-[0.5rem] font-semibold uppercase tracking-wide text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ds['dot'] }}"></span>
                                {{ $ds['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-xs text-gray-400">No employees in this batch yet.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-50 bg-gray-50/50">
                        <td class="w-10 px-4 py-3"></td>
                        <td class="px-4 py-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Batch Totals</p>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <p class="text-sm font-semibold text-gray-900 tabular-nums">₱{{ number_format($batch->payrolls->sum(fn($p) => $p->basic_salary ?? 0),2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <p class="text-sm font-semibold text-emerald-500 tabular-nums">₱{{ number_format($batch->payrolls->sum(fn($p) => ($p->total_allowances ?? 0) + ($p->total_bonuses ?? 0) + ($p->holiday_pay ?? 0) + ($p->holiday_ot_pay ?? 0)),2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <p class="text-sm font-semibold text-gray-900 tabular-nums">₱{{ number_format($batch->total_gross_pay ?? 0,2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <p class="text-sm font-semibold text-red-500 tabular-nums">₱{{ number_format($batch->total_deductions ?? 0,2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-left">
                            <p class="text-sm font-bold text-gray-900 tabular-nums">₱{{ number_format($batch->total_net_pay,2) }}</p>
                        </td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


</div>

{{-- Remove single confirmation modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="removeSingleModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('removeSingleModal')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Remove Employee</h3>
                <p class="text-xs text-gray-400 mt-0.5">This will permanently remove their payroll record.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-5 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
            Remove <strong id="removeSingleName" class="font-semibold text-gray-900"></strong> from this batch?
        </p>
        <form id="removeSingleForm" method="POST" data-action="{{ route('payroll.batch.remove-employee', [$batch, 'REPLACE_ME']) }}">
            @csrf @method('DELETE')
            <div class="flex gap-2">
                <button type="button" onclick="closeModal('removeSingleModal')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14m-9 3v6m4-6v6m-6-10V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    Remove
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Bulk delete modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="deleteBulkModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('deleteBulkModal')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Remove Selected</h3>
                <p class="text-xs text-gray-400 mt-0.5">This will permanently remove their payroll records.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-5 bg-red-50/50 rounded-xl px-4 py-3 border border-red-100">
            Remove <strong id="bulkCountDisplay" class="font-semibold text-gray-900"></strong> employee(s) from this batch?
        </p>
        <form id="deleteBulkForm" method="POST" action="{{ route('payroll.batch.remove-selected', $batch) }}">
            @csrf
            <div id="bulkInputs"></div>
            <div class="flex gap-2">
                <button type="button" onclick="closeModal('deleteBulkModal')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14m-9 3v6m4-6v6m-6-10V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    Remove
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete batch confirmation modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="deleteBatchModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('deleteBatchModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Delete Batch</h3>
                <p class="text-xs text-gray-400 mt-0.5">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-5 bg-red-50/50 rounded-xl px-4 py-3 border border-red-100">
            Are you sure you want to delete this batch? All payroll records in this batch will be permanently removed.
        </p>
        <form action="{{ route('payroll.batch.cancel', $batch) }}" method="POST">
            @csrf @method('DELETE')
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('deleteBatchModal').classList.add('hidden')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Never mind</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-red-700 active:scale-[0.97] cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14m-9 3v6m4-6v6m-6-10V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    Yes, delete it
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Finalize confirmation modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="finalizeModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('finalizeModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl modal-card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Finalize &amp; Submit</h3>
                <p class="text-xs text-gray-400 mt-0.5">This will lock payroll data and send to accounting.</p>
            </div>
        </div>
        <div class="text-sm text-gray-600 mb-5 bg-emerald-50/50 rounded-xl px-4 py-3 border border-emerald-100 space-y-2">
            <p>This batch will be <strong class="font-semibold text-gray-900">submitted</strong> and sent to accounting for review.</p>
            <div class="flex items-center gap-2 text-xs text-gray-500 pt-1 border-t border-emerald-100/50">
                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span>All payrolls must be <strong>prepared</strong> before finalizing.</span>
            </div>
        </div>
        <form action="{{ route('payroll.batch.finalize', $batch) }}" method="POST">
            @csrf
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('finalizeModal').classList.add('hidden')" class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Yes, finalize
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleAll(source) {
    document.querySelectorAll('input[name="selected_ids[]"]').forEach(function(cb) { cb.checked = source.checked; });
    updateSelectedCount();
}
function updateSelectedCount() {
    var checked = document.querySelectorAll('input[name="selected_ids[]"]:checked');
    var count = checked.length;
    var el = document.getElementById('selectedCount');
    var delBtn = document.getElementById('deleteSelectedBtn');
    var preBtn = document.getElementById('prepareSelectedBtn');
    if (el) el.textContent = count + ' selected';
    if (count > 0) {
        if (delBtn) { delBtn.disabled = false; delBtn.className = 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-600 border border-red-200 transition-all hover:bg-red-100 active:scale-[0.97] cursor-pointer'; }
        if (preBtn) { preBtn.disabled = false; preBtn.className = 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-600 border border-blue-200 transition-all hover:bg-blue-100 active:scale-[0.97] cursor-pointer'; }
    } else {
        if (delBtn) { delBtn.disabled = true; delBtn.className = 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-400 border border-gray-200 transition-all cursor-not-allowed'; }
        if (preBtn) { preBtn.disabled = true; preBtn.className = 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-400 border border-gray-200 transition-all cursor-not-allowed'; }
    }
}
function prepareSelected() {
    var checked = document.querySelectorAll('input[name="selected_ids[]"]:checked');
    var ids = [];
    checked.forEach(function(cb) { ids.push(cb.value); });
    if (ids.length === 0) return;
    var container = document.getElementById('prepareInputs');
    container.innerHTML = '';
    ids.forEach(function(id) {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'payroll_ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('prepareForm').submit();
}
function confirmRemove(id, name) {
    document.getElementById('removeSingleName').textContent = name;
    document.getElementById('removeSingleForm').action = document.getElementById('removeSingleForm').dataset.action.replace('REPLACE_ME', id);
    openModal('removeSingleModal');
}
function showDeleteModal() {
    var checked = document.querySelectorAll('input[name="selected_ids[]"]:checked');
    var ids = [];
    checked.forEach(function(cb) { ids.push(cb.value); });
    if (ids.length === 0) return;
    document.getElementById('bulkCountDisplay').textContent = ids.length;
    document.getElementById('deleteBulkForm').dataset.ids = ids.join(',');
    openModal('deleteBulkModal');
}
document.getElementById('deleteBulkForm').addEventListener('submit', function(e) {
    var ids = (this.dataset.ids || '').split(',').filter(Boolean);
    var container = document.getElementById('bulkInputs');
    container.innerHTML = '';
    ids.forEach(function(id) {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'payroll_ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
});
function addEmployeePanel() {
    var el = document.getElementById('addEmployeePanel');
    el.classList.toggle('hidden');
}
function openModal(id) {
    document.querySelectorAll('[id$="Modal"]').forEach(function(m) {
        if (m.id !== id) m.classList.add('hidden');
    });
    var m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('hidden');
    var card = m.querySelector('.modal-card');
    if (card) { card.style.animation = 'none'; void card.offsetWidth; card.style.animation = ''; }
}
function closeModal(id) {
    var m = document.getElementById(id);
    if (!m) return;
    m.classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(function(m) {
            if (!m.classList.contains('hidden')) closeModal(m.id);
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
  var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); };
  var targets = document.querySelectorAll('.tabular-nums');
  targets.forEach(function(el, i) {
    if (el.offsetParent === null) return;
    var raw = el.textContent.trim();
    var m = raw.match(/^([+-])?\s*₱?\s*([\d,]+\.\d{2})/);
    if (!m) return;
    var sign = m[1] || '';
    var target = parseFloat(m[2].replace(/,/g, ''));
    var duration = 600 + i * 50;
    var t0 = performance.now();
    function tick(now) {
      var p = Math.min((now - t0) / duration, 1);
      var v = (1 - Math.pow(1 - p, 3)) * Math.abs(target);
      el.textContent = sign + '₱' + fmt(v);
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  });
});
</script>
@endpush
