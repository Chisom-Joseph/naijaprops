@extends('layouts.app')

@section('title', 'Sign In – NaijaProps')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-800 text-white font-extrabold text-xl flex items-center justify-center mx-auto mb-3 shadow-sm">
                    NP
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Welcome back to NaijaProps</h1>
                <p class="text-sm text-slate-500 mt-1">Sign in to access your properties, enquiries, and recommendations</p>
            </div>

            <form
                method="POST"
                action="{{ route('login.store') }}"
                x-data="{ submitting: false }"
                @submit="submitting = true"
                data-loading-overlay="true"
                data-loading-title="Signing in to NaijaProps..."
                data-loading-subtitle="Verifying your credentials and loading your account..."
                class="space-y-5"
            >
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                    >
                    @error('email')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                    >
                    @error('password')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="submitting"
                    data-loading-text="Signing in..."
                    class="w-full bg-blue-800 hover:bg-blue-700 disabled:opacity-80 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition text-sm inline-flex items-center justify-center gap-2"
                >
                    <svg x-show="submitting" x-cloak class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Signing in...' : 'Sign In to Account'">Sign In to Account</span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Don't have a NaijaProps account?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-800 hover:text-blue-700">
                        Create an account
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
