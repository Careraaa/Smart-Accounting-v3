{{-- Character References --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Character References</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Provide character references who can vouch for the employee.</div>

    <div id="referencesList" class="flex flex-col gap-3 mb-3">
        @php $refIndex = 0; @endphp

        @if(isset($employee) && $employee->charRefs && $employee->charRefs->count() > 0)
            @foreach($employee->charRefs as $ref)
                <div class="reference-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
                    <button type="button" onclick="this.closest('.reference-row').remove()"
                        class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="references[{{ $loop->index }}][name]" value="{{ old('references.' . $loop->index . '.name', $ref->name) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Position <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="references[{{ $loop->index }}][position]" value="{{ old('references.' . $loop->index . '.position', $ref->position) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 mt-3 pr-8">
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Company <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                            <input type="text" name="references[{{ $loop->index }}][company]" value="{{ old('references.' . $loop->index . '.company', $ref->company ?? '') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                        <div>
                            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Contact No. <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="references[{{ $loop->index }}][contact_number]" value="{{ old('references.' . $loop->index . '.contact_number', $ref->contact_number) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                        </div>
                    </div>
                    <div class="mt-3 pr-8">
                        <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Email <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                        <input type="email" name="references[{{ $loop->index }}][email]" value="{{ old('references.' . $loop->index . '.email', $ref->email ?? '') }}"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                </div>
                @php $refIndex = $loop->index + 1; @endphp
            @endforeach
        @endif
    </div>

    <template id="referenceTemplate">
        <div class="reference-row bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 relative transition-all duration-200">
            <button type="button" onclick="this.closest('.reference-row').remove()"
                class="absolute top-3 right-3 w-7 h-7 rounded-lg bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 flex items-center justify-center cursor-pointer text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Full Name <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="references[__INDEX__][name]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Position <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="references[__INDEX__][position]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 mt-3 pr-8">
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Company <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                    <input type="text" name="references[__INDEX__][company]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                <div>
                    <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Contact No. <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="references[__INDEX__][contact_number]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
            <div class="mt-3 pr-8">
                <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Email <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span></label>
                <input type="email" name="references[__INDEX__][email]" class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500">
            </div>
        </div>
    </template>

    <button type="button" onclick="addReference()"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-[0.97]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Reference
    </button>
</div>

<script>
    let refIndex = {{ $refIndex ?? 0 }};

    function addReference() {
        const template = document.getElementById('referenceTemplate');
        const html = template.content.querySelector('.reference-row').outerHTML.replace(/__INDEX__/g, refIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.getElementById('referencesList').appendChild(wrapper.firstElementChild);
        refIndex++;
    }
</script>
