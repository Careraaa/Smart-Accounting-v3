{{-- Special Skills --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Special Skills</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">List relevant skills and proficiency levels.</div>

    <div id="skillsList" class="flex flex-col gap-3 mb-3">
        @php $skIndex = 0; @endphp

        @if(isset($employee) && $employee->specialSkills && $employee->specialSkills->count() > 0)
            @foreach($employee->specialSkills as $skill)
                <div class="skill-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
                    <button type="button" onclick="this.closest('.skill-row').remove()"
                        class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-3 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Skill <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="skills[{{ $loop->index }}][name]" value="{{ old('skills.' . $loop->index . '.name', $skill->skill_name) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Proficiency <span class="text-red-500 dark:text-red-400">*</span></label>
                            <div class="relative">
                                <select name="skills[{{ $loop->index }}][proficiency]"
                                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20">
                                    <option value="Beginner" {{ old('skills.' . $loop->index . '.proficiency', $skill->proficiency) === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="Intermediate" {{ old('skills.' . $loop->index . '.proficiency', $skill->proficiency) === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="Advanced" {{ old('skills.' . $loop->index . '.proficiency', $skill->proficiency) === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                    <option value="Expert" {{ old('skills.' . $loop->index . '.proficiency', $skill->proficiency) === 'Expert' ? 'selected' : '' }}>Expert</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Remarks <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                            <input type="text" name="skills[{{ $loop->index }}][remarks]" value="{{ old('skills.' . $loop->index . '.remarks', $skill->remarks ?? '') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>
                </div>
                @php $skIndex = $loop->index + 1; @endphp
            @endforeach
        @endif
    </div>

    <template id="skillTemplate">
        <div class="skill-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
            <button type="button" onclick="this.closest('.skill-row').remove()"
                class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-3 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Skill <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="skills[__INDEX__][name]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Proficiency <span class="text-red-500 dark:text-red-400">*</span></label>
                    <div class="relative">
                        <select name="skills[__INDEX__][proficiency]" class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Remarks <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                    <input type="text" name="skills[__INDEX__][remarks]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
        </div>
    </template>

    <button type="button" onclick="addSkill()"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Skill
    </button>
</div>

<script>
    let skIndex = {{ $skIndex ?? 0 }};

    function addSkill() {
        const template = document.getElementById('skillTemplate');
        const html = template.content.querySelector('.skill-row').outerHTML.replace(/__INDEX__/g, skIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('skillsList').appendChild(wrapper.firstElementChild);
        skIndex++;
    }
</script>
