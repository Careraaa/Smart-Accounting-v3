<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="System Maintenance">
    <title>System Under Maintenance | Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes pulse-glow { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.7; } }
        @keyframes slide-up { 0% { opacity: 0; transform: translateY(16px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .animate-glow { animation: pulse-glow 3s ease-in-out infinite; }
        .animate-slide { animation: slide-up 0.5s ease-out forwards; }
        .animate-slide-delay { animation: slide-up 0.5s ease-out 0.1s forwards; opacity: 0; }
        .animate-slide-delay-2 { animation: slide-up 0.5s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>
<body class="font-[Sora,sans-serif] antialiased min-h-screen flex items-center justify-center bg-white p-5 md:p-8">
    @php
        $icon = file_exists(public_path('images/504 icon.png'))
            ? url('images/504%20icon.png')
            : (file_exists(public_path('images/503 icon.png')) ? url('images/503%20icon.png') : asset('images/knights_logo_icon.png'));
    @endphp

    <div class="relative w-full max-w-[1160px]">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-6 md:gap-8">
            <div class="order-2 md:order-1 text-center md:text-left">
                <div class="animate-slide inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-[1px] bg-blue-50 text-[#1d4ed8] border border-blue-100 mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    System Notice
                </div>
                <h1 class="animate-slide-delay text-[clamp(3.5rem,12vw,9rem)] font-black leading-none text-[#2563eb] mb-1">503</h1>
                <h2 class="animate-slide-delay text-base md:text-xl font-bold text-gray-900 mb-2 md:mb-3">The blacksmith is hard at work.</h2>
                <p class="animate-slide-delay-2 text-gray-500 leading-relaxed max-w-[480px] text-sm md:text-base mb-1">
                    We're strengthening the castle walls and improving our defenses. Smart Accounting will return shortly, stronger than before.
                </p>
                <div class="animate-slide-delay-2 flex items-center gap-2 justify-center md:justify-start text-gray-400 text-xs mb-4 md:mb-5">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Super admin access remains available for emergency work
                </div>
                <div class="animate-slide-delay-2">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 font-bold text-sm bg-[#c8292a] text-white shadow-[0_4px_16px_rgba(200,41,42,0.3)] hover:bg-[#a81f20] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150 border-none cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Back to Login
                            </button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 font-bold text-sm bg-[#c8292a] text-white shadow-[0_4px_16px_rgba(200,41,42,0.3)] hover:bg-[#a81f20] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150 no-underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Back to Login
                        </a>
                    @endauth
                </div>
            </div>

            <div class="order-1 md:order-2 flex justify-center items-center py-2 md:py-4">
                <img src="{{ $icon }}" alt="Maintenance" class="w-28 h-28 md:w-[380px] md:h-[380px] object-contain animate-float">
            </div>
        </div>
    </div>
</body>
</html>
