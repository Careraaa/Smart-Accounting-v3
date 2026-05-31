{{-- Government Account Numbers --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Government Account Numbers</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Enter the employee's government-mandated account numbers.</div>

    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="sss_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                SSS Number <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">SSS</span>
                <input type="text" name="sss_number" id="sss_number" value="{{ old('sss_number', $employee->sss_number ?? '') }}" required placeholder="XX-XXXXXXX-X"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('sss_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('sss_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XX-XXXXXXX-X</span>
        </div>
        <div>
            <label for="pagibig_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Pag-IBIG Number <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">HDMF</span>
                <input type="text" name="pagibig_number" id="pagibig_number" value="{{ old('pagibig_number', $employee->pagibig_number ?? '') }}" required placeholder="XXXX-XXXX-XXXX"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('pagibig_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('pagibig_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XXXX-XXXX-XXXX</span>
        </div>
    </div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="philhealth_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                PhilHealth Number <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">PH</span>
                <input type="text" name="philhealth_number" id="philhealth_number" value="{{ old('philhealth_number', $employee->philhealth_number ?? '') }}" required placeholder="XX-XXXXXXX-X"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('philhealth_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('philhealth_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XX-XXXXXXX-X</span>
        </div>
        <div>
            <label for="tin_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                TIN Number <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">TIN</span>
                <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number', $employee->tin_number ?? '') }}" required placeholder="XXX-XXX-XXX-XXX"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('tin_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('tin_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XXX-XXX-XXX-XXX</span>
        </div>
    </div>

    <div class="flex items-start gap-2.5 px-4 py-3.5 rounded-xl text-xs font-medium mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 mt-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Ensure the numbers match the official SSS, Pag-IBIG, PhilHealth, and TIN IDs.</span>
    </div>
</div>
