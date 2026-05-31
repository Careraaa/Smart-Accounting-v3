{{-- Government IDs --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Government IDs</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Government-mandated numbers and other identification.</div>

    {{-- SSS --}}
    <div>
        <label for="sss_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            SSS Number <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex items-center gap-2">
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10 flex-1">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">SSS</span>
                <input type="text" name="sss_number" id="sss_number" value="{{ old('sss_number', $employee->sss_number ?? '') }}" placeholder="XX-XXXXXXX-X" data-gov-field="sss"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('sss_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
        </div>
        @error('sss_number')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XX-XXXXXXX-X</span>
        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer group">
            <input type="checkbox" name="has_sss" id="has_sss" value="1" {{ old('has_sss', $employee->has_sss ?? false) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-red-500 dark:text-red-400 focus:ring-red-500/30 cursor-pointer"
                onchange="document.getElementById('sss_number').disabled = !this.checked; document.getElementById('sss_number').required = this.checked;">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Employee has SSS</span>
        </label>
    </div>

    {{-- Pag-IBIG --}}
    <div>
        <label for="pagibig_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Pag-IBIG Number <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
            <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">HDMF</span>
            <input type="text" name="pagibig_number" id="pagibig_number" value="{{ old('pagibig_number', $employee->pagibig_number ?? '') }}" placeholder="XXXX-XXXX-XXXX" data-gov-field="pagibig"
                class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('pagibig_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
        </div>
        @error('pagibig_number')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XXXX-XXXX-XXXX</span>
        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer group">
            <input type="checkbox" name="has_pagibig" id="has_pagibig" value="1" {{ old('has_pagibig', $employee->has_pagibig ?? false) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-red-500 dark:text-red-400 focus:ring-red-500/30 cursor-pointer"
                onchange="document.getElementById('pagibig_number').disabled = !this.checked; document.getElementById('pagibig_number').required = this.checked;">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Employee has Pag-IBIG</span>
        </label>
    </div>

    {{-- PhilHealth --}}
    <div>
        <label for="philhealth_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            PhilHealth Number <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
            <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">PH</span>
            <input type="text" name="philhealth_number" id="philhealth_number" value="{{ old('philhealth_number', $employee->philhealth_number ?? '') }}" placeholder="XX-XXXXXXX-X" data-gov-field="philhealth"
                class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('philhealth_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
        </div>
        @error('philhealth_number')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XX-XXXXXXX-X</span>
        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer group">
            <input type="checkbox" name="has_philhealth" id="has_philhealth" value="1" {{ old('has_philhealth', $employee->has_philhealth ?? false) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-red-500 dark:text-red-400 focus:ring-red-500/30 cursor-pointer"
                onchange="document.getElementById('philhealth_number').disabled = !this.checked; document.getElementById('philhealth_number').required = this.checked;">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Employee has PhilHealth</span>
        </label>
    </div>

    {{-- TIN --}}
    <div>
        <label for="tin_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            TIN Number <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
            <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">TIN</span>
            <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number', $employee->tin_number ?? '') }}" placeholder="XXX-XXX-XXX-XXX" data-gov-field="tin"
                class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('tin_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
        </div>
        @error('tin_number')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XXX-XXX-XXX-XXX</span>
        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer group">
            <input type="checkbox" name="has_tin" id="has_tin" value="1" {{ old('has_tin', $employee->has_tin ?? false) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-red-500 dark:text-red-400 focus:ring-red-500/30 cursor-pointer"
                onchange="document.getElementById('tin_number').disabled = !this.checked; document.getElementById('tin_number').required = this.checked;">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">Employee has TIN</span>
        </label>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-800 my-6"></div>

    {{-- GSIS --}}
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="driver_license_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Driver's License No. <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="text" name="driver_license_number" id="driver_license_number" value="{{ old('driver_license_number', $employee->driver_license_number ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('driver_license_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('driver_license_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="driver_license_validity" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                License Expiry <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="date" name="driver_license_validity" id="driver_license_validity" value="{{ old('driver_license_validity', $employee->driver_license_validity?->format('Y-m-d') ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('driver_license_validity') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('driver_license_validity')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="flex items-start gap-2.5 px-4 py-3.5 rounded-xl text-xs font-medium mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Ensure the numbers match the official government-issued IDs.</span>
    </div>

</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ['sss', 'pagibig', 'philhealth', 'tin'].forEach(function (key) {
                const cb = document.getElementById('has_' + key);
                const inp = document.getElementById(key + '_number');
                if (cb && inp) {
                    inp.disabled = !cb.checked;
                    inp.required = cb.checked;
                }
            });
        });
    </script>
@endonce
