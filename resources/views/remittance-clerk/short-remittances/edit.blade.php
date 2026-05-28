@extends('layouts.layout')
@section('content')
{{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Resolve Short Remittance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Record payments and mark driver/PAO liabilities as settled.</p>
        </div>
        <a href="{{ route('short-remittances.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7-7 7 7 7"/></svg>
            Close
        </a>
    </div>

    {{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4
            @if($t==='success') bg-emerald-500/15 border border-emerald-500/25 text-emerald-700
            @elseif($t==='error') bg-red-500/15 border border-red-500/25 text-red-700
            @else bg-blue-500/15 border border-blue-500/25 text-blue-700 @endif">
            @if($t==='success')
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach
    @if($errors->any())
    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500/15 border border-red-500/25 rounded-lg text-red-700 text-xs font-semibold mb-4">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        Please fix the errors below.
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 py-3.5 border-b border-gray-50">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Resolution Details</h2>
                </div>
                <div class="p-5">
                    {{-- Alert summary --}}
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 mb-5">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-xs font-bold text-amber-800 mb-1">Short Remittance on {{ $shortRemittance->remittance_date?->format('F d, Y') }}</p>
                            <p class="text-[0.65rem] text-amber-700 mb-0.5">Vehicle: <strong>{{ $shortRemittance->vehicle->plate_number }}</strong></p>
                            <p class="text-[0.65rem] text-amber-700 mb-0.5">Short Amount: <strong style="color: #dc2626;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong></p>
                            <p class="text-[0.65rem] text-amber-700 mb-0">Driver & PAO each liable for: <strong>₱{{ number_format($shortRemittance->driver_share, 2) }}</strong></p>
                        </div>
                    </div>

                    <form action="{{ route('short-remittances.update', $shortRemittance) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Driver Resolution --}}
                        <div class="mb-6">
                            <h3 class="flex items-center gap-2 text-[0.65rem] font-bold uppercase tracking-wider text-gray-400 mb-4">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Driver Resolution
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Driver Name</label>
                                    <input type="text" value="{{ $shortRemittance->driver->name ?? 'N/A' }}" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Driver Liability</label>
                                    <input type="text" value="₱{{ number_format($shortRemittance->driver_share, 2) }}" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Amount Paid <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">₱</span>
                                        <input type="number" step="0.01" name="driver_amount_paid" id="driver_amount_paid" value="{{ old('driver_amount_paid', $shortRemittance->driver_amount_paid ?? 0) }}" min="0" max="{{ $shortRemittance->driver_share }}" class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('driver_amount_paid') border-red-300 @enderror">
                                    </div>
                                    @error('driver_amount_paid') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Remaining Balance</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">₱</span>
                                        <input type="text" id="driver_remaining" value="0.00" disabled class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Status</label>
                                    <select name="driver_status" id="driver_status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('driver_status') border-red-300 @enderror">
                                        <option value="">-- Select Status --</option>
                                        <option value="pending" @selected(old('driver_status', $shortRemittance->driver_status) === 'pending')>Pending</option>
                                        <option value="partial" @selected(old('driver_status', $shortRemittance->driver_status) === 'partial')>Partial Payment</option>
                                        <option value="paid" @selected(old('driver_status', $shortRemittance->driver_status) === 'paid')>Fully Paid</option>
                                    </select>
                                    @error('driver_status') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-6">

                        {{-- PAO Resolution --}}
                        <div class="mb-6">
                            <h3 class="flex items-center gap-2 text-[0.65rem] font-bold uppercase tracking-wider text-gray-400 mb-4">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                PAO Resolution
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">PAO Name</label>
                                    <input type="text" value="{{ $shortRemittance->pao->name ?? 'N/A' }}" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">PAO Liability</label>
                                    <input type="text" value="₱{{ number_format($shortRemittance->pao_share, 2) }}" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Amount Paid <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">₱</span>
                                        <input type="number" step="0.01" name="pao_amount_paid" id="pao_amount_paid" value="{{ old('pao_amount_paid', $shortRemittance->pao_amount_paid ?? 0) }}" min="0" max="{{ $shortRemittance->pao_share }}" class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('pao_amount_paid') border-red-300 @enderror">
                                    </div>
                                    @error('pao_amount_paid') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Remaining Balance</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">₱</span>
                                        <input type="text" id="pao_remaining" value="0.00" disabled class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-xs text-gray-500 bg-gray-50 cursor-not-allowed">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Status</label>
                                    <select name="pao_status" id="pao_status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('pao_status') border-red-300 @enderror">
                                        <option value="">-- Select Status --</option>
                                        <option value="pending" @selected(old('pao_status', $shortRemittance->pao_status) === 'pending')>Pending</option>
                                        <option value="partial" @selected(old('pao_status', $shortRemittance->pao_status) === 'partial')>Partial Payment</option>
                                        <option value="paid" @selected(old('pao_status', $shortRemittance->pao_status) === 'paid')>Fully Paid</option>
                                    </select>
                                    @error('pao_status') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-6">

                        {{-- Notes --}}
                        <div class="mb-6">
                            <label class="block text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-1">Resolution Notes</label>
                            <textarea name="notes" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('notes') border-red-300 @enderror" placeholder="Add notes about short remittance resolution, payment arrangements, or follow-up actions...">{{ old('notes', $shortRemittance->resolution_notes) }}</textarea>
                            @error('notes') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2.5 justify-end pt-5 border-t border-gray-100">
                            <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-none inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Save Resolution
                            </button>
                            <a href="{{ route('short-remittances.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer no-underline inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7-7 7 7 7"/></svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-4 py-3.5 border-b border-gray-50">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Liability Summary</h2>
                </div>
                <div class="p-4 space-y-4">
                    <div class="pb-4 border-b border-gray-100">
                        <span class="text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 block mb-1">Short Amount</span>
                        <p class="text-base font-bold text-red-600">₱{{ number_format($shortRemittance->short_amount, 2) }}</p>
                    </div>
                    <div class="pb-4 border-b border-gray-100">
                        <h4 class="flex items-center gap-1.5 text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-2">
                            <svg class="w-3 h-3 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Driver
                        </h4>
                        <div class="space-y-1 text-xs">
                            <p><span class="text-gray-400">Liability:</span> <strong>₱{{ number_format($shortRemittance->driver_share, 2) }}</strong></p>
                            <p><span class="text-gray-400">Amount Paid:</span> <strong id="driver_paid_display">₱0.00</strong></p>
                            <p><span class="text-gray-400">Remaining:</span> <strong id="driver_remaining_display" class="text-red-600">₱{{ number_format($shortRemittance->driver_share, 2) }}</strong></p>
                        </div>
                    </div>
                    <div>
                        <h4 class="flex items-center gap-1.5 text-[0.6rem] font-bold uppercase tracking-wider text-gray-400 mb-2">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            PAO
                        </h4>
                        <div class="space-y-1 text-xs">
                            <p><span class="text-gray-400">Liability:</span> <strong>₱{{ number_format($shortRemittance->pao_share, 2) }}</strong></p>
                            <p><span class="text-gray-400">Amount Paid:</span> <strong id="pao_paid_display">₱0.00</strong></p>
                            <p><span class="text-gray-400">Remaining:</span> <strong id="pao_remaining_display" class="text-red-600">₱{{ number_format($shortRemittance->pao_share, 2) }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const driverLiability = {{ $shortRemittance->driver_share }};
    const paoLiability = {{ $shortRemittance->pao_share }};

    function updateDriverBalance() {
        const amountPaid = parseFloat(document.getElementById('driver_amount_paid').value) || 0;
        const remaining = Math.max(0, driverLiability - amountPaid);

        document.getElementById('driver_remaining').value = remaining.toFixed(2);
        document.getElementById('driver_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('driver_remaining_display').textContent = '₱' + remaining.toFixed(2);

        if (amountPaid === 0) {
            document.getElementById('driver_status').value = 'pending';
        } else if (amountPaid >= driverLiability) {
            document.getElementById('driver_status').value = 'paid';
        } else {
            document.getElementById('driver_status').value = 'partial';
        }
    }

    function updatePaoBalance() {
        const amountPaid = parseFloat(document.getElementById('pao_amount_paid').value) || 0;
        const remaining = Math.max(0, paoLiability - amountPaid);

        document.getElementById('pao_remaining').value = remaining.toFixed(2);
        document.getElementById('pao_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('pao_remaining_display').textContent = '₱' + remaining.toFixed(2);

        if (amountPaid === 0) {
            document.getElementById('pao_status').value = 'pending';
        } else if (amountPaid >= paoLiability) {
            document.getElementById('pao_status').value = 'paid';
        } else {
            document.getElementById('pao_status').value = 'partial';
        }
    }

    document.getElementById('driver_amount_paid').addEventListener('input', updateDriverBalance);
    document.getElementById('pao_amount_paid').addEventListener('input', updatePaoBalance);

    window.addEventListener('DOMContentLoaded', function() {
        updateDriverBalance();
        updatePaoBalance();
    });
</script>
@endpush
@endsection
