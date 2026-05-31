{{-- Account Credentials --}}
<div class="space-y-6">

    <div class="text-base font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight">Account Credentials</div>
    <div class="text-xs text-gray-400 dark:text-gray-500 mt-[-14px] mb-[18px]">Login credentials for the employee portal.</div>

    {{-- Username --}}
    <div>
        <label for="username" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Username <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <input type="text" name="username" id="username" value="{{ old('username', $employee->username ?? '') }}" readonly
            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-mono text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 read-only:bg-gray-50 dark:read-only:bg-gray-800/50 read-only:text-gray-500 dark:read-only:text-gray-400">
        @error('username')
            <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
        @enderror
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Auto-generated based on first and last name.</span>
    </div>

    {{-- Password --}}
    <div>
        <label for="password" class="block text-[0.72rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">
            Default Password <span class="text-red-500 dark:text-red-400">*</span>
        </label>
        <div class="flex gap-2 items-start">
            <div class="flex-1">
                <input type="text" name="generated_password" id="generated_password" value="{{ old('generated_password', '') }}" readonly placeholder="Auto-generated on save"
                    class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm font-mono text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none transition-all duration-150 focus:border-red-400 dark:focus:border-red-500 focus:ring-2 focus:ring-red-500/10 dark:focus:ring-red-400/20 placeholder-gray-400 dark:placeholder-gray-500 read-only:bg-gray-50 dark:read-only:bg-gray-800/50 read-only:text-gray-500 dark:read-only:text-gray-400">
                @error('generated_password')
                    <span class="block text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Auto-generated. The employee must change on first login.</span>
            </div>
        </div>
    </div>

    {{-- Role (hidden, always employee) --}}
    <input type="hidden" name="role" value="employee">

</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const firstInput = document.getElementById('first_name');
            const lastInput = document.getElementById('last_name');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('generated_password');

            function generateCredentials() {
                const first = (firstInput?.value || '').trim();
                const last = (lastInput?.value || '').trim();
                if (first && last) {
                    const base = first.toLowerCase().replace(/[^a-z]/g, '');
                    const uname = base + '.' + last.toLowerCase().replace(/[^a-z]/g, '');
                    usernameInput.value = uname;
                    const passBase = first.charAt(0).toUpperCase() + first.slice(1).toLowerCase().replace(/[^a-zA-Z]/g, '').substring(0, 6);
                    const suffix = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
                    passwordInput.value = passBase + suffix;
                }
            }

            if (firstInput && lastInput) {
                firstInput.addEventListener('input', generateCredentials);
                lastInput.addEventListener('input', generateCredentials);
                if (!usernameInput.value) generateCredentials();
            }
        });
    </script>
@endonce
