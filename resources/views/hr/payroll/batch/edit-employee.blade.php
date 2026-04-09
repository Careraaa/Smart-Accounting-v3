@extends('layouts.layout')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

        .prl-page {
            font-family: 'Sora', sans-serif;
        }

        .prl-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding-bottom: 56px;
        }

        .prl-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #9ca3af;
            text-decoration: none;
            margin-bottom: 16px;
            transition: color 0.13s;
        }

        .prl-back-link:hover {
            color: #c8292a;
        }

        .prl-page-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.02em;
            margin: 0 0 4px;
        }

        .prl-page-sub {
            font-size: 0.78rem;
            color: #9ca3af;
            margin: 0 0 24px;
        }

        /* ── Employee context bar ──────────────────────────────────── */
        .prl-emp-context {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .prl-ctx-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
            color: #6b7280;
            flex-shrink: 0;
            border: 2px solid #e5e7eb;
            text-transform: uppercase;
        }

        .prl-ctx-name {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .prl-ctx-meta {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .prl-ctx-period {
            margin-left: auto;
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
        }

        /* ── Cards ─────────────────────────────────────────────────── */
        .prl-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .prl-card-head {
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .prl-card-head-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .prl-card-head-icon.amber {
            background: #fffbeb;
            color: #d97706;
        }

        .prl-card-head-icon.green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .prl-card-head-icon.red {
            background: #fff0f0;
            color: #c8292a;
        }

        .prl-card-head-title {
            font-size: 0.845rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .prl-card-head-sub {
            font-size: 0.72rem;
            color: #9ca3af;
            margin: 0;
        }

        .prl-card-body {
            padding: 18px 20px;
        }

        /* ── Stat chips ─────────────────────────────────────────────── */
        .prl-chips {
            display: flex;
            gap: 10px;
        }

        @media (max-width:600px) {
            .prl-chips {
                flex-wrap: wrap;
            }
        }

        .prl-chip {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            text-align: center;
        }

        .prl-chip-lbl {
            display: block;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .prl-chip-val {
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
            font-variant-numeric: tabular-nums;
            font-family: 'DM Mono', monospace;
        }

        /* ── Breakdown rows ─────────────────────────────────────────── */
        .prl-breakdown {
            display: flex;
            flex-direction: column;
        }

        .prl-brow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            gap: 10px;
        }

        .prl-brow:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .prl-brow-lbl {
            font-size: 0.845rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 7px;
            flex: 1;
        }

        .prl-brow-lbl.c-green {
            color: #16a34a;
        }

        .prl-brow-lbl.c-red {
            color: #dc2626;
        }

        .prl-brow-lbl.c-bold {
            color: #111827;
            font-weight: 700;
            font-size: .9rem;
        }

        .prl-badge {
            font-size: 0.67rem;
            font-weight: 600;
            background: #f3f4f6;
            color: #6b7280;
            border-radius: 4px;
            padding: 2px 6px;
            white-space: nowrap;
        }

        .prl-brow-val {
            font-size: .9rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            font-family: 'DM Mono', monospace;
            color: #111827;
            white-space: nowrap;
        }

        .prl-brow-val.c-green {
            color: #16a34a;
        }

        .prl-brow-val.c-red {
            color: #dc2626;
        }

        .prl-loading-hint {
            font-size: .75rem;
            color: #9ca3af;
            font-style: italic;
        }

        /* ── Add rows ───────────────────────────────────────────────── */
        .prl-add-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
            margin-bottom: 12px;
        }

        .prl-add-row .prl-fi {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: .845rem;
            color: #111827;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            font-family: 'Sora', sans-serif;
        }

        .prl-add-row .prl-fi:focus {
            border-color: #c8292a;
            box-shadow: 0 0 0 3px rgba(200, 41, 42, .1);
            outline: none;
        }

        .prl-add-row .prl-fi-name {
            flex: 1 1 0;
            min-width: 0;
        }

        .prl-add-row .prl-fi-amount {
            width: 120px;
            flex-shrink: 0;
        }

        @media (max-width:600px) {
            .prl-add-row {
                flex-wrap: wrap;
            }

            .prl-add-row .prl-fi-amount {
                width: 100%;
            }
        }

        .prl-add-btn-green,
        .prl-add-btn-red {
            flex-shrink: 0;
            padding: 0 16px;
            border: none;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            white-space: nowrap;
            color: #fff;
            font-family: 'Sora', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .prl-add-btn-green {
            background: #16a34a;
        }

        .prl-add-btn-green:hover {
            background: #15803d;
        }

        .prl-add-btn-red {
            background: #dc2626;
        }

        .prl-add-btn-red:hover {
            background: #b91c1c;
        }

        /* ── Pills ──────────────────────────────────────────────────── */
        .prl-pill-area {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            min-height: 34px;
            padding: 2px 0 8px;
        }

        .prl-pill-empty {
            font-size: .78rem;
            color: #d1d5db;
            font-style: italic;
            align-self: center;
        }

        .prl-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 8px 5px 11px;
            border-radius: 999px;
            font-size: .775rem;
            font-weight: 600;
            animation: pillIn .15s ease;
        }

        @keyframes pillIn {
            from {
                transform: scale(.75);
                opacity: 0
            }

            to {
                transform: scale(1);
                opacity: 1
            }
        }

        .prl-pill.p-green {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .prl-pill.p-red {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .prl-pill-text {
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .prl-pill-amt {
            font-variant-numeric: tabular-nums;
            font-family: 'DM Mono', monospace;
            opacity: .85;
        }

        .prl-pill-rm {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            flex-shrink: 0;
            opacity: .55;
            transition: opacity .12s;
        }

        .prl-pill-rm:hover {
            opacity: 1;
        }

        .prl-pill.p-green .prl-pill-rm {
            background: #bbf7d0;
            color: #15803d;
        }

        .prl-pill.p-red .prl-pill-rm {
            background: #fecaca;
            color: #b91c1c;
        }

        .prl-sub {
            font-size: .795rem;
            font-weight: 600;
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            padding-top: 2px;
            font-variant-numeric: tabular-nums;
            font-family: 'DM Mono', monospace;
        }

        .prl-sub.p-green {
            color: #16a34a;
        }

        .prl-sub.p-red {
            color: #dc2626;
        }

        /* ── Net box ─────────────────────────────────────────────────── */
        .prl-net {
            background: #111827;
            border-radius: 12px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
        }

        .prl-net-lbl {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: #6b7280;
        }

        .prl-net-val {
            font-size: 1.55rem;
            font-weight: 800;
            color: #fff;
            font-variant-numeric: tabular-nums;
            letter-spacing: -.02em;
            font-family: 'DM Mono', monospace;
        }

        /* ── Footer ──────────────────────────────────────────────────── */
        .prl-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .prl-btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: #c8292a;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: .855rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(200, 41, 42, .25);
        }

        .prl-btn-save:hover {
            background: #a81f20;
            box-shadow: 0 6px 20px rgba(200, 41, 42, .35);
            color: #fff;
        }

        .prl-btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: .845rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .13s, border-color .13s;
        }

        .prl-btn-cancel:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }
    </style>
@endpush

@section('content')
    <div class="prl-page">
        <div class="prl-wrap">

            <a href="{{ route('payroll.batch.confirm', $batch) }}" class="prl-back-link">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Batch Review
            </a>

            <h1 class="prl-page-title">Edit Employee Payroll</h1>
            <p class="prl-page-sub">Adjust allowances and deductions for this employee. All base figures are computed from
                attendance.</p>

            {{-- Employee context bar --}}
            @php
                $initials = strtoupper(
                    substr($payroll->user->first_name ?? 'U', 0, 1) . substr($payroll->user->last_name ?? '', 0, 1),
                );
            @endphp
            <div class="prl-emp-context">
                <div class="prl-ctx-avatar">{{ $initials }}</div>
                <div>
                    <div class="prl-ctx-name">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</div>
                    <div class="prl-ctx-meta">{{ $payroll->user->position ?? ($payroll->user->department ?? 'N/A') }}</div>
                </div>
                <div class="prl-ctx-period">
                    {{ $batch->period_start->format('M d') }} – {{ $batch->period_end->format('M d, Y') }}
                </div>
            </div>

            <form id="prl-batch-edit-form" action="{{ route('payroll.batch.update-employee', [$batch, $payroll]) }}"
                method="POST">
                @csrf
                @method('PUT')

                {{-- Hidden fields for user/period (required by JS) --}}
                <input type="hidden" id="prl_user_id" value="{{ $payroll->user_id }}">
                <input type="hidden" id="prl_period_start" value="{{ $batch->period_start->format('Y-m-d') }}">
                <input type="hidden" id="prl_period_end" value="{{ $batch->period_end->format('Y-m-d') }}">

                @php
                    $systemDeductionItems = $payroll->deductions
                        ->filter(function ($d) {
                            $type = (string) ($d->deduction_type ?? $d->name ?? '');
                            return in_array($type, ['SSS', 'Pag-IBIG', 'PhilHealth', 'Cash Advance', 'Salary Loan'])
                                || str_starts_with($type, 'Undertime Deduction');
                        })
                        ->values();
                @endphp

                {{-- Attendance Summary (read-only) --}}
                <div class="prl-card">
                    <div class="prl-card-head">
                        <div class="prl-card-head-icon amber">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                        </div>
                        <div>
                            <p class="prl-card-head-title">Attendance Summary</p>
                            <p class="prl-card-head-sub">Live-computed from attendance records</p>
                        </div>
                    </div>
                    <div class="prl-card-body">
                        <div class="prl-chips">
                            <div class="prl-chip"><span class="prl-chip-lbl">Days Worked</span><span class="prl-chip-val"
                                    id="prl_days_worked">—</span></div>
                            <div class="prl-chip"><span class="prl-chip-lbl">Hours Worked</span><span class="prl-chip-val"
                                    id="prl_hours_worked">—</span></div>
                            <div class="prl-chip"><span class="prl-chip-lbl">Worked Days</span><span class="prl-chip-val"
                                    id="prl_worked_days">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Salary Computation --}}
                <div class="prl-card">
                    <div class="prl-card-head">
                        <div class="prl-card-head-icon green">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="prl-card-head-title">Salary Computation</p>
                            <p class="prl-card-head-sub">Server-computed — read-only base figures</p>
                        </div>
                    </div>
                    <div class="prl-card-body">
                        <div class="prl-breakdown">
                            <div class="prl-brow">
                                <span class="prl-brow-lbl">Basic Salary <span class="prl-badge">rate × days</span></span>
                                <span class="prl-brow-val"
                                    id="prl_basic_display">₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</span>
                            </div>
                            <input type="hidden" name="basic_salary" id="prl_basic_input"
                                value="{{ $payroll->basic_salary ?? 0 }}">

                            <!-- OT, UT, SSS, Pag-IBIG rows (same as before) -->
                            <!-- ... keep your existing OT/UT/SSS/Pag-IBIG rows ... -->

                            <div class="prl-brow">
                                <span class="prl-brow-lbl c-bold">Adjusted Gross</span>
                                <span class="prl-brow-val" id="prl_adjusted"
                                    style="font-size:1rem;">₱{{ number_format($payroll->gross_pay ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Deductions (read-only) --}}
                <div class="prl-card">
                    <div class="prl-card-head">
                        <div class="prl-card-head-icon red">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="prl-card-head-title">System Deductions</p>
                            <p class="prl-card-head-sub">Auto-generated deductions for this payroll (read-only)</p>
                        </div>
                    </div>
                    <div class="prl-card-body">
                        <div class="prl-breakdown">
                            @forelse($systemDeductionItems as $item)
                                <div class="prl-brow">
                                    <span class="prl-brow-lbl c-red">{{ $item->deduction_type ?? $item->name }}</span>
                                    <span class="prl-brow-val c-red">₱{{ number_format((float) $item->amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="prl-loading-hint">No system deductions for this period.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Allowances --}}
                <div class="prl-card">
                    <div class="prl-card-head">
                        <div class="prl-card-head-icon green">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="prl-card-head-title">Allowances <span
                                    style="font-weight:400;color:#9ca3af;font-size:0.75rem;margin-left:6px;">optional</span>
                            </p>
                            <p class="prl-card-head-sub">Transportation, meal, housing, and other extras</p>
                        </div>
                    </div>
                    <div class="prl-card-body">
                        <div class="prl-add-row">
                            <input type="text" id="prl_allow_name" class="prl-fi prl-fi-name"
                                placeholder="Label — e.g. Transportation">
                            <input type="number" id="prl_allow_amount" class="prl-fi prl-fi-amount" placeholder="0.00"
                                min="0.01" step="0.01">
                            <button type="button" class="prl-add-btn-green" id="prl_allow_btn">Add</button>
                        </div>
                        <div class="prl-pill-area" id="prl_allow_pills"><span class="prl-pill-empty"
                                id="prl_allow_empty">No allowances added yet.</span></div>
                        <div class="prl-sub p-green" id="prl_allow_subtotal" style="display:none;">Total allowances:
                            ₱<span id="prl_allow_total">0.00</span></div>
                        <div id="prl_allow_hidden"></div>
                    </div>
                </div>

                {{-- Deductions --}}
                <div class="prl-card">
                    <div class="prl-card-head">
                        <div class="prl-card-head-icon red">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="prl-card-head-title">Additional Deductions <span
                                    style="font-weight:400;color:#9ca3af;font-size:0.75rem;margin-left:6px;">optional</span>
                            </p>
                            <p class="prl-card-head-sub">Cash advances, loans, and custom deductions</p>
                        </div>
                    </div>
                    <div class="prl-card-body">
                        <div class="prl-add-row">
                            <input type="text" id="prl_deduct_name" class="prl-fi prl-fi-name"
                                placeholder="Label — e.g. Cash Advance">
                            <input type="number" id="prl_deduct_amount" class="prl-fi prl-fi-amount" placeholder="0.00"
                                min="0.01" step="0.01">
                            <button type="button" class="prl-add-btn-red" id="prl_deduct_btn">Add</button>
                        </div>
                        <div class="prl-pill-area" id="prl_deduct_pills"><span class="prl-pill-empty"
                                id="prl_deduct_empty">No deductions added yet.</span></div>
                        <div class="prl-sub p-red" id="prl_deduct_subtotal" style="display:none;">Total deductions:
                            ₱<span id="prl_deduct_total">0.00</span></div>
                        <div id="prl_deduct_hidden"></div>
                    </div>
                </div>

                {{-- Net Salary --}}
                <div class="prl-net">
                    <span class="prl-net-lbl">Net Salary</span>
                    <span class="prl-net-val" id="prl_net_salary">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</span>
                </div>

                <div class="prl-footer">
                    <button type="submit" class="prl-btn-save">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('payroll.batch.confirm', $batch) }}" class="prl-btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('head_scripts')
    <meta name="page-id" content="payroll-batch-edit">
    @php
        $manualAllowances = $payroll->allowances
            ->filter(fn($a) => !str_starts_with((string) ($a->allowance_type ?? $a->name ?? ''), 'Overtime Pay'))
            ->map(fn($a) => ['name' => $a->allowance_type ?? $a->name, 'amount' => $a->amount])
            ->values();

        $manualDeductions = $payroll->deductions
            ->filter(function ($d) {
                $type = (string) ($d->deduction_type ?? $d->name ?? '');
                return !in_array($type, ['SSS', 'Pag-IBIG', 'PhilHealth'])
                    && !str_starts_with($type, 'Undertime Deduction');
            })
            ->map(fn($d) => ['name' => $d->deduction_type ?? $d->name, 'amount' => $d->amount])
            ->values();
    @endphp

    <script>
        window._prl = {
            previewUrl: "{{ route('payroll.preview') }}",
            initAllowances: @json($manualAllowances),
            initDeductions: @json($manualDeductions),
            initComputed: {
                daysWorked: {{ (float) ($payroll->days_worked ?? 0) }},
                hoursWorked: {{ (float) ($payroll->hours_worked ?? 0) }},
                basicSalary: {{ (float) ($payroll->basic_salary ?? 0) }},
                adjustedGross: {{ (float) ($payroll->gross_pay ?? 0) }},
                netPay: {{ (float) ($payroll->net_pay ?? 0) }},
            },
            prefillUserId: {{ $payroll->user_id }},
            prefillStart: "{{ $batch->period_start->format('Y-m-d') }}",
            prefillEnd: "{{ $batch->period_end->format('Y-m-d') }}",
        };
    </script>
@endpush

@push('scripts')
    {{-- Load adapter first, then the main payroll-form.js --}}
    <script src="{{ asset('js/payroll-batch-edit-adapter.js') }}"></script>
    <script src="{{ asset('js/payroll-form.js') }}"></script>
@endpush
