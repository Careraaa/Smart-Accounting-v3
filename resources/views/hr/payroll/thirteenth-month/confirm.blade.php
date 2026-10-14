@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.clickable-row { cursor: pointer; }
.clickable-row:hover { background-color: rgba(0,0,0,0.02); }
</style>
@endpush

@section('content')
<div class="space-y-5">

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @case('info') bg-blue-50 text-blue-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    @if($batch->status === 'rejected' && $batch->rejection_note)
    <div class="fade-up bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-red-700">Batch Rejected</p>
            <p class="text-xs text-red-600 mt-2 bg-red-100/60 rounded-lg px-3 py-2">{{ $batch->rejection_note }}</p>
        </div>
    </div>
    @endif

    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Payroll
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">13th Month Pay &mdash; {{ $batch->period_start->format('Y') }}</h1>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $batch->period_start->format('M d, Y') }} &ndash; {{ $batch->period_end->format('M d, Y') }}</p>
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

    @php
        $totalPayable = $batch->thirteenthMonthPays->sum('thirteenth_month_pay');
    @endphp
    <div class="grid grid-cols-2 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Employees</p>
            <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">{{ $batch->thirteenthMonthPays->count() }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-400 font-medium">Total Payable</p>
            <p class="text-lg font-bold text-amber-600 tabular-nums mt-1">₱{{ number_format($totalPayable, 2) }}</p>
        </div>
    </div>

    @if(in_array($batch->status, ['draft', 'submitted']))
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-end gap-2">
        <button type="button" onclick="openDeleteModal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-xs font-semibold transition-all hover:bg-red-50 active:scale-[0.97] cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete Batch
        </button>
        <button type="button" onclick="openSubmitModal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-violet-700 active:scale-[0.97] cursor-pointer border-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Submit for Approval
        </button>
    </div>
    @elseif($batch->status === 'approved')
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-end gap-2">
        <a href="{{ route('payroll.thirteenth-month-pay.batch.payslips', $batch) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-all hover:bg-emerald-700 active:scale-[0.97] cursor-pointer no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            View Payslips
        </a>
    </div>
    @endif

    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900">Employee Records</span>
            <span class="text-xs text-gray-400 tabular-nums">{{ $batch->thirteenthMonthPays->count() }} records</span>
        </div>

        @if($batch->thirteenthMonthPays->isEmpty())
        <div class="flex flex-col items-center py-12 text-center">
            <p class="text-sm font-semibold text-gray-500">No records in this batch</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Months Worked</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">13th Month Pay</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($batch->thirteenthMonthPays as $record)
                    @php
                        $ps = match($record->status) {
                            'paid' => ['label'=>'Paid','dot'=>'bg-emerald-400','text'=>'text-emerald-600'],
                            'partial' => ['label'=>'Partial','dot'=>'bg-blue-400','text'=>'text-blue-600'],
                            default => ['label'=>'Pending','dot'=>'bg-amber-300','text'=>'text-amber-600'],
                        };
                    @endphp
                    <tr class="clickable-row hover:bg-gray-50/40 transition-colors" onclick="window.location='{{ route('payroll.thirteenth-month-pay.show', $record) }}'">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-[11px] font-bold text-gray-600 uppercase shrink-0">
                                    {{ substr($record->user?->first_name ?? '?', 0, 1) }}{{ substr($record->user?->last_name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $record->user?->name ?? 'Deleted User' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $record->user?->department ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right text-sm text-gray-900 tabular-nums">₱{{ number_format($record->total_basic_salary_earned, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm text-gray-900 tabular-nums">{{ $record->months_worked }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold text-gray-900 tabular-nums">₱{{ number_format($record->thirteenth_month_pay, 2) }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide {{ $ps['text'] }} bg-opacity-10 {{ str_replace('text-', 'bg-', $ps['text']) }}/10">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ps['dot'] }}"></span>
                                {{ $ps['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- Submit Modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="submitModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeSubmitModal()"></div>
    <div class="relative bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl modal-card">
        <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-gray-900">Submit for Approval?</h3>
        <p class="text-sm text-center text-gray-500 mt-1">This will submit <strong>{{ $batch->thirteenthMonthPays->count() }}</strong> 13th month pay record(s) for accountant review.</p>
        <form action="{{ route('payroll.thirteenth-month-pay.batch.submit', $batch) }}" method="POST" class="mt-5 flex gap-2 justify-center">
            @csrf
            <button type="button" onclick="closeSubmitModal()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-violet-600 text-white rounded-lg text-xs font-semibold hover:bg-violet-700 active:scale-[0.97] cursor-pointer border-0">Submit</button>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center p-5 hidden modal-overlay" id="deleteModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl modal-card">
        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold text-center text-gray-900">Delete Batch?</h3>
        <p class="text-sm text-center text-gray-500 mt-1">This will permanently delete this batch and all <strong>{{ $batch->thirteenthMonthPays->count() }}</strong> record(s). This action cannot be undone.</p>
        <form action="{{ route('payroll.thirteenth-month-pay.batch.destroy', $batch) }}" method="POST" class="mt-5 flex gap-2 justify-center">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 active:scale-[0.97] cursor-pointer border-0">Delete</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openSubmitModal() {
    document.getElementById('submitModal').classList.remove('hidden');
}
function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
document.getElementById('submitModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush
