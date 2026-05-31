{{-- Government IDs --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Government IDs</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Other government-issued identification numbers.</div>

    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="gsis_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                GSIS Number <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">GSIS</span>
                <input type="text" name="gsis_number" id="gsis_number" value="{{ old('gsis_number', $employee->gsis_number ?? '') }}"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('gsis_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('gsis_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
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
    </div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="driver_license_validity" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                License Expiry <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="date" name="driver_license_validity" id="driver_license_validity" value="{{ old('driver_license_validity', $employee->driver_license_validity ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('driver_license_validity') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('driver_license_validity')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="passport_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Passport No. <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $employee->passport_number ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('passport_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('passport_number')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="passport_expiry" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Passport Expiry <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry', $employee->passport_expiry ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('passport_expiry') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('passport_expiry')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="other_id" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Other ID <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="text" name="other_id" id="other_id" value="{{ old('other_id', $employee->other_id ?? '') }}" placeholder="e.g. UMID, PRC, etc."
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('other_id') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('other_id')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
