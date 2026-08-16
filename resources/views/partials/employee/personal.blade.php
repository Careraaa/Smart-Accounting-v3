{{-- Personal Info --}}
<div class="space-y-6">

    {{-- Profile Photo --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pb-4">
        <div class="relative w-16 h-16 rounded-xl overflow-hidden border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
            @if(isset($employee) && $employee->photo_url)
                <img src="{{ $employee->photo_url }}" alt="Photo" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Profile Photo <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <label for="photo" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/20 cursor-pointer hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors border-r border-gray-200 dark:border-gray-700 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Choose File
                </label>
                <input type="file" name="photo" id="photo" accept="image/*" class="sr-only" onchange="this.nextElementSibling.textContent = this.files[0]?.name || 'No file chosen';">
                <span class="flex items-center px-3.5 py-2.5 text-sm text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 flex-1 truncate">No file chosen</span>
            </div>
            @error('photo')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Full Name --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Full Name</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label for="first_name" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                First Name <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('first_name') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('first_name')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="middle_name" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Middle Name <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $employee->middle_name ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('middle_name') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('middle_name')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="last_name" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Last Name <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('last_name') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('last_name')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Birth Details --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Birth Details</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="date_of_birth" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Birth Date <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d') ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('date_of_birth') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('date_of_birth')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="place_of_birth" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Birth Place <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('place_of_birth') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('place_of_birth')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Demographics --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Demographics</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="gender" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Sex <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="relative">
                <select name="gender" id="gender" required
                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('gender') ? 'border-red-500 dark:border-red-400!' : '' }}">
                    <option value="" disabled {{ old('gender', $employee->gender ?? '') === '' ? 'selected' : '' }}>Select sex</option>
                    @foreach(['male', 'female'] as $opt)
                        <option value="{{ $opt }}" {{ old('gender', $employee->gender ?? '') === $opt ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $opt)) }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('gender')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="civil_status" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Civil Status <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="relative">
                <select name="civil_status" id="civil_status" required
                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('civil_status') ? 'border-red-500 dark:border-red-400!' : '' }}">
                    <option value="" disabled {{ old('civil_status', $employee->civil_status ?? '') === '' ? 'selected' : '' }}>Select status</option>
                    @foreach(['Single', 'Married', 'Divorced', 'Widowed', 'Separated'] as $opt)
                        <option value="{{ $opt }}" {{ old('civil_status', $employee->civil_status ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('civil_status')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div id="spouse_name_container" class="{{ old('civil_status', $employee->civil_status ?? '') !== 'Married' ? 'hidden' : '' }}">
            <label for="spouse_name" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Spouse Name <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="text" name="spouse_name" id="spouse_name" value="{{ old('spouse_name', $employee->spouse_name ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('spouse_name') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('spouse_name')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const civilStatusSelect = document.getElementById('civil_status');
            const spouseNameContainer = document.getElementById('spouse_name_container');

            function toggleSpouseName() {
                if (civilStatusSelect.value === 'Married') {
                    spouseNameContainer.classList.remove('hidden');
                } else {
                    spouseNameContainer.classList.add('hidden');
                }
            }

            civilStatusSelect.addEventListener('change', toggleSpouseName);
        });
    </script>

    {{-- Section: Education --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Education</div>
    <div class="grid gap-4 grid-cols-1">
        <div>
            <label for="educational_attainment" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Educational Attainment <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="relative">
                <select name="educational_attainment" id="educational_attainment" required
                    class="w-full appearance-none border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 pr-9 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 {{ $errors->has('educational_attainment') ? 'border-red-500 dark:border-red-400!' : '' }}">
                    <option value="" disabled {{ old('educational_attainment', $employee->educational_attainment ?? '') === '' ? 'selected' : '' }}>Select educational attainment</option>
                    @foreach(['Elementary', 'High School', 'Senior High School', 'Vocational / TESDA', "College (Bachelor's)",] as $opt)
                        <option value="{{ $opt }}" {{ old('educational_attainment', $employee->educational_attainment ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            @error('educational_attainment')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Contact --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Contact Information</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
        <div>
            <label for="phone" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Mobile Number <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <div class="flex items-stretch border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-150 focus-within:border-red-400 dark:focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-500/10">
                <span class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 text-sm border-r border-gray-200 dark:border-gray-600 whitespace-nowrap font-mono">+63</span>
                <input type="text" name="phone" id="phone" value="{{ preg_replace('/^(?:\+63|0)/', '', old('phone', $employee->phone ?? '')) }}" required placeholder="9123456789" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').replace(/^0+/,'')"
                    class="flex-1 border-none! rounded-none! shadow-none! focus:ring-0! px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('phone') ? 'border-red-500 dark:border-red-400!' : '' }}">
            </div>
            @error('phone')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="email" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Email Address <span class="text-gray-400 dark:text-gray-500 font-normal normal-case tracking-normal text-[0.72rem]">(optional)</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $employee->email ?? '') }}"
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('email') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('email')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Section: Address --}}
    <div class="text-[0.68rem] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800 pb-2.5 mt-6 mb-4">Address</div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg">
        <div>
            <label for="address_street" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Street <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="address_street" id="address_street" value="{{ old('address_street', $employee->address_street ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('address_street') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('address_street')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="address_barangay" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Barangay <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="address_barangay" id="address_barangay" value="{{ old('address_barangay', $employee->address_barangay ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('address_barangay') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('address_barangay')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg: mt-4">
        
        <div>
            <label for="address_city" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                City / Municipality <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="address_city" id="address_city" value="{{ old('address_city', $employee->address_city ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('address_city') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('address_city')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="address_province" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
                Province <span class="text-red-500 dark:text-red-400">*</span>
            </label>
            <input type="text" name="address_province" id="address_province" value="{{ old('address_province', $employee->address_province ?? '') }}" required
                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-sans text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 {{ $errors->has('address_province') ? 'border-red-500 dark:border-red-400!' : '' }}">
            @error('address_province')
                <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
