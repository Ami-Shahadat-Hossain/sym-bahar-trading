@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Page heading --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">
                Profile Settings
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Manage your account information, password, and account settings.
            </p>
        </div>

        {{-- Profile information --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update password --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Delete account --}}
        <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
@endsection
