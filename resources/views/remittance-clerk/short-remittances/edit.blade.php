@extends('layouts.layout')

@section('content')
@php
    $driverShareRemaining = max(round((float) $shortRemittance->driver_share - (float) $shortRemittance->driver_amount_paid, 2), 0);
    $paoShareRemaining = max(round((float) $shortRemittance->pao_share - (float) $shortRemittance->pao_amount_paid, 2), 0);
    $pendingPayments = $shortRemittance->boundaryPayments->where('status', 'Pending');
    $driverShareAvailable = max(round($driverShareRemaining - (float) $pendingPayments->where('paid_by', 'driver')->sum('amount'), 2), 0);
    $paoShareAvailable = max(round($paoShareRemaining - (float) $pendingPayments->where('paid_by', 'pao')->sum('amount'), 2), 0);
    $boundaryAvailable = max(round((float) $shortRemittance->short_amount - (float) $pendingPayments->sum('amount'), 2), 0);
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
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
        Record money received against the driver or PAO shortage share here. Each submission stays pending until an accountant approves it. Balances and accounting entries update only after approval.
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

    @if($shortRemittance->status === 'approved' && $boundaryAvailable > 0 && ($driverShareAvailable > 0 || $paoShareAvailable > 0))
        <form method="POST" action="{{ route('short-remittances.payments.store', $shortRemittance) }}" class="rounded-xl border border-gray-100 bg-white p-5 space-y-4">
            @csrf
            <input type="hidden" name="source_payment_key" value="{{ $paymentSubmissionKey }}">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Record received payment</h2>
                <p class="mt-1 text-xs text-gray-500">Available to submit: ₱{{ number_format($boundaryAvailable, 2) }}. Accountant approval is required before this payment is posted.</p>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <label class="text-xs font-medium text-gray-600">
                    Payment date
                    <input type="date" name="payment_date" max="{{ now()->toDateString() }}" value="{{ old('payment_date', now()->toDateString()) }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </label>
                <label class="text-xs font-medium text-gray-600">
                    Amount
                    <input type="number" name="amount" min="0.01" max="{{ number_format($boundaryAvailable, 2, '.', '') }}" step="0.01" value="{{ old('amount') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </label>
                <label class="text-xs font-medium text-gray-600">
                    Shortage share
                    <select name="paid_by" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @if($driverShareAvailable > 0)
                            <option value="driver">{{ $shortRemittance->driver?->name ?? 'Driver' }} (₱{{ number_format($driverShareAvailable, 2) }} available)</option>
                        @endif
                        @if($paoShareAvailable > 0)
                            <option value="pao">{{ $shortRemittance->pao?->name ?? 'PAO' }} (₱{{ number_format($paoShareAvailable, 2) }} available)</option>
                        @endif
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Submit for approval</button>
                </div>
            </div>
            @if($errors->any())
                <p class="text-sm text-red-600">{{ $errors->first() }}</p>
            @endif
        </form>
    @elseif($shortRemittance->status !== 'approved')
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">This remittance must be approved before a shortage payment can be recorded.</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
        <div class="border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-bold text-gray-800">Payment history</h2>
        </div>
        @if($shortRemittance->boundaryPayments->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-500">No boundary payments have been submitted for this remittance.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Internal ID</th>
                            <th class="px-5 py-3">Date / shortage share</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Recorded by</th>
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
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $payment->status === 'Posted' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'Rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $payment->status }}</span>
                                </td>
                                <td class="px-5 py-3">{{ $payment->recordedBy?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
