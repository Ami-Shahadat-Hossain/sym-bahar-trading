<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bahar Trading') }}</title>

    <!-- Fonts (same as your app) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen text-slate-800 antialiased flex flex-col">

    @php
        $isAuth = auth()->check();
        // Safe route resolver: auth users go to the real page, guests go to login
        $linkTo = fn($name) => $isAuth ? route($name) : (Route::has('login') ? route('login') : url('/'));
    @endphp

    {{-- ========================= NAVBAR (mirrors layouts.app) ========================= --}}
    <nav class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 group">
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

                {{-- Auth actions --}}
                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-md transition-all hover:-translate-y-0.5">
                            Dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 rounded-lg text-slate-200 hover:text-white hover:bg-slate-700/60 text-sm font-medium transition">
                                Log in
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-md transition-all hover:-translate-y-0.5">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================= HERO ========================= --}}
    <section class="flex-1">
        <div class="max-w-7xl mx-auto px-4 pt-16 pb-10 text-center">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold mb-5">
                <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                Business Operations Platform
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight mb-5">
                Welcome to <span class="text-red-600">Bahar Trading</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto mb-8">
                A complete operations hub to manage customers, delivery challans, invoices and money
                receipts — built for speed, clarity and trust.
            </p>

            <div class="flex flex-wrap justify-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Go to Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            Log in to your account
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-slate-800 font-semibold shadow-md border border-slate-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            Create an account
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- ========================= FEATURE CARDS (same as dashboard stats) ========================= --}}
        <div class="max-w-7xl mx-auto px-4 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                <a href="{{ $linkTo('customers.index') }}"
                    class="block bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-xs uppercase tracking-wider">Customers</p>
                            <p class="text-lg font-bold mt-2">Manage Clients</p>
                        </div>
                        <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-xs mt-3 text-blue-100">Client records &amp; contacts</p>
                </a>

                <a href="{{ $linkTo('challans.index') }}"
                    class="block bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-xs uppercase tracking-wider">Challans</p>
                            <p class="text-lg font-bold mt-2">Track Deliveries</p>
                        </div>
                        <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-xs mt-3 text-amber-100">Issue &amp; monitor challans</p>
                </a>

                <a href="{{ $linkTo('invoices.index') }}"
                    class="block bg-gradient-to-br from-emerald-500 to-green-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-xs uppercase tracking-wider">Invoices</p>
                            <p class="text-lg font-bold mt-2">Bill Clients</p>
                        </div>
                        <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 8h6m-5 4h4m-4 4h4m-9 4V6a2 2 0 012-2h6l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="text-xs mt-3 text-emerald-100">Create &amp; track invoices</p>
                </a>

                <a href="{{ $linkTo('receipts.index') }}"
                    class="block bg-gradient-to-br from-red-500 to-red-700 text-white rounded-xl shadow-md p-5 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-xs uppercase tracking-wider">Receipts</p>
                            <p class="text-lg font-bold mt-2">Record Payments</p>
                        </div>
                        <svg class="w-12 h-12 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v-2m0 5a9 9 0 110-18 9 9 0 010 18z" />
                        </svg>
                    </div>
                    <p class="text-xs mt-3 text-red-100">Money receipts &amp; payments</p>
                </a>

            </div>
        </div>
    </section>

    {{-- ========================= FOOTER ========================= --}}
    <footer class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <svg width="28" height="20" viewBox="0 0 56 40">
                    <polygon points="0,0 16,0 11,15 0,15" fill="#A00D25" />
                    <polygon points="18,0 34,0 29,15 13,15" fill="#E31E24" />
                    <polygon points="0,17 11,17 8,30 0,30" fill="#E31E24" />
                    <polygon points="13,17 29,17 24,30 8,30" fill="#A00D25" />
                    <polygon points="31,17 47,17 42,30 26,30" fill="#E31E24" />
                </svg>
                <span>&copy; {{ date('Y') }} Bahar Trading. All rights reserved.</span>
            </div>
            <div class="text-[10px] tracking-[0.25em] text-slate-500">QUALITY · TRUST · COMMITMENT</div>
        </div>
    </footer>

</body>

</html>
