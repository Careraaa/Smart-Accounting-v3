<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account In Use | Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes pulse-glow { 0%, 100% { opacity: 0.2; } 50% { opacity: 0.6; } }
        @keyframes slide-up { 0% { opacity: 0; transform: translateY(16px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .animate-glow { animation: pulse-glow 3s ease-in-out infinite; }
        .animate-slide { animation: slide-up 0.5s ease-out forwards; }
        .animate-slide-delay { animation: slide-up 0.5s ease-out 0.1s forwards; opacity: 0; }
        .animate-slide-delay-2 { animation: slide-up 0.5s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>
<body class="font-[Sora,sans-serif] antialiased min-h-screen flex items-center justify-center bg-gray-50 p-5 md:p-8">
    <div class="relative w-full max-w-[1100px]">
        <div class="absolute w-[280px] h-[280px] md:w-[380px] md:h-[380px] rounded-full bg-[radial-gradient(circle,rgba(234,179,8,0.08)_0%,transparent_70%)] top-[-80px] right-[-80px] md:top-[-120px] md:right-[-120px] pointer-events-none z-0 hidden md:block animate-glow"></div>
        <div class="absolute w-[240px] h-[240px] md:w-[320px] md:h-[320px] rounded-full bg-[radial-gradient(circle,rgba(17,24,39,0.03)_0%,transparent_70%)] bottom-[-60px] left-[-60px] md:bottom-[-100px] md:left-[-100px] pointer-events-none z-0 hidden md:block animate-glow" style="animation-delay: 1.5s;"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-6 md:gap-8 relative z-10">
            <div class="order-2 md:order-1 text-center md:text-left">
                <div class="animate-slide inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-[1px] bg-amber-50 text-amber-600 border border-amber-100 mb-3 md:mb-4">Session Conflict</div>
                <h1 class="animate-slide-delay text-[clamp(3.5rem,12vw,9rem)] font-black leading-none text-indigo-600 mb-1">409</h1>
                <h2 class="animate-slide-delay text-base md:text-xl font-bold text-gray-900 mb-2 md:mb-3">A new challenger approaches.</h2>
                <p class="animate-slide-delay-2 text-gray-500 leading-relaxed max-w-[480px] text-sm md:text-base mb-1">
                    Another session has taken over this account from a different device. Only one active session is allowed at a time.
                </p>
                <p class="animate-slide-delay-2 text-gray-400 text-xs max-w-[480px] mb-4 md:mb-5">Contact your system administrator immediately if you believe this is a mistake.</p>
                <div class="animate-slide-delay-2 flex gap-3 flex-wrap justify-center md:justify-start">
                    <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 font-bold text-sm bg-indigo-600 text-white shadow-[0_4px_16px_rgba(99,102,241,0.3)] hover:bg-indigo-700 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150 no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Log In Again
                    </a>
                </div>
            </div>
            <div class="order-1 md:order-2 flex justify-center items-center py-2 md:py-4">
                <img src="{{ url('images/InuseAcc%20icon.png') }}" alt="Account In Use" class="w-28 h-28 md:w-[380px] md:h-[380px] object-contain animate-float">
            </div>
        </div>
    </div>
</body>
</html>
