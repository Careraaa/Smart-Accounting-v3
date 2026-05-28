@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pay Slips</h1>
            <p class="text-sm text-gray-400 mt-0.5">Browse payroll batches and view employee payslips</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="batchSearch" placeholder="Search batch…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="statusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
        </select>
    </div>

    {{-- Batch cards --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50" id="batchGrid">
            @forelse($batches as $batch)
                @php
                    $startDate = $batch->period_start;
                    $endDate   = $batch->period_end;
                    $isFirst   = $startDate->format('d') <= 15;
                    $statusColors = [
                        'submitted' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'dot' => 'bg-violet-500'],
                        'approved'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                        'rejected'  => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
                        'paid'      => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                    ];
                    $si = $statusColors[$batch->status] ?? $statusColors['submitted'];
                    $payslipsUrl = route('payroll.batch.payslips', $batch);
                @endphp
                <div data-status="{{ $batch->status }}" class="batch-col">
                    <a href="{{ $payslipsUrl }}" class="block no-underline text-inherit">
                        <div class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-gray-50/60">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $isFirst ? 'bg-gradient-to-br from-red-600 to-red-800' : 'bg-gradient-to-br from-sky-600 to-blue-800' }} text-white">
                                @if($isFirst)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $startDate->format('F Y') }} — {{ $isFirst ? '1st' : '2nd' }} half</p>
                                <p class="text-[0.55rem] font-mono text-gray-400 mt-0.5">{{ $startDate->format('M d') }} – {{ $endDate->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-5 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.55rem] font-semibold border {{ $si['bg'] }} {{ $si['text'] }} {{ $si['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $si['dot'] }}"></span>
                                    {{ $si['text'] === 'text-violet-700' ? 'Submitted' : ucfirst($batch->status) }}
                                </span>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Employees</p>
                                    <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">{{ $batch->payrolls_count ?? $batch->payrolls->count() }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
                                    <p class="text-sm font-bold text-emerald-600 tabular-nums mt-0.5">₱{{ number_format($batch->total_net_pay, 2) }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="flex flex-col items-center py-12 text-center">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No payroll batches yet</p>
                    <p class="text-xs text-gray-400 mt-0.5">Generate a payroll batch first to view payslips.</p>
                </div>
            @endforelse
        </div>
        <div id="batchNoResults" class="hidden">
            <div class="flex flex-col items-center py-8 text-center">
                <p class="text-sm font-semibold text-gray-500">No results</p>
                <p class="text-xs text-gray-400 mt-0.5">Try adjusting your search or filters.</p>
            </div>
        </div>
        @if($batches->hasPages())
        <div class="px-5 py-3 border-t border-gray-50 flex justify-end text-xs">{{ $batches->withQueryString()->links() }}</div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    var search=document.getElementById('batchSearch'),statusF=document.getElementById('statusFilter');
    var grid=document.getElementById('batchGrid'),noRes=document.getElementById('batchNoResults');
    if(!grid) return;
    function run(){
        var q=(search?.value||'').toLowerCase().trim(),st=statusF?.value||'';
        var visible=0;
        grid.querySelectorAll('.batch-col').forEach(function(col){
            var match=(!q||col.textContent.toLowerCase().includes(q))&&(!st||col.dataset.status===st);
            col.style.display=match?'':'none';
            if(match) visible++;
        });
        if(noRes)noRes.style.display=visible===0?'':'none';
    }
    statusF?.addEventListener('change',function(){
        var url=new URL(window.location.href);
        if(this.value)url.searchParams.set('status',this.value);else url.searchParams.delete('status');
        window.location=url.toString();
    });
    search?.addEventListener('input',run);
    run();
})();
</script>
@endpush
