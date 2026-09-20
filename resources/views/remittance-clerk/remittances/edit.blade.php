@extends('layouts.layout')

@section('content')
<div class="flex items-start justify-between flex-wrap gap-4 mb-6 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Remittance</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update remittance details. Edited approved/rejected entries will return to pending for re-approval.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('remittances.show', $remittance) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </a>
            <a href="{{ route('remittances.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    @if(in_array($remittance->status, ['approved', 'rejected']))
    <div class="flex items-center gap-2 px-4 py-3 mb-5 rounded-xl text-xs font-medium bg-amber-50 border border-amber-200 text-amber-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <strong>Notice:</strong> This remittance is currently {{ ucfirst($remittance->status) }}. Any changes will reset the status back to <strong>Pending</strong> for re-approval.
        <button type="button" class="ml-auto cursor-pointer bg-transparent border-none text-amber-700/50 hover:text-amber-700" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 w-full max-w-10xl">
        <form action="{{ route('remittances.update', $remittance) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Remittance Date <span class="text-amber-600">*</span></label>
                    <input type="date" name="remittance_date" value="{{ old('remittance_date', $remittance->remittance_date?->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('remittance_date') border-red-300 @enderror" required>
                    @error('remittance_date') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Driver <span class="text-amber-600">*</span></label>
                    <select name="driver_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('driver_id') border-red-300 @enderror" required>
                        <option value="">— Select Driver —</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id', $remittance->driver_id) == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                    @error('driver_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">PAO <span class="text-amber-600">*</span></label>
                    <select name="pao_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('pao_id') border-red-300 @enderror" required>
                        <option value="">— Select PAO —</option>
                        @foreach ($paos as $pao)
                            <option value="{{ $pao->id }}" {{ old('pao_id', $remittance->pao_id) == $pao->id ? 'selected' : '' }}>{{ $pao->name }}</option>
                        @endforeach
                    </select>
                    @error('pao_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Vehicle <span class="text-amber-600">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('vehicle_id') border-red-300 @enderror" required>
                        <option value="">— Select Vehicle —</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" data-boundary="{{ $vehicle->route->boundary ?? 0 }}" {{ old('vehicle_id', $remittance->vehicle_id) == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }} ({{ $vehicle->route->origin ?? 'N/A' }} - {{ $vehicle->route->destination ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-6 mb-4">Financials</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Total Collection <span class="text-amber-600">*</span></label>
                    <input type="text" name="total_collection" value="{{ old('total_collection', $remittance->total_collection) }}" class="money-input w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('total_collection') border-red-300 @enderror" inputmode="decimal" autocomplete="off" required>
                    @error('total_collection') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Total Expenses</label>
                    <input type="text" name="total_expenses" id="total_expenses" value="{{ old('total_expenses', $remittance->total_expenses) }}" class="money-input w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 bg-gray-50 focus:outline-none" readonly>
                    <p class="text-[10px] text-gray-400 mt-1">Calculated from the expense details below.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Boundary Rate</label>
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-200 focus-within:border-blue-300 transition-all">
                        <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">₱</span>
                        <input type="text" id="boundary_display" class="w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none bg-transparent" value="{{ number_format($remittance->boundary ?? $remittance->vehicle->route->boundary ?? 0, 2) }}" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Net Remittance <span class="text-amber-600">*</span></label>
                    <input type="text" name="net_remittance" id="net_remittance" value="{{ old('net_remittance', $remittance->net_remittance) }}" class="money-input w-full border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all @error('net_remittance') border-red-300 @enderror" inputmode="decimal" autocomplete="off" required>
                    @error('net_remittance') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-6 mb-4">Expense Details <span class="font-normal normal-case tracking-normal">(optional)</span></div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach ([['diesel', 'Diesel'], ['parking', 'Parking'], ['dispatcher', 'Dispatcher'], ['food_allowance', 'Food Allowance'], ['barker', 'Barker'], ['others', 'Others']] as [$field, $label])
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">{{ $label }}</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-200 focus-within:border-blue-300 transition-all">
                            <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">₱</span>
                            <input type="text" name="{{ $field }}" value="{{ old($field, $remittance->{$field}) }}" class="money-input expense-input w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none" inputmode="decimal" autocomplete="off">
                        </div>
                        @error($field) <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            <div id="short-remittance-section" class="grid grid-cols-1 sm:grid-cols-4 gap-5 mt-5" style="display: none;">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Short Amount</label>
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                        <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">₱</span>
                        <input type="text" id="short_amount_display_calc" class="w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none bg-transparent" placeholder="0.00" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Driver Share (%)</label>
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                        <input type="number" name="driver_share_percent" id="driver_share_percent" min="0" max="100" step="0.01" value="{{ old('driver_share_percent', $shortRemittance->short_amount > 0 ? round(($shortRemittance->driver_share / $shortRemittance->short_amount) * 100, 2) : 50) }}" class="w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none bg-transparent" inputmode="decimal">
                        <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-l border-gray-200">%</span>
                    </div>
                    @error('driver_share_percent') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Driver Share</label>
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                        <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">₱</span>
                        <input type="text" id="driver_share_display_calc" class="w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none bg-transparent" placeholder="0.00" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">PAO Share</label>
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                        <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">₱</span>
                        <input type="text" id="pao_share_display_calc" class="w-full px-3 py-2.5 text-xs text-gray-700 border-none outline-none bg-transparent" placeholder="0.00" readonly>
                    </div>
                </div>
            </div>

            <div class="flex gap-2.5 justify-end pt-5 border-t border-gray-100 mt-6">
                <a href="{{ route('remittances.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 active:scale-[0.97] cursor-pointer no-underline">Cancel</a>
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gray-900 transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-none">Update Remittance</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vehicleSelect = document.getElementById('vehicle_id');
            const boundaryDisplay = document.getElementById('boundary_display');
            const netRemittanceInput = document.getElementById('net_remittance');
            const shortRemittanceSection = document.getElementById('short-remittance-section');
            const driverSharePercentInput = document.getElementById('driver_share_percent');
            const totalExpensesInput = document.getElementById('total_expenses');

            function parseMoney(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            function formatMoney(input) {
                const rawValue = String(input.value).replace(/,/g, '').trim();
                if (rawValue === '') return;
                const amount = Number(rawValue);
                if (Number.isFinite(amount)) {
                    input.value = amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }

            function updateTotalExpenses() {
                const total = [...document.querySelectorAll('.expense-input')]
                    .reduce((sum, input) => sum + parseMoney(input.value), 0);
                totalExpensesInput.value = total.toFixed(2);
                formatMoney(totalExpensesInput);
            }

            function updateShortRemittance() {
                const netRemittance = parseMoney(netRemittanceInput.value);
                const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
                const boundary = parseFloat(selectedOption?.getAttribute('data-boundary')) || 0;

                if (boundary > 0 && netRemittance < boundary) {
                    shortRemittanceSection.style.display = 'grid';
                    
                    const shortAmount = boundary - netRemittance;
                    const requestedPercent = parseFloat(driverSharePercentInput.value);
                    const driverSharePercent = Number.isFinite(requestedPercent) ? Math.min(100, Math.max(0, requestedPercent)) : 50;
                    const driverShare = shortAmount * (driverSharePercent / 100);
                    const paoShare = shortAmount - driverShare;

                    const shortAmountDisplay = document.getElementById('short_amount_display_calc');
                    const driverShareDisplay = document.getElementById('driver_share_display_calc');
                    const paoShareDisplay = document.getElementById('pao_share_display_calc');
                    
                    if (shortAmountDisplay) shortAmountDisplay.value = shortAmount.toFixed(2);
                    if (driverShareDisplay) driverShareDisplay.value = driverShare.toFixed(2);
                    if (paoShareDisplay) paoShareDisplay.value = paoShare.toFixed(2);

                    if (!document.getElementById('is_short_hidden')) {
                        const form = netRemittanceInput.closest('form');
                        const shortInput = document.createElement('input');
                        shortInput.type = 'hidden';
                        shortInput.id = 'is_short_hidden';
                        shortInput.name = 'is_short_remittance';
                        shortInput.value = '1';
                        form.appendChild(shortInput);

                        const shortAmountInput = document.createElement('input');
                        shortAmountInput.type = 'hidden';
                        shortAmountInput.id = 'short_amount_hidden';
                        shortAmountInput.name = 'short_amount';
                        shortAmountInput.value = shortAmount.toFixed(2);
                        form.appendChild(shortAmountInput);

                        const driverShareInput = document.createElement('input');
                        driverShareInput.type = 'hidden';
                        driverShareInput.id = 'driver_share_hidden';
                        driverShareInput.name = 'driver_share';
                        driverShareInput.value = driverShare.toFixed(2);
                        form.appendChild(driverShareInput);

                        const paoShareInput = document.createElement('input');
                        paoShareInput.type = 'hidden';
                        paoShareInput.id = 'pao_share_hidden';
                        paoShareInput.name = 'pao_share';
                        paoShareInput.value = paoShare.toFixed(2);
                        form.appendChild(paoShareInput);
                    } else {
                        document.getElementById('is_short_hidden').value = '1';
                        document.getElementById('short_amount_hidden').value = shortAmount.toFixed(2);
                        document.getElementById('driver_share_hidden').value = driverShare.toFixed(2);
                        document.getElementById('pao_share_hidden').value = paoShare.toFixed(2);
                    }
                } else {
                    shortRemittanceSection.style.display = 'none';
                    
                    const isShortInput = document.getElementById('is_short_hidden');
                    const shortAmountInput = document.getElementById('short_amount_hidden');
                    const driverShareInput = document.getElementById('driver_share_hidden');
                    const paoShareInput = document.getElementById('pao_share_hidden');
                    
                    if (isShortInput) isShortInput.remove();
                    if (shortAmountInput) shortAmountInput.remove();
                    if (driverShareInput) driverShareInput.remove();
                    if (paoShareInput) paoShareInput.remove();
                }
            }

            vehicleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const boundary = selectedOption.getAttribute('data-boundary');
                
                if (boundary && boundary !== '' && boundary !== 'null' && boundary !== '0') {
                    boundaryDisplay.value = parseFloat(boundary).toFixed(2);
                } else {
                    boundaryDisplay.value = '0.00';
                }
            });

            document.querySelectorAll('.expense-input').forEach(input => input.addEventListener('input', updateTotalExpenses));
            document.querySelectorAll('.money-input').forEach(input => {
                input.addEventListener('focus', () => input.value = input.value.replace(/,/g, ''));
                input.addEventListener('blur', () => {
                    formatMoney(input);
                    if (input.classList.contains('expense-input')) updateTotalExpenses();
                });
            });
            netRemittanceInput.closest('form').addEventListener('submit', function() {
                this.querySelectorAll('.money-input').forEach(input => input.value = input.value.replace(/,/g, ''));
            });
            updateTotalExpenses();

            netRemittanceInput.addEventListener('input', updateShortRemittance);
            driverSharePercentInput.addEventListener('input', updateShortRemittance);

            if (vehicleSelect.value) {
                vehicleSelect.dispatchEvent(new Event('change'));
            }
            updateShortRemittance();
        });
    </script>
@endpush
@endsection
