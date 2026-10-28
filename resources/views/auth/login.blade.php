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

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
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
                    class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition text-sm"
                >
                    Sign In to Account
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

        <!-- Quick Demo Credentials Helper -->
        <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 text-xs text-slate-700">
            <p class="font-bold text-blue-900 mb-2 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Demo Accounts (Password: <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 font-mono text-blue-900">password</code>)
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                <button type="button" onclick="document.getElementById('email').value='admin@naijaprops.ng'; document.getElementById('password').value='password';" class="text-left bg-white hover:bg-blue-100/60 p-2 rounded-lg border border-blue-100 transition">
                    <span class="font-semibold text-purple-800 block">Administrator</span>
                    <span class="text-slate-500">admin@naijaprops.ng</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='adebayo.ogunlesi@naijaprops.ng'; document.getElementById('password').value='password';" class="text-left bg-white hover:bg-blue-100/60 p-2 rounded-lg border border-blue-100 transition">
                    <span class="font-semibold text-blue-800 block">Property Owner</span>
                    <span class="text-slate-500">adebayo.ogunlesi@naijaprops.ng</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='zainab.bello@naijaprops.ng'; document.getElementById('password').value='password';" class="text-left bg-white hover:bg-blue-100/60 p-2 rounded-lg border border-blue-100 transition">
                    <span class="font-semibold text-indigo-800 block">Real Estate Agent</span>
                    <span class="text-slate-500">zainab.bello@naijaprops.ng</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='amaka.eze@naijaprops.ng'; document.getElementById('password').value='password';" class="text-left bg-white hover:bg-blue-100/60 p-2 rounded-lg border border-blue-100 transition">
                    <span class="font-semibold text-emerald-800 block">Buyer / Tenant</span>
                    <span class="text-slate-500">amaka.eze@naijaprops.ng</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
