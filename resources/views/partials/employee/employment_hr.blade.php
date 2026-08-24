{{-- HR Fields --}}
<div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Compensation &amp; Banking</div>

<div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
    <div>
        <label for="salary_rate" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Daily Rate <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex items-stretch w-full border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
            <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">₱</span>
            <input type="number" step="0.01" name="salary_rate" id="salary_rate" value="{{ old('salary_rate', $employee->salary_rate ?? '') }}" required
                class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('salary_rate') ? 'border-red-500 dark:border-red-400!' : '' }}">
        </div>
        @error('salary_rate')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="work_days_per_week" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Work Days Per Week <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="relative w-full">
            <select name="work_days_per_week" id="work_days_per_week" required
                class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('work_days_per_week') ? 'border-red-500 dark:border-red-400!' : '' }}">
                <option value="5" {{ old('work_days_per_week', $employee->work_days_per_week ?? 5) == 5 ? 'selected' : '' }}>5 Days (Mon–Fri)</option>
                <option value="6" {{ old('work_days_per_week', $employee->work_days_per_week ?? 5) == 6 ? 'selected' : '' }}>6 Days (Mon–Sat)</option>
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('work_days_per_week')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 mt-4">
    <div>
        <label for="bank_name" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Bank Name <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
        </label>
        <div class="relative">
            <select name="bank_name" id="bank_name"
                class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('bank_name') ? 'border-red-500 dark:border-red-400!' : '' }}">
                <option value="" {{ old('bank_name', $employee->bank_name ?? '') === '' ? 'selected' : '' }}>Select bank</option>
                @foreach(['BDO', 'BPI', 'Metrobank', 'Landbank', 'PNB', 'Security Bank', 'EastWest', 'UnionBank', 'RCBC', 'China Bank'] as $opt)
                    <option value="{{ $opt }}" {{ old('bank_name', $employee->bank_name ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        @error('bank_name')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="bank_account_number" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Bank Account No. <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
        </label>
        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}"
            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('bank_account_number') ? 'border-red-500 dark:border-red-400!' : '' }}">
        @error('bank_account_number')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

