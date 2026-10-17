<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bahar Trading')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-slate-100 min-h-screen text-slate-800 antialiased">

    {{-- ========================= NAVBAR ========================= --}}
    <nav x-data="{ open: false, userMenu: false }"
        class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white shadow-lg sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="transition-transform duration-300 group-hover:rotate-6 group-hover:scale-110">
                        <svg width="34" height="24" viewBox="0 0 56 40">
                            <polygon points="0,0 16,0 11,15 0,15" fill="#A00D25" />
                            <polygon points="18,0 34,0 29,15 13,15" fill="#E31E24" />
                            <polygon points="0,17 11,17 8,30 0,30" fill="#E31E24" />
                            <polygon points="13,17 29,17 24,30 8,30" fill="#A00D25" />
                            <polygon points="31,17 47,17 42,30 26,30" fill="#E31E24" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div
                            class="font-black text-white tracking-tight group-hover:text-red-400 transition-colors duration-300">
                            BAHAR TRADING
                        </div>
                        <div class="text-[9px] text-slate-400 tracking-[0.25em] hidden sm:block">
                            QUALITY · TRUST · COMMITMENT
                        </div>
                    </div>
                </a>

                {{-- Desktop nav --}}
                @php $route = request()->route()->getName() ?? ''; @endphp
                <div class="hidden lg:flex items-center gap-1">
                    @php
                        $navItems = [
                            [
                                'dashboard',
                                'Dashboard',
                                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            ],
                            [
                                'customers.index',
                                'Customers',
                                'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z',
                            ],
                            [
                                'challans.index',
                                'Challans',
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            ],
                            [
                                'invoices.index',
                                'Invoices',
                                'M9 8h6m-5 4h4m-4 4h4m-9 4V6a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2z',
                            ],
                            [
                                'receipts.index',
                                'Receipts',
                                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v-2m0 5a9 9 0 110-18 9 9 0 010 18z',
                            ],
                        ];
                    @endphp
                    @foreach ($navItems as [$routeName, $label, $icon])
                        @php $active = str_starts_with($route, explode('.', $routeName)[0]); @endphp
                        <a href="{{ route($routeName) }}"
                            class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 group
                              {{ $active ? 'bg-red-600/90 text-white shadow-md' : 'text-slate-200 hover:text-white' }}">
                            <span class="relative z-10 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $icon }}" />
                                </svg>
                                {{ $label }}
                            </span>
                            @if (!$active)
                                <span
                                    class="absolute inset-0 rounded-lg bg-red-600/0 group-hover:bg-red-600/80 transition-all duration-300"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- User menu --}}
                <div class="hidden lg:block relative">
                    <button @click="userMenu = !userMenu"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-slate-700/60 transition">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm">{{ auth()->user()->name ?? 'User' }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="userMenu ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMenu" @click.away="userMenu = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-200 py-1 z-50">
                        <div class="px-4 py-2 border-b">
                            <p class="text-xs text-slate-500">Signed in as</p>
                            <p class="text-sm font-medium text-slate-800 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Dashboard</a>
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile toggle --}}
                <button @click="open = !open" class="lg:hidden p-2 rounded-lg hover:bg-slate-700/60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition x-cloak class="lg:hidden border-t border-slate-700 bg-slate-900">
            <div class="px-4 py-3 space-y-1">
                @foreach ($navItems as [$routeName, $label, $icon])
                    @php $active = str_starts_with($route, explode('.', $routeName)[0]); @endphp
                    <a href="{{ route($routeName) }}"
                        class="block px-3 py-2 rounded-md text-sm {{ $active ? 'bg-red-600 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <a href="{{ route('profile.edit') }}"
                    class="block px-3 py-2 rounded-md text-sm text-slate-200 hover:bg-slate-800">
                    Profile
                </a>
                <div class="border-t border-slate-700 pt-3 mt-3">
                    <div class="px-3 py-2 text-xs text-slate-400">Signed in as</div>
                    <div class="px-3 text-sm text-white font-medium mb-2">{{ auth()->user()->name ?? '' }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2 rounded-md text-sm text-red-400 hover:bg-red-500/20">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================= MAIN ========================= --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @if (session('success'))
            <div
                class="bg-green-100 border-l-4 border-green-600 text-green-800 px-4 py-3 rounded-lg mb-5 no-print shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-100 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded-lg mb-5 no-print shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('mouseover', e => {
            const el = e.target.closest('[data-tip]');
            if (!el) return;
            const tip = document.createElement('div');
            tip.textContent = el.dataset.tip;
            tip.className =
                'fixed z-[60] bg-slate-900 text-white text-xs px-2 py-1 rounded shadow-lg pointer-events-none whitespace-nowrap';
            tip.id = 'active-tip';
            const r = el.getBoundingClientRect();
            tip.style.top = (r.top - 30) + 'px';
            tip.style.left = (r.left + r.width / 2) + 'px';
            tip.style.transform = 'translateX(-50%)';
            document.body.appendChild(tip);
        });
        document.addEventListener('mouseout', e => {
            if (e.target.closest('[data-tip]')) {
                document.getElementById('active-tip')?.remove();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
