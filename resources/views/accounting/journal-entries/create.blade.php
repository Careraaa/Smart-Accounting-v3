@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.line-enter { animation:fadeSlideUp 0.3s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="fade-up">
        <a href="{{ route('accounting.journal-entries.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">New Journal Entry</h1>
        <p class="text-xs text-gray-400 mt-0.5">Create a double-entry accounting transaction</p>
    </div>

    {{-- Form --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('accounting.journal-entries.store') }}" id="jeForm">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="transaction_date" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Transaction Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 outline-none transition-all focus:border-gray-400 focus:bg-white @error('transaction_date') border-red-300 @enderror">
                    @error('transaction_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">
                        Description <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" required
                           placeholder="e.g. Payroll – August 1-15, 2026"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-300 outline-none transition-all focus:border-gray-400 focus:bg-white @error('description') border-red-300 @enderror">
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="reference_type" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Reference Type</label>
                    <input type="text" name="reference_type" id="reference_type" value="{{ old('reference_type') }}"
                           placeholder="e.g. Payroll, Invoice, Receipt"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-300 outline-none transition-all focus:border-gray-400 focus:bg-white">
                </div>
                <div>
                    <label for="reference_id" class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Reference ID</label>
                    <input type="number" name="reference_id" id="reference_id" value="{{ old('reference_id') }}"
                           placeholder="e.g. 123"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-300 outline-none transition-all focus:border-gray-400 focus:bg-white">
                </div>
            </div>

            {{-- Lines --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-bold uppercase tracking-wide text-gray-500">Journal Lines <span class="text-rose-500">*</span></label>
                    <button type="button" onclick="addLine()" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[0.65rem] font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] cursor-pointer">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Line
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="linesTable">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-2 py-2 w-[35%]">Account</th>
                                <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-2 py-2 w-[25%]">Description</th>
                                <th class="text-right text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-2 py-2 w-[17%]">Debit (₱)</th>
                                <th class="text-right text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-2 py-2 w-[17%]">Credit (₱)</th>
                                <th class="w-[6%]"></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody" class="divide-y divide-gray-50">
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Balance Summary --}}
            <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between flex-wrap gap-4" id="balanceSummary">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Total Debit</p>
                        <p class="text-sm font-bold text-gray-900 tabular-nums" id="totalDebit">₱0.00</p>
                    </div>
                    <div>
                        <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Total Credit</p>
                        <p class="text-sm font-bold text-gray-900 tabular-nums" id="totalCredit">₱0.00</p>
                    </div>
                </div>
                <div id="balanceStatus" class="text-sm font-bold text-gray-400">
                    Add lines to see balance
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <a href="{{ route('accounting.journal-entries.index') }}"
                   class="flex-1 py-2.5 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] text-center no-underline">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-0" id="submitBtn">
                    Create Journal Entry
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $accountsJson = $accounts->map(fn($a) => ['id' => $a->id, 'code' => $a->account_code, 'name' => $a->account_name])->toJson();
@endphp

@endsection

@push('scripts')
<script>
const accounts = {!! $accountsJson !!};
let lineIndex = 0;

function addLine() {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    tr.className = 'line-enter';
    tr.dataset.index = lineIndex;

    const optionsHtml = accounts.map(a => `<option value="${a.id}">${a.code} – ${a.name}</option>`).join('');

    tr.innerHTML = `
        <td class="px-2 py-1.5">
            <select name="lines[${lineIndex}][account_id]" required
                    class="w-full text-xs border border-gray-200 rounded-lg px-2 py-2 outline-none transition-all focus:border-gray-400 bg-white">
                <option value="">Select…</option>
                ${optionsHtml}
            </select>
        </td>
        <td class="px-2 py-1.5">
            <input type="text" name="lines[${lineIndex}][description]" placeholder="Line description"
                   class="w-full text-xs border border-gray-200 rounded-lg px-2 py-2 outline-none transition-all focus:border-gray-400 bg-white placeholder:text-gray-300">
        </td>
        <td class="px-2 py-1.5">
            <input type="text" name="lines[${lineIndex}][debit]" value="0" data-type="debit"
                   class="line-amount w-full text-xs border border-gray-200 rounded-lg px-2 py-2 outline-none transition-all focus:border-gray-400 bg-white text-right tabular-nums"
                   onfocus="this.select()">
        </td>
        <td class="px-2 py-1.5">
            <input type="text" name="lines[${lineIndex}][credit]" value="0" data-type="credit"
                   class="line-amount w-full text-xs border border-gray-200 rounded-lg px-2 py-2 outline-none transition-all focus:border-gray-400 bg-white text-right tabular-nums"
                   onfocus="this.select()">
        </td>
        <td class="px-2 py-1.5 text-center">
            <button type="button" onclick="removeLine(this)"
                    class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer border-0 bg-transparent">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    lineIndex++;
    bindAmountListeners();
}

function removeLine(btn) {
    const tr = btn.closest('tr');
    tr.style.opacity = '0';
    tr.style.transform = 'translateX(20px)';
    tr.style.transition = 'all 0.2s ease';
    setTimeout(() => {
        tr.remove();
        recalculate();
    }, 200);
}

function bindAmountListeners() {
    document.querySelectorAll('.line-amount').forEach(input => {
        input.removeEventListener('input', recalculate);
        input.addEventListener('input', recalculate);
    });
}

function recalculate() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.line-amount').forEach(input => {
        const val = parseFloat(input.value) || 0;
        if (input.dataset.type === 'debit') totalDebit += val;
        else totalCredit += val;
    });

    document.getElementById('totalDebit').textContent = '₱' + totalDebit.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('totalCredit').textContent = '₱' + totalCredit.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});

    const statusEl = document.getElementById('balanceStatus');
    const submitBtn = document.getElementById('submitBtn');
    const lineCount = document.querySelectorAll('#linesBody tr').length;

    if (lineCount < 2) {
        statusEl.innerHTML = '<span class="text-gray-400">Need at least 2 lines</span>';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
        statusEl.innerHTML = '<span class="text-emerald-600 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> BALANCED</span>';
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        statusEl.innerHTML = '<span class="text-red-600 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg> NOT BALANCED</span>';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

// Start with 2 lines
addLine();
addLine();
</script>
@endpush
