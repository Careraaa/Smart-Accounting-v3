<header class="fixed top-0 left-0 right-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center">
    <div class="flex items-center w-full h-full px-4 sm:px-6">

        {{-- Mobile menu toggle (hidden on desktop) --}}
        <button class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="mobile-menu-btn" type="button" aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Greeting --}}
        @php
            $user = auth()->user();
            $name = $user->first_name ?? $user->name ?? 'User';
            $role = $user->role;

            $roleGreetings = [
                'superadmin' => [
                    "Welcome back, $name. The entire system is ready for your command.",
                    "Good to see you, $name. Another day of keeping everything running smoothly.",
                    "The dashboard is looking sharp today, $name.",
                    "Welcome back, $name. Every great system starts with great leadership.",
                    "Hello, $name. Your control center is standing by.",
                    "A successful organization begins with good decisions. Ready for today's, $name?",
                    "Good day, $name. The team's progress starts here.",
                    "Welcome, $name. Time to turn plans into results.",
                    "Your kingdom awaits, $name. Rule wisely.",
                    "Hello, $name. The gears are turning and the system is ready.",
                    "Another opportunity to make an impact, $name. Let's get started.",
                    "Welcome back, $name. Excellence is built one decision at a time.",
                    "Good day, $name. The dashboard has been expecting you.",
                    "Your mission control is online, $name.",
                    "Great leaders inspire progress. Welcome back, $name.",
                ],
                'hr' => [
                    "Welcome back, $name. People are the heart of every organization.",
                    "Good day, $name. Time to help the team thrive.",
                    "Hello, $name. Every employee story matters.",
                    "Welcome back, $name. Building a better workplace starts with you.",
                    "Great workplaces don't happen by accident. Thanks for being part of it, $name.",
                    "Good to see you, $name. Your team is counting on your support.",
                    "Welcome, $name. Another day to make work a little better for everyone.",
                    "Hello, $name. Behind every successful company is a dedicated HR team.",
                    "Good day, $name. Let's create positive experiences today.",
                    "Welcome back, $name. Helping people succeed is a powerful mission.",
                    "Your HR dashboard is ready, $name.",
                    "Great culture starts with small actions. Welcome back, $name.",
                    "Hello, $name. Every great team needs someone looking out for them.",
                    "Welcome, $name. Let's keep the workplace organized and supported.",
                    "Another day to empower employees, $name.",
                ],
                'accountant' => [
                    "Welcome back, $name. Every number tells a story.",
                    "Good day, $name. Let's keep the books balanced and the future bright.",
                    "Hello, $name. Precision is your superpower.",
                    "Welcome back, $name. Today's numbers are waiting.",
                    "Good to see you, $name. Accuracy makes all the difference.",
                    "Welcome, $name. Turning figures into insights, one entry at a time.",
                    "Hello, $name. Financial clarity starts here.",
                    "Another productive day ahead, $name. Let's make every cent count.",
                    "Good day, $name. Balance sheets and opportunities await.",
                    "Welcome back, $name. Behind every strong business is solid accounting.",
                    "Your financial dashboard is ready, $name.",
                    "Hello, $name. Small details create big results.",
                    "Welcome, $name. Time to bring order to the numbers.",
                    "Every transaction matters. Welcome back, $name.",
                    "Good day, $name. Let's keep everything adding up.",
                ],
                'remittance_clerk' => [
                    "Welcome back, $name. Keeping transactions moving smoothly today.",
                    "Good day, $name. Every accurate remittance builds trust.",
                    "Hello, $name. Ready to keep things flowing efficiently?",
                    "Welcome, $name. Precision and reliability start here.",
                    "Good to see you, $name. Another day of helping operations run smoothly.",
                    "Welcome back, $name. Every successful transfer begins with careful work.",
                    "Hello, $name. Your dashboard is ready for today's transactions.",
                    "Good day, $name. Accuracy is the key to every successful remittance.",
                    "Welcome, $name. Making financial processes seamless, one task at a time.",
                    "Another productive day ahead, $name.",
                    "Welcome back, $name. Consistency creates confidence.",
                    "Hello, $name. Small details make a big difference.",
                    "Good day, $name. Your work helps keep everything connected.",
                    "Welcome, $name. Efficiency looks good on you.",
                    "Every completed task helps someone. Welcome back, $name.",
                ],
                'employee' => [
                    "Welcome back, $name. Hope you're having a great day.",
                    "Good day, $name. Let's make today productive and rewarding.",
                    "Hello, $name. Your dashboard is ready when you are.",
                    "Welcome back, $name. Every contribution matters.",
                    "Good to see you, $name. You're an important part of the team.",
                    "Welcome, $name. Small steps lead to big achievements.",
                    "Hello, $name. Another opportunity to do great work today.",
                    "Good day, $name. Wishing you a smooth and successful day.",
                    "Welcome back, $name. Progress begins with showing up.",
                    "Hello, $name. Let's accomplish something great today.",
                    "Your workspace is ready, $name.",
                    "Good day, $name. Every task completed is a step forward.",
                    "Welcome, $name. Success is built one day at a time.",
                    "Thanks for being part of the team, $name.",
                    "Welcome back, $name. Today is full of possibilities.",
                ],
                'qr_admin' => [
                    "Welcome back, $name. The QR gates are ready for action.",
                    "Good day, $name. Attendance scanning is primed and waiting.",
                    "Hello, $name. Your QR command center is online.",
                    "Welcome back, $name. Ready to scan the day away.",
                    "Good to see you, $name. Every scan tells a story.",
                    "Welcome, $name. The QR codes have been expecting you.",
                    "Hello, $name. No lines, no delays — just smooth scanning.",
                    "Good day, $name. Another day of seamless attendance tracking.",
                    "Welcome back, $name. Your scanner is calibrated and ready.",
                    "The attendance zone is clear. Ready when you are, $name.",
                    "Welcome, $name. Making check-ins effortless, one scan at a time.",
                    "Hello, $name. Your QR dashboard is looking crisp today.",
                    "Good day, $name. Let's keep those attendance records flawless.",
                    "Welcome back, $name. Scanning duty awaits.",
                    "The system is green and ready. Over to you, $name.",
                ],
            ];

            $rareGreetings = [
                "Welcome back, $name. The coffee machine believes in you.",
                "Good day, $name. Your keyboard has been patiently waiting.",
                "Welcome, $name. No bugs were scheduled for today. Hopefully.",
                "Hello, $name. The dashboard polished itself before you arrived.",
                "Welcome back, $name. Achievement unlocked: Logged In.",
                "Good day, $name. The system reports morale levels are stable.",
                "Welcome, $name. May your loading screens be short and your tasks be easy.",
                "Hello, $name. Another beautiful day to click buttons professionally.",
                "Welcome back, $name. The server says hi.",
                "Good day, $name. Your dashboard missed you. Probably.",
                "Welcome back, $name. We checked the logs. You're definitely one of the users.",
                "Hello, $name. The system has successfully located your account. Congratulations.",
                "Welcome, $name. Today's productivity forecast: somewhere between \"not bad\" and \"legendary.\"",
                "Good day, $name. The server survived the night. We're off to a strong start.",
                "Welcome back, $name. No buttons were harmed during the previous session.",
                "Hello, $name. Your dashboard has been pretending to work while you were away.",
                "Welcome, $name. A wild workday appeared!",
                "Good day, $name. The bugs have agreed to behave today. They signed nothing, though.",
                "Welcome back, $name. Your password remains a mystery to us. As intended.",
                "Hello, $name. The coffee isn't in the dashboard, but we checked anyway.",
                "Welcome, $name. You have received +1 experience point for logging in.",
                "Good day, $name. The system believes in you more than the printer does.",
                "Welcome back, $name. If nobody told you today, your Caps Lock is probably off.",
                "Hello, $name. Another day, another carefully organized collection of buttons.",
                "Welcome, $name. The dashboard cleaned up before you arrived. You're welcome.",
            ];

            $legendaryGreetings = [
                "\xf0\x9f\x8e\x89 LEGENDARY LOGIN DETECTED. Welcome, $name. The odds of seeing this message were lower than finding a bug-free project.",
                "\xf0\x9f\x8f\x86 Congratulations, $name. You rolled the rare greeting. No prizes, only glory.",
                "\xf0\x9f\x91\x91 The prophecy spoke of your arrival, $name. It was oddly specific.",
                "\xe2\xad\x90 Rare Event Unlocked: $name has entered the dashboard.",
                "\xf0\x9f\x8e\xb2 Critical Success! $name logged in and triggered a legendary message.",
                "\xf0\x9f\x9a\x80 Welcome, $name. Statistically speaking, you're special today.",
                "\xf0\x9f\xa6\x84 A mythical greeting appears. Hello, $name.",
                "\xf0\x9f\x93\x9c Achievement Unlocked: Found the message nobody expected to see.",
                "\xe2\x9a\xa1 System Status: Normal. Greeting Status: Extremely Rare.",
                "\xf0\x9f\x8f\x85 Welcome, $name. This greeting is rarer than a perfectly formatted spreadsheet.",
            ];

            $roll = mt_rand(1, 1000);
            if ($roll <= 1) {
                $greeting = $legendaryGreetings[array_rand($legendaryGreetings)];
            } elseif ($roll <= 6) {
                $greeting = $rareGreetings[array_rand($rareGreetings)];
            } else {
                $pool = $roleGreetings[$role] ?? $roleGreetings['employee'];
                $greeting = $pool[array_rand($pool)];
            }
        @endphp
        <div class="flex-1 min-w-0 flex items-center h-full ml-2 sm:ml-4">
            <span id="typewriter" class="text-sm sm:text-base font-semibold text-gray-700 truncate"></span>
        </div>

        {{-- Right side icons --}}
        <div class="flex items-center gap-1.5 ml-auto">

            {{-- Dark mode toggle --}}
            <button class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="dark-mode-toggle" type="button" data-tip="Toggle dark mode">
                <svg class="dark-mode-sun" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <svg class="dark-mode-moon hidden" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>

            {{-- Fullscreen --}}
            <a href="javascript:void(0);" class="hidden sm:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="kt-fullscreen-btn">
                <i class="feather-maximize" style="font-size:16px"></i>
            </a>

            @if(auth()->user()->role === 'superadmin')
            @php
                $viewAs = session('view_as_role');
                $viewLabel = $viewAs ? ucwords(str_replace('_', ' ', $viewAs)) : 'Super Admin';
                $viewIcon = $viewAs ? 'feather-eye' : 'feather-shield';
            @endphp
            <div class="relative" data-dropdown>
                <button class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="view-switch-btn" type="button">
                    <i class="{{ $viewIcon }}" style="font-size:14px"></i>
                    <span class="hidden sm:inline">{{ $viewLabel }}</span>
                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div id="view-switch-dropdown" class="dropdown-closed sm:absolute sm:top-full sm:right-0 sm:mt-2 sm:w-44 sm:left-auto fixed top-16 right-4 left-4 w-auto bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                    <div class="py-1">
                        <p class="px-4 py-2 text-[0.55rem] font-bold uppercase tracking-widest text-gray-400">Switch View</p>
                        <form id="view-switch-form" method="POST" action="{{ route('superadmin.switch-view') }}">
                            @csrf
                            <input type="hidden" name="role" id="view-switch-role">
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group" data-role="superadmin" onclick="document.getElementById('view-switch-role').value='superadmin'">
                                <i class="feather-shield w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                                <span>Super Admin</span>
                                @if(!$viewAs)<span class="ml-auto text-[0.5rem] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded-full">Active</span>@endif
                            </button>
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group" data-role="hr" onclick="document.getElementById('view-switch-role').value='hr'">
                                <i class="feather-users w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                                <span>HR</span>
                                @if($viewAs === 'hr')<span class="ml-auto text-[0.5rem] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded-full">Active</span>@endif
                            </button>
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group" data-role="employee" onclick="document.getElementById('view-switch-role').value='employee'">
                                <i class="feather-user w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                                <span>Employee</span>
                                @if($viewAs === 'employee')<span class="ml-auto text-[0.5rem] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded-full">Active</span>@endif
                            </button>
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group" data-role="accountant" onclick="document.getElementById('view-switch-role').value='accountant'">
                                <i class="feather-bar-chart w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                                <span>Accountant</span>
                                @if($viewAs === 'accountant')<span class="ml-auto text-[0.5rem] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded-full">Active</span>@endif
                            </button>
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group" data-role="remittance_clerk" onclick="document.getElementById('view-switch-role').value='remittance_clerk'">
                                <i class="feather-dollar-sign w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                                <span>Remittance Clerk</span>
                                @if($viewAs === 'remittance_clerk')<span class="ml-auto text-[0.5rem] font-bold text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded-full">Active</span>@endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'superadmin' && \App\Models\Setting::get('testing_mode', 'disabled') === 'enabled')
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase tracking-wider whitespace-nowrap">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Testing Mode
            </div>
            @endif

            {{-- Notifications --}}
            @php
                $notifData = cache()->remember('notif.'.auth()->id(), 30, function() {
                    $u = auth()->user();
                    return [
                        'count' => $u->notifications()->unread()->count(),
                        'recent' => $u->notifications()->unread()->recent()->limit(3)->get(),
                    ];
                });
                $unread_count = $notifData['count'];
            @endphp
            <div class="relative" data-dropdown>
                <button class="relative flex items-center justify-center w-9 h-9 rounded-lg transition-colors hover:bg-gray-100 {{ $unread_count > 0 ? 'notif-has-unread' : '' }}" id="notification-btn" type="button">
                    <svg viewBox="0 0 24 24" fill="none" height="20" width="20" xmlns="http://www.w3.org/2000/svg" class="text-gray-500">
                        <path d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor"></path>
                    </svg>
                    <span class="notif-blip hidden"></span>
                </button>
                <div id="notification-dropdown" class="dropdown-closed sm:absolute sm:top-full sm:right-[-8px] sm:left-auto sm:mt-3 sm:w-[780px] sm:max-w-[96vw] fixed top-16 right-4 left-4 w-auto bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center"><i class="feather-bell" style="font-size:15px"></i></span>
                            <div>
                                <div class="text-sm font-bold text-gray-900">Notifications</div>
                                <div class="text-xs text-gray-500">Latest updates</div>
                            </div>
                        </div>

                    </div>
                    <div class="max-h-[420px] overflow-y-auto p-1.5">
                        <div id="notification-list">
                            @forelse($notifData['recent'] as $notification)
                                <div class="flex items-start gap-3 px-4 py-3.5 rounded-lg cursor-pointer transition-colors duration-100 hover:bg-red-50 {{ $notification->isUnread() ? 'bg-gray-50/80 font-medium' : '' }}" data-notif-id="{{ $notification->id }}" data-action-url="{{ $notification->getActionUrl() }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 mb-1">{{ $notification->title }}</div>
                                        <p class="text-sm text-gray-600 mb-1.5 leading-relaxed">{{ $notification->message }}</p>
                                        <small class="text-xs text-gray-400">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                    <i class="feather-bell-off" style="font-size:40px;opacity:0.4"></i>
                                    <p class="text-sm mt-2">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/80 border-t border-gray-100">
                        <a class="btn-uv-pill text-sm" href="{{ route('notifications.index') }}">View all</a>
                        <button class="text-sm font-bold text-red-500 no-underline hover:underline bg-transparent border-none cursor-pointer {{ $unread_count > 0 ? '' : 'hidden' }}" id="mark-all-read">Mark all as read</button>
                    </div>
                </div>
            </div>

            {{-- User Profile Dropdown --}}
            @php
                $avatarUser = auth()->user();
                $avatarPhoto = $avatarUser->photo_url;
                $avatarFirst = strtoupper(substr($avatarUser->first_name ?? $avatarUser->name ?? 'U', 0, 1));
                $avatarLast  = strtoupper(substr($avatarUser->last_name ?? '', 0, 1));
                $avatarInitials = $avatarFirst . ($avatarLast ?: '');
                $avatarSeed = crc32($avatarUser->username ?? $avatarUser->email ?? $avatarUser->id ?? 'user');
                $avatarHue = abs($avatarSeed) % 360;
                $avatarBg = "hsl({$avatarHue}, 52%, 42%)";
                $avatarRing = "hsl({$avatarHue}, 52%, 75%)";
            @endphp
            <div class="relative" data-dropdown>
                <button class="flex items-center gap-2.5 no-underline rounded-lg py-1.5 pl-2 pr-1.5 transition-all duration-200 hover:bg-gray-50 group" id="user-dropdown-btn" type="button">
                    <div class="relative w-9 h-9 min-w-[36px] flex-shrink-0">
                        @if($avatarPhoto)
                            <div id="avatar-img-wrap" class="w-full h-full rounded-full overflow-hidden shadow-sm transition-transform duration-200 group-hover:scale-105">
                                <img src="{{ $avatarPhoto }}" alt="Photo" class="w-full h-full object-cover" onerror="this.closest('.relative').querySelector('#avatar-initials').classList.remove('hidden'); this.closest('#avatar-img-wrap').classList.add('hidden');">
                            </div>
                        @endif
                        <div id="avatar-initials" class="profile-avatar-inner {{ $avatarPhoto ? 'hidden' : '' }} relative w-9 h-9 min-w-[36px] rounded-full flex items-center justify-center text-white text-[13px] font-bold shadow-sm transition-transform duration-200 group-hover:scale-105" style="background:{{ $avatarBg }};box-shadow:0 0 0 2px {{ $avatarRing }}, 0 2px 6px rgba(0,0,0,0.08)">
                            {{ $avatarInitials ?: '?' }}
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-white"></span>
                        </div>
                    </div>
                    <svg class="hidden md:block text-gray-400 transition-transform duration-200 group-hover:rotate-180" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div id="user-dropdown" class="dropdown-closed sm:absolute sm:top-full sm:right-0 sm:mt-2 sm:w-56 sm:left-auto fixed top-16 right-4 left-4 w-auto bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                    <div class="py-1.5">
                        <a href="{{ route('profile.details') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group">
                            <i class="feather-user w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i> My Profile
                        </a>
                        <a href="{{ route('employee.attachments.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group">
                            <i class="feather-folder w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i> Documents
                        </a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 no-underline transition-colors duration-100 hover:bg-red-50 group"
                            onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out w-4 text-center text-red-400 group-hover:text-red-500 transition-colors"></i> Logout
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<style>
    /* ── Navbar icon hover animations ── */
    #dark-mode-toggle:hover svg { animation: hdrSpin 0.6s cubic-bezier(0.34,1.56,0.64,1); }
    #kt-fullscreen-btn:hover i { animation: hdrExpand 0.5s cubic-bezier(0.34,1.56,0.64,1); }
    #notification-btn:hover svg { animation: hdrRing 0.5s cubic-bezier(0.34,1.56,0.64,1); }
    #notification-btn.notif-has-unread svg { animation: hdrRing 0.5s cubic-bezier(0.34,1.56,0.64,1) infinite; }
    #notification-btn.notif-has-unread:hover svg { animation: hdrRing 0.5s cubic-bezier(0.34,1.56,0.64,1) infinite; }
    #user-dropdown-btn:hover .profile-avatar-inner { animation: hdrPulse 0.6s cubic-bezier(0.34,1.56,0.64,1); }

    @keyframes hdrSpin { 0%{transform:rotate(0)} 100%{transform:rotate(180deg)} }
    @keyframes hdrExpand { 0%{transform:scale(1)} 50%{transform:scale(1.25)} 100%{transform:scale(1)} }
    @keyframes hdrRing { 0%{transform:rotate(0)} 20%{transform:rotate(12deg)} 40%{transform:rotate(-10deg)} 60%{transform:rotate(6deg)} 80%{transform:rotate(-4deg)} 100%{transform:rotate(0)} }
    @keyframes hdrPulse { 0%{box-shadow:0 0 0 0 var(--pulse-color,rgba(200,41,42,0.4))} 70%{box-shadow:0 0 0 8px transparent} 100%{box-shadow:0 0 0 0 transparent} }

    /* ── Dropdown animation ── */
    [data-dropdown-menu] {
        transition: opacity .15s ease-out, transform .15s ease-out, visibility .15s ease-out;
    }
    .dropdown-closed {
        visibility: hidden !important;
        opacity: 0 !important;
        transform: translateY(-4px) scale(.98) !important;
        pointer-events: none !important;
    }

    /* ── Notification blinking dot ── */
    .notif-blip {
        position: absolute;
        bottom: 6px;
        left: 6px;
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 4px rgba(34,197,94,.4);
    }
    .notif-blip::before {
        content: '';
        position: absolute;
        inset: 50%;
        translate: -50% -50%;
        width: 2px;
        height: 2px;
        background: #22c55e;
        border-radius: 50%;
        animation: notif-pulse 1.2s ease-in-out infinite;
    }
    @keyframes notif-pulse {
        0%   { width: 2px; height: 2px; opacity: 1; }
        100% { width: 28px; height: 28px; opacity: 0; }
    }

    /* ── Search loading spinner ── */
    .three-body {
        --uib-size: 35px;
        --uib-speed: 0.8s;
        --uib-color: #f43f5e;
        position: relative;
        display: inline-block;
        height: var(--uib-size);
        width: var(--uib-size);
        animation: spin78236 calc(var(--uib-speed) * 2.5) infinite linear;
    }
    .three-body__dot {
        position: absolute;
        height: 100%;
        width: 30%;
    }
    .three-body__dot::after {
        content: '';
        position: absolute;
        height: 0%;
        width: 100%;
        padding-bottom: 100%;
        background-color: var(--uib-color);
        border-radius: 50%;
    }
    .three-body__dot:nth-child(1) {
        bottom: 5%;
        left: 0;
        transform: rotate(60deg);
        transform-origin: 50% 85%;
    }
    .three-body__dot:nth-child(1)::after {
        bottom: 0;
        left: 0;
        animation: wobble1 var(--uib-speed) infinite ease-in-out;
        animation-delay: calc(var(--uib-speed) * -0.3);
    }
    .three-body__dot:nth-child(2) {
        bottom: 5%;
        right: 0;
        transform: rotate(-60deg);
        transform-origin: 50% 85%;
    }
    .three-body__dot:nth-child(2)::after {
        bottom: 0;
        left: 0;
        animation: wobble1 var(--uib-speed) infinite calc(var(--uib-speed) * -0.15) ease-in-out;
    }
    .three-body__dot:nth-child(3) {
        bottom: -5%;
        left: 0;
        transform: translateX(116.666%);
    }
    .three-body__dot:nth-child(3)::after {
        top: 0;
        left: 0;
        animation: wobble2 var(--uib-speed) infinite ease-in-out;
    }
    @keyframes spin78236 {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes wobble1 {
        0%, 100% { transform: translateY(0%) scale(1); opacity: 1; }
        50%      { transform: translateY(-66%) scale(0.65); opacity: 0.8; }
    }
    @keyframes wobble2 {
        0%, 100% { transform: translateY(0%) scale(1); opacity: 1; }
        50%      { transform: translateY(66%) scale(0.65); opacity: 0.8; }
    }

</style>

@push('scripts')
    <script>
        // ── Global Search ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            var SEARCH_URL = @json(route('search'));
            var input = document.getElementById('kt-search-input');
            var popup = document.getElementById('kt-search-popup');
            if (!input || !popup) { console.warn('[Search] input/popup not found'); return; }

            var spState = document.getElementById('kt-sp-state');
            var spList  = document.getElementById('kt-sp-list');

            var timer     = null;
            var lastQ     = '';
            var activeIdx = -1;
            var isOpen    = false;

            var ANIM_DURATION = 200;

            function reposition() {
                var pill = document.getElementById('kt-nav-search');
                if (!pill) return;
                var r = pill.getBoundingClientRect();
                var gap = 8;
                var maxW = window.innerWidth - gap * 2;
                var w = Math.min(300, maxW);
                var l = Math.min(r.left, window.innerWidth - w - gap);
                l = Math.max(gap, l);
                popup.style.top   = (r.bottom + 4) + 'px';
                popup.style.left  = l + 'px';
                popup.style.width = w + 'px';
            }

            function openPopup() {
                if (isOpen) return;
                reposition();
                popup.style.visibility = 'visible';
                popup.style.pointerEvents = 'auto';
                void popup.offsetWidth;
                popup.style.opacity = '1';
                popup.style.transform = 'translateY(0) scale(1)';
                isOpen = true;
            }

            function closePopup() {
                if (!isOpen) return;
                popup.style.opacity = '0';
                popup.style.transform = 'translateY(-8px) scale(0.97)';
                popup.style.pointerEvents = 'none';
                popup.style.visibility = 'hidden';
                isOpen = false;
                activeIdx = -1;
            }

            function resetState() {
                spList.innerHTML = '';
                spState.style.display = '';
                spState.innerHTML =
                    '<svg class="w-6 h-6 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>' +
                    '</svg>' +
                    '<span class="text-sm text-gray-400">Type to search</span>';
            }

            function escHtml(s) {
                return String(s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function markText(text, q) {
                if (!q) return escHtml(text);
                return escHtml(text).replace(
                    new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi'),
                    '<strong class="text-gray-900 font-extrabold">$1</strong>'
                );
            }

            var iconColorMap = {
                'feather-airplay': 'text-blue-500 bg-blue-50',
                'feather-camera': 'text-cyan-500 bg-cyan-50',
                'feather-user': 'text-indigo-500 bg-indigo-50',
                'feather-folder': 'text-sky-500 bg-sky-50',
                'feather-bell': 'text-amber-500 bg-amber-50',
                'feather-user-plus': 'text-emerald-500 bg-emerald-50',
                'feather-tag': 'text-violet-500 bg-violet-50',
                'feather-calendar': 'text-violet-500 bg-violet-50',
                'feather-clock': 'text-cyan-500 bg-cyan-50',
                'feather-dollar-sign': 'text-green-600 bg-green-50',
                'feather-inbox': 'text-emerald-500 bg-emerald-50',
                'feather-file-text': 'text-sky-500 bg-sky-50',
                'feather-bar-chart-2': 'text-sky-500 bg-sky-50',
                'feather-users': 'text-orange-500 bg-orange-50',
                'feather-map': 'text-orange-500 bg-orange-50',
                'feather-truck': 'text-orange-500 bg-orange-50',
                'feather-activity': 'text-teal-500 bg-teal-50',
                'feather-check-circle': 'text-indigo-500 bg-indigo-50',
                'feather-credit-card': 'text-emerald-500 bg-emerald-50',
                'feather-briefcase': 'text-amber-500 bg-amber-50',
                'feather-settings': 'text-slate-500 bg-slate-50',
                'feather-shield': 'text-pink-500 bg-pink-50',
                'feather-monitor': 'text-rose-500 bg-rose-50',
                'feather-gift': 'text-rose-500 bg-rose-50',
                'feather-award': 'text-yellow-500 bg-yellow-50',
            };

            function getIconColors(icon) {
                return iconColorMap[icon] || 'text-gray-500 bg-gray-100';
            }

            function setActive(idx) {
                var links = spList.querySelectorAll('li a');
                links.forEach(function(a, i) {
                    a.classList.toggle('bg-rose-50', i === idx);
                    a.classList.toggle('bg-transparent', i !== idx);
                });
                activeIdx = idx;
                if (links[idx]) links[idx].scrollIntoView({ block: 'nearest' });
            }

            function render(results, q) {
                spList.innerHTML = '';
                activeIdx = -1;

                if (!results.length) {
                    spState.style.display = '';
                    spState.innerHTML =
                        '<svg class="w-8 h-8 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>' +
                        '</svg>' +
                        '<span class="text-sm text-gray-400">No results for <span class="font-semibold text-gray-600">"' + escHtml(q) + '"</span></span>';
                    return;
                }

                spState.style.display = 'none';

                function makeRow(href, left, nameHtml, subHtml) {
                    var li = document.createElement('li');
                    var a  = document.createElement('a');
                    a.href = href;
                    a.className = 'flex items-center gap-3 px-3 py-2.5 no-underline transition-all duration-150 hover:bg-rose-50 group';
                    a.innerHTML =
                        left +
                        '<span class="min-w-0 flex-1">' +
                            '<div class="text-sm font-semibold text-gray-800 leading-tight truncate group-hover:text-rose-700">' + nameHtml + '</div>' +
                            (subHtml ? '<div class="text-xs text-gray-400 mt-0.5 leading-tight truncate">' + subHtml + '</div>' : '') +
                        '</span>';
                    li.appendChild(a);
                    spList.appendChild(li);
                }

                results.forEach(function(item) {
                    if (item.type === 'page') {
                        var colors = getIconColors(item.icon).split(' ');
                        var iconBox = '<span style="width:32px;height:32px;min-width:32px" class="rounded-lg flex items-center justify-center shrink-0 ' + colors.join(' ') + '"><i class="' + escHtml(item.icon) + '" style="font-size:14px"></i></span>';
                        makeRow(escHtml(item.url), iconBox, markText(item.label, q), '');
                    } else {
                        var avatar = '<span style="width:32px;height:32px;min-width:32px" class="rounded-full bg-gradient-to-br from-gray-700 to-gray-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 tracking-tight shadow-sm">' + escHtml(item.initials) + '</span>';
                        makeRow(escHtml(item.url), avatar, markText(item.label, q), item.subtitle ? escHtml(item.subtitle) : '');
                    }
                });
            }

            function doSearch(q) {
                if (q === lastQ) return;
                lastQ = q;
                spState.style.display = '';
                spState.innerHTML = '<div class="three-body"><div class="three-body__dot"></div><div class="three-body__dot"></div><div class="three-body__dot"></div></div>';
                spList.innerHTML = '';
                openPopup();

                fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (d) { render(d.results || [], q); })
                .catch(function (err) {
                    console.error('[Search] fetch error', err);
                    spState.style.display = '';
                    spState.innerHTML = 'Search unavailable';
                });
            }

            /* ── Events ── */
            input.addEventListener('focus', function () { resetState(); openPopup(); });

            input.addEventListener('input', function () {
                var q = this.value.trim();
                clearTimeout(timer);
                if (!q) { lastQ = ''; resetState(); openPopup(); return; }
                timer = setTimeout(function () { doSearch(q); }, 220);
            });

            input.addEventListener('keydown', function (e) {
                var links = spList.querySelectorAll('li a');
                if (e.key === 'Escape') { closePopup(); input.blur(); return; }
                if (!links.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); setActive(Math.min(activeIdx + 1, links.length - 1)); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(Math.max(activeIdx - 1, 0)); }
                else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); links[activeIdx].click(); }
            });

            document.addEventListener('mousedown', function (e) {
                if (!popup.contains(e.target) && !document.getElementById('kt-nav-search').contains(e.target)) {
                    closePopup();
                }
            });

            window.addEventListener('resize', function () { if (isOpen) reposition(); });

        }); // end DOMContentLoaded

        // Global polling interval ID
        let notificationPollingInterval = null;

        // Track unread count for notification sound
        let previousUnreadCount = 0;

        // Preload notification sound
        const notifSound = new Audio(@json(asset('sounds/notif.mp3')));
        notifSound.volume = 0.5;
        notifSound.preload = 'auto';

        const notificationReadUrlTemplate = @json(route('notifications.read', ['notification' => '__ID__']));

        // ── Notification functionality ────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const isNotificationsPage = window.location.pathname.includes('/notifications');

            attachMarkAllAsReadListener();
            attachNotificationClickListeners();

            // Auto-refresh every 30 seconds (not on the notifications page itself)
            if (!isNotificationsPage) {
                startNotificationPolling();
            }

            // Initialise blip + sound tracking immediately
            updateNotificationCount();

            // Refresh list when dropdown opens
            const notifBtn = document.getElementById('notification-btn');
            if (notifBtn) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleDropdown('notification-dropdown', notifBtn);
                    refreshNotificationList();
                });
            }
        });

        // ── Dropdown helpers ───────────────────────────────────────────────
        function toggleDropdown(id, btn) {
            var menu = document.getElementById(id);
            if (!menu) return;
            var isOpen = !menu.classList.contains('dropdown-closed');
            document.querySelectorAll('[data-dropdown-menu]').forEach(function(el) {
                el.classList.add('dropdown-closed');
            });
            if (!isOpen) {
                menu.classList.remove('dropdown-closed');
            }
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
            document.querySelectorAll('[data-dropdown-menu]:not(.dropdown-closed)').forEach(function(menu) {
                var parent = menu.closest('[data-dropdown]');
                if (parent && !parent.contains(e.target)) {
                    menu.classList.add('dropdown-closed');
                }
            });
        });

        // View‑switch dropdown toggle
        var viewSwitchBtn = document.getElementById('view-switch-btn');
        if (viewSwitchBtn) {
            viewSwitchBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown('view-switch-dropdown', viewSwitchBtn);
            });
        }

        // User dropdown toggle
        var userBtn = document.getElementById('user-dropdown-btn');
        if (userBtn) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown('user-dropdown', userBtn);
            });
        }

        // Mobile menu toggle
        var mobBtn = document.getElementById('mobile-menu-btn');
        if (mobBtn) {
            mobBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.body.classList.toggle('sidebar-open');
            });
        }

        // Close mobile sidebar on backdrop click / Escape key
        document.addEventListener('click', function(e) {
            if (document.body.classList.contains('sidebar-open')) {
                var sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.contains(e.target) && e.target !== mobBtn && !mobBtn?.contains(e.target)) {
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // ── Typewriter effect (single line, no loop) ──────────────────────
        (function() {
            var el = document.getElementById('typewriter');
            if (!el) return;
            var text = @json($greeting);
            var j = 0;
            (function type() {
                el.textContent = text.substring(0, j + 1);
                j++;
                if (j < text.length) { setTimeout(type, text.length > 40 ? 30 : 50); }
            })();
        })();

        function attachNotificationClickListeners() {
            document.querySelectorAll('#notification-list > div').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const notifId  = this.dataset.notifId;
                    const actionUrl = this.dataset.actionUrl;

                    fetch(notificationReadUrlTemplate.replace('__ID__', notifId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(() => {
                        var menu = document.getElementById('notification-dropdown');
                        if (menu) { menu.classList.add('dropdown-closed'); }
                        if (actionUrl && actionUrl !== 'null') {
                            window.location.href = actionUrl;
                        } else {
                            updateNotificationCount();
                            refreshNotificationList();
                        }
                    })
                    .catch(err => console.error('Error marking notification as read:', err));
                });
            });
        }

        // Mark all as read
        function attachMarkAllAsReadListener() {
            const btn = document.getElementById('mark-all-read');
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fetch(@json(route('notifications.mark-all-as-read')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(() => refreshNotificationList())
                .catch(err => console.error('Error marking all as read:', err));
            });
        }

        // Update the bell blip + sound on new notifications
        function updateNotificationCount() {
            fetch(@json(route('notifications.count')), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const count = data.unread_count || 0;
                let   blip  = document.querySelector('#notification-btn .notif-blip');

                // Play notification sound if unread count increased
                if (count > previousUnreadCount) {
                    notifSound.currentTime = 0;
                    notifSound.play().catch(function(){});
                }
                previousUnreadCount = count;

                const notifBtn = document.getElementById('notification-btn');
                if (blip) blip.classList.toggle('hidden', count === 0);
                if (notifBtn) notifBtn.classList.toggle('notif-has-unread', count > 0);
                const markAllBtn = document.getElementById('mark-all-read');
                if (markAllBtn) markAllBtn.classList.toggle('hidden', count === 0);
            })
            .catch(err => console.error('Error updating count:', err));
        }

        // Refresh the notification list in the dropdown (no delete button, max 3)
        function refreshNotificationList() {
            fetch(@json(route('notifications.index')) + '?view=unread&limit=3', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const payload       = data && data.status === 'success' ? data.data : null;
                const notifications = payload && Array.isArray(payload.data) ? payload.data : [];
                const list          = document.getElementById('notification-list');
                if (!list) return;

                if (notifications.length > 0) {
                    list.innerHTML = notifications.slice(0, 3).map(n => {
                        const created = new Date(n.created_at);
                        const timeText = created.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true })
                        const actionUrl = n.action_url || '';
                        return `
                            <div class="flex items-start gap-2.5 px-3.5 py-2.5 rounded-lg cursor-pointer transition-colors duration-100 hover:bg-red-50 ${n.read_at ? '' : 'bg-gray-50/80 font-medium'}" data-notif-id="${n.id}" data-action-url="${actionUrl}">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-900 mb-1">${n.title}</div>
                                    <p class="text-sm text-gray-600 mb-1 leading-snug">${n.message}</p>
                                    <small class="text-xs text-gray-400">${timeText}</small>
                                </div>
                            </div>`;
                    }).join('');
                } else {
                    list.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <i class="feather-bell-off" style="font-size:40px;opacity:0.4"></i>
                            <p class="text-sm mt-2">No new notifications</p>
                        </div>`;
                }

                // Re-attach click listeners to freshly rendered items
                attachNotificationClickListeners();
                updateNotificationCount();
            })
            .catch(err => console.error('Error refreshing notifications:', err));
        }

        // Start polling
        function startNotificationPolling() {
            if (notificationPollingInterval) clearInterval(notificationPollingInterval);
            notificationPollingInterval = setInterval(refreshNotificationList, 10000);
        }

        // Stop polling when page unloads
        window.addEventListener('beforeunload', function() {
            if (notificationPollingInterval) {
                clearInterval(notificationPollingInterval);
            }
        });

        // Fullscreen — swap icon only, no show/hide logic
        const ktFsBtn = document.getElementById('kt-fullscreen-btn');
        if (ktFsBtn) {
            ktFsBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen().catch(() => {});
                }
            });
            document.addEventListener('fullscreenchange', function() {
                const icon = ktFsBtn.querySelector('i');
                if (!icon) return;
                icon.className = document.fullscreenElement ?
                    'feather-minimize fs-17' :
                    'feather-maximize fs-17';
            });
        }
    </script>

@endpush
