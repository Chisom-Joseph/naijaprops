@extends('layouts.app')

@section('title', 'Create Account – NaijaProps')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-800 text-white font-extrabold text-xl flex items-center justify-center mx-auto mb-3 shadow-sm">
                    NP
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Create your NaijaProps Account</h1>
                <p class="text-sm text-slate-500 mt-1">Join Nigeria's trusted property listing and trading platform</p>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5" x-data="{ selectedRole: '{{ old('role', 'buyer') }}' }">
                @csrf

                <!-- Account Role Selection -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        I am registering as a
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Buyer / Tenant -->
                        <label
                            @click="selectedRole = 'buyer'"
                            :class="selectedRole === 'buyer' ? 'border-blue-700 bg-blue-50/70 ring-2 ring-blue-700/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                            class="cursor-pointer rounded-xl border p-3 text-center transition flex flex-col items-center justify-center"
                        >
                            <input type="radio" name="role" value="buyer" class="sr-only" x-model="selectedRole">
                            <span class="text-sm font-bold text-slate-900">Buyer / Tenant</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Search &amp; enquire</span>
                        </label>

                        <!-- Property Owner -->
                        <label
                            @click="selectedRole = 'owner'"
                            :class="selectedRole === 'owner' ? 'border-blue-700 bg-blue-50/70 ring-2 ring-blue-700/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                            class="cursor-pointer rounded-xl border p-3 text-center transition flex flex-col items-center justify-center"
                        >
                            <input type="radio" name="role" value="owner" class="sr-only" x-model="selectedRole">
                            <span class="text-sm font-bold text-slate-900">Property Owner</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">List own properties</span>
                        </label>

                        <!-- Real Estate Agent -->
                        <label
                            @click="selectedRole = 'agent'"
                            :class="selectedRole === 'agent' ? 'border-blue-700 bg-blue-50/70 ring-2 ring-blue-700/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                            class="cursor-pointer rounded-xl border p-3 text-center transition flex flex-col items-center justify-center"
                        >
                            <input type="radio" name="role" value="agent" class="sr-only" x-model="selectedRole">
                            <span class="text-sm font-bold text-slate-900">Agent</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Manage client listings</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Full Name
                    </label>
                    <input
                        id="full_name"
                        name="full_name"
                        type="text"
                        value="{{ old('full_name') }}"
                        required
                        autofocus
                        placeholder="e.g. Chukwudi Okafor"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                    >
                    @error('full_name')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email & Phone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                            placeholder="you@example.com"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                        >
                        @error('email')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Phone Number (Optional)
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone') }}"
                            placeholder="+234 803 000 0000"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                        >
                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                        >
                        @error('password')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Repeat password"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 focus:outline-none transition"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-800 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition text-sm"
                >
                    Create NaijaProps Account
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-semibold text-blue-800 hover:text-blue-700">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
