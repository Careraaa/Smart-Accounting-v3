@extends('layouts.layout')

@push('styles')
    <style>
        /* Reuse your design language but lighter */

        .prl-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding-bottom: 48px;
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

        .prl-eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        .prl-grid {
            display: grid;
            gap: 14px;
        }

        .prl-col-2 {
            grid-template-columns: 1fr 1fr;
        }

        .prl-lbl {
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .prl-ctrl {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 0.85rem;
        }

        .prl-ctrl:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .13);
            outline: none;
        }

        .prl-employee-box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            max-height: 260px;
            overflow-y: auto;
            background: #fafafa;
        }

        .prl-check {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 4px;
            border-radius: 6px;
            transition: background .1s;
        }

        .prl-check:hover {
            background: #f3f4f6;
        }

        .prl-note {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #3730a3;
            font-size: .8rem;
            padding: 12px;
            border-radius: 8px;
        }

        .prl-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .prl-btn-submit {
            padding: 10px 22px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
        }

        .prl-btn-submit:hover {
            background: #15803d;
        }

        .prl-btn-cancel {
            padding: 10px 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
        }

        .prl-summary {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .prl-chip {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .prl-chip-lbl {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .prl-chip-val {
            font-weight: 700;
            font-size: 1.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="prl-wrap">

        <form action="{{ route('payroll.generate-batch') }}" method="POST">
            @csrf

            <div class="prl-card">
                <div class="prl-card-body">

                    {{-- Header --}}
                    <p class="prl-eyebrow">Batch Payroll Generator</p>

                    {{-- Period --}}
                    <div class="prl-grid prl-col-2 mb-3">
                        <div>
                            <label class="prl-lbl">Period Start *</label>
                            <input type="date" name="period_start"
                                class="prl-ctrl @error('period_start') is-invalid @enderror"
                                value="{{ old('period_start') }}" required>
                        </div>

                        <div>
                            <label class="prl-lbl">Period End *</label>
                            <input type="date" name="period_end"
                                class="prl-ctrl @error('period_end') is-invalid @enderror" value="{{ old('period_end') }}"
                                required>
                        </div>
                    </div>

                    {{-- Employees --}}
                    <p class="prl-eyebrow">Employees</p>

                    <small class="text-muted d-block mb-2">
                        Leave unchecked to generate for all active employees
                    </small>

                    <div class="prl-employee-box">
                        @php
                            $activeEmployees = \App\Models\Employee::where('role', '!=', 'superadmin')
                                ->where('status', 'active')
                                ->orderBy('first_name')
                                ->get();
                        @endphp

                        @forelse($activeEmployees as $employee)
                            <label class="prl-check">
                                <input type="checkbox" name="employees[]" value="{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employees', [])) ? 'checked' : '' }}>
                                <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
                            </label>
                        @empty
                            <p class="text-muted">No active employees</p>
                        @endforelse
                    </div>

                    {{-- Summary --}}
                    <div class="prl-summary">
                        <div class="prl-chip">
                            <span class="prl-chip-lbl">Selected</span>
                            <span class="prl-chip-val" id="empCount">0</span>
                        </div>
                        <div class="prl-chip">
                            <span class="prl-chip-lbl">Mode</span>
                            <span class="prl-chip-val" id="modeLabel">All</span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="prl-note mt-3">
                        System will auto-compute salaries from attendance and skip existing payroll records.
                    </div>

                    {{-- Actions --}}
                    <div class="prl-footer">
                        <button type="submit" class="prl-btn-submit">
                            Generate Batch Payroll
                        </button>
                        <a href="{{ route('payroll.salary-computation.index') }}" class="prl-btn-cancel">Cancel</a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        const checkboxes = document.querySelectorAll('input[name="employees[]"]');
        const countEl = document.getElementById('empCount');
        const modeEl = document.getElementById('modeLabel');

        function updateCount() {
            const selected = [...checkboxes].filter(c => c.checked).length;
            countEl.textContent = selected;
            modeEl.textContent = selected === 0 ? 'All' : 'Custom';
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();
    </script>
@endsection
