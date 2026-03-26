@extends('layouts.layout')

@push('styles')
    <style>
        /* ── (styles identical to create.blade.php — extract to shared partial if desired) ── */
        .prl-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding-bottom: 48px;
        }

        .prl-eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9ca3af;
            margin: 24px 0 8px;
        }

        .prl-eyebrow:first-child {
            margin-top: 0;
        }

        .prl-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .prl-card-body {
            padding: 20px 22px;
        }

        .prl-grid {
            display: grid;
            gap: 14px;
        }

        .prl-col-1 {
            grid-template-columns: 1fr;
        }

        .prl-col-2 {
            grid-template-columns: 1fr 1fr;
        }

        .prl-lbl {
            display: block;
            font-size: 0.775rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .prl-lbl .req {
            color: #ef4444;
            margin-left: 2px;
        }

        .prl-ctrl {
            display: block;
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.855rem;
            padding: 9px 12px;
            color: #111827;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
            appearance: auto;
        }

        .prl-ctrl:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .13);
            outline: none;
        }

        .prl-ctrl.is-invalid {
            border-color: #ef4444;
        }

        .prl-err {
            font-size: 0.74rem;
            color: #ef4444;
            margin-top: 4px;
        }

        .prl-chips {
            display: flex;
            gap: 10px;
        }

        .prl-chip {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 13px 14px;
            text-align: center;
        }

        .prl-chip-lbl {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #9ca3af;
            margin-bottom: 5px;
        }

        .prl-chip-val {
            font-size: 1.3rem;
            font-weight: 700;
            color: #111827;
            font-variant-numeric: tabular-nums;
        }

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
        }

        .prl-add-row .prl-fi:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .13);
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

        .prl-add-btn-green,
        .prl-add-btn-red {
            flex-shrink: 0;
            padding: 0 18px;
            border: none;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            white-space: nowrap;
            color: #fff;
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
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
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
        }

        .prl-sub.p-green {
            color: #16a34a;
        }

        .prl-sub.p-red {
            color: #dc2626;
        }

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
        }

        .prl-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .prl-btn-submit {
            padding: 10px 24px;
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .845rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .prl-btn-submit:hover {
            background: #4f46e5;
        }

        .prl-btn-cancel {
            padding: 10px 20px;
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: .845rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background .15s;
        }

        .prl-btn-cancel:hover {
            background: #f9fafb;
            color: #374151;
        }

        @media (max-width: 600px) {
            .prl-col-2 {
                grid-template-columns: 1fr;
            }

            .prl-chips {
                flex-wrap: wrap;
            }

            .prl-add-row {
                flex-wrap: wrap;
            }

            .prl-add-row .prl-fi-amount {
                width: 100%;
            }

            .prl-add-btn-green,
            .prl-add-btn-red {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="prl-wrap">

        <form action="{{ route('payroll.salary-computation.update', $payroll) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="prl-card">
                <div class="prl-card-body">

                    {{-- ① Employee & Period ──────────────────────────────────────── --}}
                    <p class="prl-eyebrow" style="margin-top:0;">Employee & Period</p>

                    <div class="prl-grid prl-col-1 mb-3">
                        <div>
                            <label class="prl-lbl" for="prl_user_id">Employee <span class="req">*</span></label>
                            <select name="user_id" id="prl_user_id" class="prl-ctrl @error('user_id') is-invalid @enderror"
                                required>
                                <option value="">— Select an employee —</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" data-salary-rate="{{ $emp->salary_rate }}"
                                        data-has-sss="{{ $emp->has_sss ? 1 : 0 }}"
                                        data-has-pagibig="{{ $emp->has_pagibig ? 1 : 0 }}"
                                        {{ $payroll->user_id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->first_name }} {{ $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="prl-err">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="prl-grid prl-col-2 mb-4">
                        <div>
                            <label class="prl-lbl" for="prl_period_start">Period Start <span class="req">*</span></label>
                            <input type="date" name="payroll_period_start" id="prl_period_start"
                                class="prl-ctrl @error('payroll_period_start') is-invalid @enderror"
                                value="{{ old('payroll_period_start', $payroll->payroll_period_start->format('Y-m-d')) }}"
                                required>
                            @error('payroll_period_start')
                                <p class="prl-err">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="prl-lbl" for="prl_period_end">Period End <span class="req">*</span></label>
                            <input type="date" name="payroll_period_end" id="prl_period_end"
                                class="prl-ctrl @error('payroll_period_end') is-invalid @enderror"
                                value="{{ old('payroll_period_end', $payroll->payroll_period_end->format('Y-m-d')) }}"
                                required>
                            @error('payroll_period_end')
                                <p class="prl-err">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ② Attendance Summary ─────────────────────────────────────── --}}
                    <p class="prl-eyebrow">Attendance Summary</p>
                    <div class="prl-chips mb-4">
                        <div class="prl-chip">
                            <span class="prl-chip-lbl">Days Worked</span>
                            <span class="prl-chip-val" id="prl_days_worked">{{ $payroll->days_worked ?? 0 }}</span>
                        </div>
                        <div class="prl-chip">
                            <span class="prl-chip-lbl">Hours Worked</span>
                            <span class="prl-chip-val"
                                id="prl_hours_worked">{{ number_format($payroll->hours_worked ?? 0, 2) }}</span>
                        </div>
                        {{-- ✅ "Worked Days" — distinct from absent/late counts --}}
                        <div class="prl-chip">
                            <span class="prl-chip-lbl">Worked Days</span>
                            <span class="prl-chip-val" id="prl_worked_days">{{ $payroll->days_worked ?? 0 }}</span>
                        </div>
                    </div>

                    {{-- ③ Salary Computation ──────────────────────────────────────── --}}
                    <p class="prl-eyebrow">Salary Computation</p>
                    <div class="prl-breakdown mb-4">

                        <div class="prl-brow">
                            <span class="prl-brow-lbl">Basic Salary <span class="prl-badge">rate × days</span></span>
                            <span class="prl-brow-val"
                                id="prl_basic_display">₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</span>
                        </div>
                        {{--
                            ✅ basic_salary is shown for reference; PayrollController
                               recomputes it from AttendanceService on every update.
                        --}}
                        <input type="hidden" name="basic_salary" id="prl_basic_input"
                            value="{{ old('basic_salary', $payroll->basic_salary ?? 0) }}">

                        <div class="prl-brow" id="prl_ot_row"
                            style="{{ $payroll->allowances->where('allowance_type', 'Overtime Pay')->sum('amount') > 0 ? '' : 'display:none;' }}">
                            <span class="prl-brow-lbl c-green">
                                + Overtime Pay
                                <span class="prl-badge" id="prl_ot_hrs">
                                    {{ $payroll->hours_worked }} hrs
                                </span>
                            </span>
                            <span class="prl-brow-val c-green" id="prl_ot_pay">
                                ₱{{ number_format($payroll->allowances->where('allowance_type', 'Overtime Pay')->sum('amount'), 2) }}
                            </span>
                        </div>

                        <div class="prl-brow" id="prl_ut_row"
                            style="{{ $payroll->deductions->where('deduction_type', 'Undertime Deduction')->sum('amount') > 0 ? '' : 'display:none;' }}">
                            <span class="prl-brow-lbl c-red">
                                − Undertime Deduction
                                <span class="prl-badge" id="prl_ut_hrs">
                                    {{ $payroll->hours_worked }} hrs
                                </span>
                            </span>
                            <span class="prl-brow-val c-red" id="prl_ut_deduct">
                                ₱{{ number_format($payroll->deductions->where('deduction_type', 'Undertime Deduction')->sum('amount'), 2) }}
                            </span>
                        </div>


                        <div class="prl-brow" id="prl_sss_row" style="display:none;">
                            <span class="prl-brow-lbl c-red">
                                − SSS Contribution <span class="prl-badge">statutory · ½ of monthly</span>
                            </span>
                            <span class="prl-brow-val c-red" id="prl_sss_val">₱0.00</span>
                        </div>

                        <div class="prl-brow" id="prl_pagibig_row" style="display:none;">
                            <span class="prl-brow-lbl c-red">
                                − Pag-IBIG Contribution <span class="prl-badge">statutory · ½ of monthly</span>
                            </span>
                            <span class="prl-brow-val c-red" id="prl_pagibig_val">₱0.00</span>
                        </div>

                        <div class="prl-brow" id="prl_loading_row" style="display:none;">
                            <span class="prl-loading-hint">Computing from attendance records…</span>
                            <span></span>
                        </div>

                        <div class="prl-brow">
                            <span class="prl-brow-lbl c-bold">Adjusted Gross</span>
                            <span class="prl-brow-val" id="prl_adjusted" style="font-size:1rem;">₱0.00</span>
                        </div>

                    </div>

                    {{-- ④ Allowances ──────────────────────────────────────────────── --}}
                    <p class="prl-eyebrow">
                        Allowances
                        <span
                            style="font-weight:400;text-transform:none;letter-spacing:0;color:#d1d5db;margin-left:6px;">optional
                            extras on top</span>
                    </p>

                    <div class="prl-add-row">
                        <input type="text" id="prl_allow_name" class="prl-fi prl-fi-name"
                            placeholder="Label — e.g. Transportation">
                        <input type="number" id="prl_allow_amount" class="prl-fi prl-fi-amount" placeholder="0.00"
                            min="0.01" step="0.01">
                        <button type="button" class="prl-add-btn-green" id="prl_allow_btn">+ Add</button>
                    </div>
                    <div class="prl-pill-area" id="prl_allow_pills">
                        <span class="prl-pill-empty" id="prl_allow_empty">No allowances added yet.</span>
                    </div>
                    <div class="prl-sub p-green" id="prl_allow_subtotal" style="display:none;">
                        Total allowances: ₱<span id="prl_allow_total">0.00</span>
                    </div>
                    <div id="prl_allow_hidden"></div>

                    {{-- ⑤ Deductions ─────────────────────────────────────────────── --}}
                    <p class="prl-eyebrow" style="margin-top:24px;">
                        Additional Deductions
                        <span
                            style="font-weight:400;text-transform:none;letter-spacing:0;color:#d1d5db;margin-left:6px;">cash
                            advances, loans, etc.</span>
                    </p>

                    <div class="prl-add-row">
                        <input type="text" id="prl_deduct_name" class="prl-fi prl-fi-name"
                            placeholder="Label — e.g. Cash Advance">
                        <input type="number" id="prl_deduct_amount" class="prl-fi prl-fi-amount" placeholder="0.00"
                            min="0.01" step="0.01">
                        <button type="button" class="prl-add-btn-red" id="prl_deduct_btn">+ Add</button>
                    </div>
                    <div class="prl-pill-area" id="prl_deduct_pills">
                        <span class="prl-pill-empty" id="prl_deduct_empty">Nothing deducted yet — lucky them.</span>
                    </div>
                    <div class="prl-sub p-red" id="prl_deduct_subtotal" style="display:none;">
                        Total deductions: ₱<span id="prl_deduct_total">0.00</span>
                    </div>
                    <div id="prl_deduct_hidden"></div>

                    {{-- ⑥ Net Salary ──────────────────────────────────────────────── --}}
                    <div class="prl-net">
                        <span class="prl-net-lbl">Net Salary</span>
                        <span class="prl-net-val"
                            id="prl_net_salary">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</span>
                    </div>

                    {{-- Actions ──────────────────────────────────────────────────── --}}
                    <div class="prl-footer">
                        <button type="submit" class="prl-btn-submit">Update Payroll</button>
                        <a href="{{ route('payroll.salary-computation.index') }}" class="prl-btn-cancel">Cancel</a>
                    </div>

                </div>{{-- /prl-card-body --}}
            </div>{{-- /prl-card --}}

        </form>
    </div>
@endsection

@push('head_scripts')
    <meta name="page-id" content="payroll-edit">

    @push('head_scripts')
        <meta name="page-id" content="payroll-edit">

        <script>
            window._prl = {
                previewUrl: "{{ route('payroll.preview') }}",

                initAllowances: @json(
                    $payroll->allowances->reject(fn($a) => $a->allowance_type === 'Overtime Pay')->map(
                            fn($a) => [
                                'name' => $a->allowance_type,
                                'amount' => (float) $a->amount,
                            ])->values()),
                initDeductions: @json(
                    $payroll->deductions->reject(fn($d) => $d->deduction_type === 'Undertime Deduction')->map(
                            fn($d) => [
                                'name' => $d->deduction_type,
                                'amount' => (float) $d->amount,
                            ])->values()),
            };
        </script>
    @endpush

@endpush
