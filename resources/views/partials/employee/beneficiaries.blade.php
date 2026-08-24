{{-- Beneficiaries --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Beneficiaries</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">List the employee's beneficiaries and their allocations.</div>

    <div id="beneficiariesList" class="flex flex-col gap-3 mb-3">
        @php $benIndex = 0; @endphp

        @if(isset($employee) && $employee->beneficiaries && $employee->beneficiaries->count() > 0)
            @foreach($employee->beneficiaries as $ben)
                <div class="beneficiary-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
                    <button type="button" onclick="this.closest('.beneficiary-row').remove()"
                        class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="beneficiaries[{{ $loop->index }}][name]" value="{{ old('beneficiaries.' . $loop->index . '.name', $ben->name) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Relationship <span class="text-red-500 dark:text-red-400">*</span></label>
                            <div class="relative">
                                <select name="beneficiaries[{{ $loop->index }}][relationship]"
                                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20">
                                    @foreach(['Spouse', 'Child', 'Parent', 'Sibling', 'Relative', 'Other'] as $opt)
                                        <option value="{{ $opt }}" {{ old('beneficiaries.' . $loop->parent->index . '.relationship', $ben->relationship ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Birth Date <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="date" name="beneficiaries[{{ $loop->index }}][birth_date]" value="{{ old('beneficiaries.' . $loop->index . '.birth_date', $ben->birth_date ?? '') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Percentage <span class="text-red-500 dark:text-red-400">*</span></label>
                            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                                <input type="number" step="0.01" min="0" max="100" name="beneficiaries[{{ $loop->index }}][percentage]" value="{{ old('beneficiaries.' . $loop->index . '.percentage', $ben->percentage ?? '') }}"
                                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500">
                                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-l border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                @php $benIndex = $loop->index + 1; @endphp
            @endforeach
        @endif
    </div>

    <template id="beneficiaryTemplate">
        <div class="beneficiary-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
            <button type="button" onclick="this.closest('.beneficiary-row').remove()"
                class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="beneficiaries[__INDEX__][name]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Relationship <span class="text-red-500 dark:text-red-400">*</span></label>
                    <div class="relative">
                        <select name="beneficiaries[__INDEX__][relationship]" class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20">
                            @foreach(['Spouse', 'Child', 'Parent', 'Sibling', 'Relative', 'Other'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Birth Date <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="date" name="beneficiaries[__INDEX__][birth_date]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Percentage <span class="text-red-500 dark:text-red-400">*</span></label>
                    <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                        <input type="number" step="0.01" min="0" max="100" name="beneficiaries[__INDEX__][percentage]"
                            class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500">
                        <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-l border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">%</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <button type="button" onclick="addBeneficiary()"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Beneficiary
    </button>
</div>

<script>
    let benIndex = {{ $benIndex ?? 0 }};

    function addBeneficiary() {
        const template = document.getElementById('beneficiaryTemplate');
        const html = template.content.querySelector('.beneficiary-row').outerHTML.replace(/__INDEX__/g, benIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('beneficiariesList').appendChild(wrapper.firstElementChild);
        benIndex++;
    }
</script>
