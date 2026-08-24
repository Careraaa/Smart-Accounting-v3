{{-- Employment --}}
<div class="space-y-6">

    {{-- Section: Employment Details --}}
    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Employment Details</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="position" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Position <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="position" id="position" value="{{ old('position', $employee->position ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('position') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('position')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="department" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Department <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="relative">
                <select name="department" id="department" required
                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('department') ? 'border-red-500 dark:border-red-400!' : '' }}">
                    <option value="" disabled {{ old('department', $employee->department ?? '') === '' ? 'selected' : '' }}>Select department</option>
                    @if(isset($departments) && $departments->count())
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ old('department', $employee->department ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    @endif
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('department')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Dates --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Employment Dates</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="date_of_hire" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Hire Date <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="date" name="date_of_hire" id="date_of_hire" value="{{ old('date_of_hire', $employee->date_of_hire?->format('Y-m-d') ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('date_of_hire') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('date_of_hire')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="status" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Status <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="relative">
                <select name="status" id="status" required
                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('status') ? 'border-red-500 dark:border-red-400!' : '' }}">
                    <option value="" disabled {{ old('status', $employee->status ?? '') === '' ? 'selected' : '' }}>Select status</option>
                    @foreach(['active', 'inactive'] as $opt)
                        <option value="{{ $opt }}" {{ old('status', $employee->status ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('status')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    @include('partials.employee.employment_hr')
</div>
