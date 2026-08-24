<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Sign In · Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(14px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes popIn {
            0% { transform: scale(0.4); opacity: 0; }
            70% { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }
        .fade-item { animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .fade-item:nth-child(1) { animation-delay: 0.05s; }
        .fade-item:nth-child(2) { animation-delay: 0.12s; }
        .fade-item:nth-child(3) { animation-delay: 0.2s; }
        .fade-item:nth-child(4) { animation-delay: 0.28s; }
        .token-chip { animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.15s both; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-[#0a0a10] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-[400px] mx-auto">
        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl shadow-black/20 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 pt-7 pb-5 text-center fade-item">
                <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-12 h-12 object-contain rounded-xl mx-auto mb-3">
                <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">Sign in to record attendance</h1>
                <p class="text-xs text-gray-400 mt-1">Use your account credentials below</p>
            </div>

            {{-- Token badge --}}
            <div class="mx-6 mb-4 fade-item">
                <div class="token-chip flex items-center justify-center gap-2 bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-2">
                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-[0.55rem] font-semibold text-emerald-700 uppercase tracking-wider">One-time QR token active</span>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('attendance.qr.login', $token) }}" class="px-6 pb-7">
                @csrf

                @if ($errors->any())
                <div class="fade-item mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
                @endif

                {{-- Username --}}
                <div class="fade-item relative mb-5">
                    <input type="text" name="username" id="username" required autocomplete="username"
                        value="{{ old('username') }}" autofocus
                        placeholder=" "
                        class="peer w-full h-13 px-4 pt-5 pb-1 text-sm border-2 border-gray-200 rounded-xl bg-white outline-none transition-all duration-200 focus:border-gray-900 focus:shadow-[0_0_0_4px_rgba(0,0,0,0.04)]">
                    <label for="username"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none transition-all duration-200 peer-focus:top-2 peer-focus:-translate-y-0 peer-focus:text-xs peer-focus:text-gray-600 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:-translate-y-0 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-gray-500">
                        Username
                    </label>
                </div>

                {{-- Password --}}
                <div class="fade-item relative mb-6">
                    <input type="password" name="password" id="password" required
                        placeholder=" "
                        class="peer w-full h-13 px-4 pt-5 pb-1 pr-12 text-sm border-2 border-gray-200 rounded-xl bg-white outline-none transition-all duration-200 focus:border-gray-900 focus:shadow-[0_0_0_4px_rgba(0,0,0,0.04)]">
                    <label for="password"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none transition-all duration-200 peer-focus:top-2 peer-focus:-translate-y-0 peer-focus:text-xs peer-focus:text-gray-600 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:-translate-y-0 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-gray-500">
                        Password
                    </label>
                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none p-1.5 rounded-lg transition-colors">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="fade-item w-full bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm rounded-xl py-3.5 cursor-pointer active:scale-[0.97] focus:outline-none focus:ring-[3px] focus:ring-gray-900/25 transition-all duration-150 inline-flex items-center justify-center gap-2">
                    Sign In & Record Attendance
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-[0.55rem] text-gray-600/40 mt-4 fade-item tracking-wider uppercase">Knights Transport · Smart Accounting System</p>
    </div>

    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            var input = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>';
            }
        });
    </script>
</body>
</html>
