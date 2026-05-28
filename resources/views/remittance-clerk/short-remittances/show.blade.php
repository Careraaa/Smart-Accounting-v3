@extends('layouts.layout')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-5">
    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Short Remittance Details</h1>
                <p class="text-sm text-gray-500">Review shortage breakdown and resolution status.</p>
            </div>
        </div>
        <a href="{{ route('short-remittances.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7-7 7 7 7"/></svg>
            Back
        </a>
    </div>

    {{-- Summary Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Summary</h2>
        </div>
        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-3">
                    <h3 class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Remittance Information</h3>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Date</span>
                        <p class="text-xs font-semibold text-gray-800">{{ $shortRemittance->remittance_date?->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Status</span>
                        <p>
                            @if($shortRemittance->status === 'approved')
                                <span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                            @elseif($shortRemittance->status === 'rejected')
                                <span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-red-50 text-red-700 border border-red-200">Rejected</span>
                            @else
                                <span class="inline-block text-[0.6rem] font-bold px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h3 class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Vehicle Information</h3>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Plate Number</span>
                        <p class="text-xs font-semibold text-gray-800 font-mono">{{ $shortRemittance->vehicle->plate_number }}</p>
                    </div>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Route</span>
                        <p class="text-xs font-semibold text-gray-800">{{ $shortRemittance->route->route_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Personnel Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-3">
                    <h3 class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">Driver Information</h3>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Name</span>
                        <p class="text-xs font-semibold text-gray-800">{{ $shortRemittance->driver->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Liability (50% of Short Amount)</span>
                        <p class="text-xl font-bold text-sky-700">₱{{ number_format($shortRemittance->driver_share, 2) }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h3 class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">PAO Information</h3>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Name</span>
                        <p class="text-xs font-semibold text-gray-800">{{ $shortRemittance->pao->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-0.5">Liability (50% of Short Amount)</span>
                        <p class="text-xl font-bold text-emerald-600">₱{{ number_format($shortRemittance->pao_share, 2) }}</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Financial Breakdown --}}
            <div>
                <h3 class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400 mb-3">Financial Breakdown</h3>
                <div class="bg-gray-50 rounded-xl p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Collection</span>
                            <p class="text-sm font-bold text-gray-800">₱{{ number_format($shortRemittance->total_collection, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Expenses</span>
                            <p class="text-sm font-bold text-gray-800">₱{{ number_format($shortRemittance->total_expenses, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Boundary Rate</span>
                            <p class="text-sm font-bold text-gray-800">₱{{ number_format($shortRemittance->boundary ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <hr class="border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-center">
                        <div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Net Remittance (Recorded)</span>
                            <p class="text-xl font-bold" @if($shortRemittance->net_remittance < 0) style="color: #dc2626;" @else style="color: #111827;" @endif>₱{{ number_format($shortRemittance->net_remittance, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Short Amount</span>
                            <p class="text-xl font-bold text-red-600">₱{{ number_format($shortRemittance->short_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2.5 justify-end pt-3 border-t border-gray-100">
                <a href="{{ route('short-remittances.edit', $shortRemittance) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer no-underline inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Mark Resolution
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
