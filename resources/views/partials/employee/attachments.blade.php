{{-- Attachments --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Attachments</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Upload relevant documents. Accepted formats: PDF, JPG, PNG, DOC. Max 5MB each.</div>

    <div class="flex items-start gap-2.5 px-4 py-3.5 rounded-xl text-xs font-medium mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Only upload documents that are complete and legible. Ensure all details are visible before uploading.</span>
    </div>

    {{-- Existing Attachments --}}
    @php
        $attTypes = [
            'resume'                 => 'Resume',
            'psa_birth_certificate'  => 'PSA Birth Certificate',
            'nbi_clearance'          => 'NBI Clearance',
            'medical_certificate'    => 'Medical Certificate',
            'government_id'          => 'Valid Government ID (Front & Back)',
            'diploma'                => 'Diploma',
            'contract'               => 'Contract',
        ];
    @endphp

    @php $latestAttachments = isset($employee) ? $employee->latestAttachments : collect(); @endphp

    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($attTypes as $key => $label)
            @php $existingAtt = $latestAttachments->get($key); @endphp
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3.5 flex flex-col gap-2.5 transition-colors duration-200
                {{ $existingAtt ? 'border-green-500 dark:border-green-400' : '' }}"
                id="att-card-{{ $key }}">

                <div class="text-xs font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $label }}
                </div>

                @if($existingAtt)
                    <div class="flex items-center gap-2 px-2.5 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        @if($existingAtt->is_image)
                            <img src="{{ $existingAtt->url }}" alt="{{ $label }}" class="w-10 h-10 object-cover rounded-lg">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 008.586 5H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">{{ $existingAtt->original_name ?? basename($existingAtt->file_path) }}</div>
                            <div class="text-[0.7rem] text-gray-400 dark:text-gray-500">{{ $existingAtt->created_at ? $existingAtt->created_at->format('M d, Y h:i A') : '' }}</div>
                        </div>
                        <label class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:border-red-300 dark:hover:border-red-600 text-[0.65rem] font-semibold cursor-pointer transition-all active:scale-[0.95]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <input type="file" name="attachments[{{ $key }}]" class="hidden" onchange="this.closest('[id^=\'att-card-\']').querySelector('.file-name')?.remove(); this.closest('[id^=\'att-card-\']').classList.toggle('border-green-500', this.files.length > 0)">
                            Replace
                        </label>
                    </div>
                    <input type="hidden" name="existing_attachments[{{ $key }}]" value="1">
                @else
                    <label class="flex flex-col items-center justify-center gap-1.5 px-3 py-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer transition-all duration-150 hover:border-red-300 dark:hover:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="text-[0.65rem] font-semibold text-gray-400 dark:text-gray-500">Click to upload</span>
                        <input type="file" name="attachments[{{ $key }}]" class="hidden" onchange="
                            const card = this.closest('[id^=\'att-card-\']');
                            card.classList.add('border-green-500', 'dark:border-green-400');
                            const label = this.closest('label');
                            const name = this.files[0]?.name || '';
                            if (name) {
                                const existing = card.querySelector('.file-name');
                                if (existing) existing.remove();
                                const div = document.createElement('div');
                                div.className = 'file-name text-[0.65rem] font-semibold text-green-600 dark:text-green-400 truncate';
                                div.textContent = name;
                                label.after(div);
                            }
                        ">
                    </label>
                @endif
            </div>
        @endforeach
    </div>
</div>
