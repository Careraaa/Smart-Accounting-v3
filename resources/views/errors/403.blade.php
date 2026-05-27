<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Access Forbidden">
    <title>403 - Access Forbidden | Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes pulse-glow { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.7; } }
        @keyframes slide-up { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .animate-glow { animation: pulse-glow 3s ease-in-out infinite; }
        .animate-slide { animation: slide-up 0.5s ease-out forwards; }
        .animate-slide-delay { animation: slide-up 0.5s ease-out 0.1s forwards; opacity: 0; }
        .animate-slide-delay-2 { animation: slide-up 0.5s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>
<body class="font-[Sora,sans-serif] antialiased min-h-screen flex items-center justify-center bg-white p-6">
    <div class="relative w-full max-w-[1100px]">
        <div class="absolute w-[380px] h-[380px] rounded-full bg-[radial-gradient(circle,rgba(200,41,42,0.07)_0%,transparent_70%)] top-[-120px] left-[-120px] pointer-events-none z-0 hidden md:block animate-glow"></div>
        <div class="absolute w-[320px] h-[320px] rounded-full bg-[radial-gradient(circle,rgba(17,24,39,0.04)_0%,transparent_70%)] bottom-[-100px] right-[-100px] pointer-events-none z-0 hidden md:block animate-glow" style="animation-delay: 1.5s;"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-8 relative z-10">
            <div class="order-2 md:order-1 text-center md:text-left">
                <div class="animate-slide inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.68rem] font-bold uppercase tracking-[1px] bg-red-50 text-[#c8292a] border border-red-100 mb-4">Restricted</div>
                <h1 class="animate-slide-delay text-[clamp(4.5rem,14vw,9rem)] font-black leading-none text-[#c8292a] mb-2">403</h1>
                <h2 class="animate-slide-delay text-lg md:text-xl font-bold text-gray-900 mb-3">You shall not pass.</h2>
                <p class="animate-slide-delay-2 text-gray-500 leading-relaxed max-w-[480px] text-sm md:text-base mb-2">
                    You don't have permission to access this page. This area is restricted to authorized personnel only.
                </p>
                <p class="animate-slide-delay-2 text-gray-400 text-xs max-w-[480px] mb-5">Contact your system administrator if you believe this is a mistake.</p>
                <div class="animate-slide-delay-2 flex gap-3 flex-wrap justify-center md:justify-start">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 font-bold text-sm bg-[#c8292a] text-white shadow-[0_4px_16px_rgba(200,41,42,0.3)] hover:bg-[#a81f20] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150 no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Return Home
                    </a>
                    <a href="javascript:history.back()" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 font-bold text-sm border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 transition-all duration-150 no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Go Back
                    </a>
                </div>
            </div>
            <div class="order-1 md:order-2 flex justify-center items-center py-4">
                <img src="{{ url('images/403%20icon.png') }}" alt="Access Forbidden" class="w-[200px] h-[200px] md:w-[380px] md:h-[380px] object-contain animate-float">
            </div>
        </div>
    </div>
</body>
</html>
