@extends('layouts.layout')

@section('content')
@php
    $driverShareRemaining = max(round((float) $shortRemittance->driver_share - (float) $shortRemittance->driver_amount_paid, 2), 0);
    $paoShareRemaining = max(round((float) $shortRemittance->pao_share - (float) $shortRemittance->pao_amount_paid, 2), 0);
@endphp
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
        The boundary is recorded jointly for the driver and PAO. Any shortage is tracked separately against each partner's existing share. Only an accountant can confirm actual money received; each approved payment is posted once and retained in the payment history. No receipt is required.
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
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Partners</p>
            <p class="mt-2 text-sm font-semibold text-gray-800">{{ $shortRemittance->driver?->name ?? '—' }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-800">{{ $shortRemittance->pao?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-sky-100 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Driver shortage share</p>
            <p class="mt-2 text-xs text-gray-500">Assessed share</p>
            <p class="font-mono text-lg font-bold text-gray-900">₱{{ number_format($shortRemittance->driver_share ?? 0, 2) }}</p>
            <div class="mt-3 flex justify-between text-xs text-gray-500">
                <span>Paid</span>
                <strong class="font-mono text-gray-800">₱{{ number_format($shortRemittance->driver_amount_paid ?? 0, 2) }}</strong>
            </div>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
                <span>Share remaining</span>
                <strong class="font-mono text-red-600">₱{{ number_format($driverShareRemaining, 2) }}</strong>
            </div>
            <p class="mt-2 text-xs text-gray-500">Status: {{ ucfirst($shortRemittance->driver_status ?? 'pending') }}</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">PAO shortage share</p>
            <p class="mt-2 text-xs text-gray-500">Assessed share</p>
            <p class="font-mono text-lg font-bold text-gray-900">₱{{ number_format($shortRemittance->pao_share ?? 0, 2) }}</p>
            <div class="mt-3 flex justify-between text-xs text-gray-500">
                <span>Paid</span>
                <strong class="font-mono text-gray-800">₱{{ number_format($shortRemittance->pao_amount_paid ?? 0, 2) }}</strong>
            </div>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
                <span>Share remaining</span>
                <strong class="font-mono text-red-600">₱{{ number_format($paoShareRemaining, 2) }}</strong>
            </div>
            <p class="mt-2 text-xs text-gray-500">Status: {{ ucfirst($shortRemittance->pao_status ?? 'pending') }}</p>
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
                            <th class="px-5 py-3">Date / shortage share</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($shortRemittance->boundaryPayments as $payment)
                            <tr>
                                <td class="px-5 py-3 font-mono">BP-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-5 py-3">
                                    {{ $payment->payment_date->format('M d, Y') }}
                                    <span class="block text-xs text-gray-400">{{ $payment->paid_by === 'pao' ? 'PAO share' : ($payment->paid_by === 'driver' ? 'Driver share' : 'Joint boundary') }}</span>
                                </td>
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
