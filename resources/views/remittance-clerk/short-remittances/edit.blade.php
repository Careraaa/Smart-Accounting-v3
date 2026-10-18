@extends('layouts.layout')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Boundary Shortage</h1>
            <p class="mt-1 text-sm text-gray-500">Daily remittance #{{ $shortRemittance->id }} · {{ $shortRemittance->remittance_date?->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('short-remittances.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 no-underline">Back to shortages</a>
    </div>

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
        This existing shortage record is read-only here. Only an accountant can confirm actual boundary money received. Each approved payment is recorded against this remittance, posted once, and retained in the payment history. No receipt is required.
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Fixed boundary</p>
            <p class="mt-2 font-mono text-xl font-bold text-gray-900">₱{{ number_format($shortRemittance->boundary ?? 0, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Remaining shortage</p>
            <p class="mt-2 font-mono text-xl font-bold {{ (float) $shortRemittance->short_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($shortRemittance->short_amount ?? 0, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Driver / PAO</p>
            <p class="mt-2 text-sm font-semibold text-gray-800">{{ $shortRemittance->driver?->name ?? '—' }} / {{ $shortRemittance->pao?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
        <div class="border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-bold text-gray-800">Verified payment history</h2>
        </div>
        @if($shortRemittance->boundaryPayments->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-500">No boundary payments have been recorded for this remittance.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Internal ID</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Paid by</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($shortRemittance->boundaryPayments as $payment)
                            <tr>
                                <td class="px-5 py-3 font-mono">BP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-5 py-3">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-5 py-3">{{ strtoupper($payment->paid_by) }}</td>
                                <td class="px-5 py-3 text-right font-mono">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-5 py-3">{{ $payment->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
