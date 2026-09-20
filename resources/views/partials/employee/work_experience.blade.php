{{-- Work Experience --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Work Experience</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">List the employee's previous work experiences.</div>

    <div id="workExperienceList" class="flex flex-col gap-3 mb-3">
        @php $weIndex = 0; @endphp

        @if(isset($employee) && $employee->workExperiences && $employee->workExperiences->count() > 0)
            @foreach($employee->workExperiences as $exp)
                <div class="work-exp-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
                    <button type="button" onclick="this.closest('.work-exp-row').remove()"
                        class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Company <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="work_experiences[{{ $loop->index }}][company]" value="{{ old('work_experiences.' . $loop->index . '.company', $exp->company_name) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Position <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="work_experiences[{{ $loop->index }}][position]" value="{{ old('work_experiences.' . $loop->index . '.position', $exp->position) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 mt-3 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">From <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="date" name="work_experiences[{{ $loop->index }}][from]" value="{{ old('work_experiences.' . $loop->index . '.from', '') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">To <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                            <input type="date" name="work_experiences[{{ $loop->index }}][to]" value="{{ old('work_experiences.' . $loop->index . '.to', '') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>
                    <div class="mt-3 pr-8">
                        <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Description <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                        <textarea name="work_experiences[{{ $loop->index }}][description]" rows="2"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 resize-none">{{ old('work_experiences.' . $loop->index . '.description', $exp->responsibilities ?? '') }}</textarea>
                    </div>
                </div>
                @php $weIndex = $loop->index + 1; @endphp
            @endforeach
        @endif
    </div>

    <template id="workExpTemplate">
        <div class="work-exp-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
            <button type="button" onclick="this.closest('.work-exp-row').remove()"
                class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Company <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="work_experiences[__INDEX__][company]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Position <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="work_experiences[__INDEX__][position]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 mt-3 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">From <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="date" name="work_experiences[__INDEX__][from]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">To <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                    <input type="date" name="work_experiences[__INDEX__][to]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
            <div class="mt-3 pr-8">
                <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Description <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                <textarea name="work_experiences[__INDEX__][description]" rows="2" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 resize-none"></textarea>
            </div>
        </div>
    </template>

    <button type="button" onclick="addWorkExperience()"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Work Experience
    </button>
</div>

<script>
    let weIndex = {{ $weIndex ?? 0 }};

    function addWorkExperience() {
        const template = document.getElementById('workExpTemplate');
        const clone = template.content.cloneNode(true);
        const html = clone.querySelector('.work-exp-row').outerHTML.replace(/__INDEX__/g, weIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('workExperienceList').appendChild(wrapper.firstElementChild);
        weIndex++;
    }
</script>
