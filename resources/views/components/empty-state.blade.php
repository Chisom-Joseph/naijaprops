@props([
    'icon' => 'property', // property | search | heart | chat | shield | users | sparkles
    'tone' => 'blue',     // blue | emerald | amber | red | slate
    'badge' => null,
    'title' => 'Nothing to display yet',
    'description' => '',
    'compact' => false,
    'showCityShortcuts' => false,
])

@php
    $toneMap = [
        'blue' => [
            'outerRing' => 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-100 dark:border-blue-900/60',
            'iconBox' => 'bg-gradient-to-br from-blue-800 to-blue-600 text-white shadow-md shadow-blue-800/20',
            'badge' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
        ],
        'emerald' => [
            'outerRing' => 'bg-emerald-50/80 dark:bg-emerald-950/40 border-emerald-100 dark:border-emerald-900/60',
            'iconBox' => 'bg-gradient-to-br from-emerald-700 to-emerald-500 text-white shadow-md shadow-emerald-700/20',
            'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        ],
        'amber' => [
            'outerRing' => 'bg-amber-50/80 dark:bg-amber-950/40 border-amber-100 dark:border-amber-900/60',
            'iconBox' => 'bg-gradient-to-br from-amber-600 to-amber-500 text-white shadow-md shadow-amber-600/20',
            'badge' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        ],
        'red' => [
            'outerRing' => 'bg-red-50/80 dark:bg-red-950/40 border-red-100 dark:border-red-900/60',
            'iconBox' => 'bg-gradient-to-br from-red-700 to-red-500 text-white shadow-md shadow-red-700/20',
            'badge' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-800',
        ],
        'slate' => [
            'outerRing' => 'bg-slate-100/80 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700',
            'iconBox' => 'bg-gradient-to-br from-slate-800 to-slate-600 text-white shadow-md shadow-slate-800/20',
            'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        ],
    ];

    $colors = $toneMap[$tone] ?? $toneMap['blue'];
@endphp

<div
    data-animate="fade-up"
    {{ $attributes->merge([
        'class' => ($compact ? 'p-7 sm:p-8' : 'p-10 sm:p-14') . ' relative overflow-hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 text-center shadow-xs'
    ]) }}
>
    <!-- Subtle Decorative Background Glow -->
    <div class="pointer-events-none absolute -top-16 left-1/2 -translate-x-1/2 w-72 h-36 bg-gradient-to-b from-blue-500/5 to-transparent rounded-full blur-2xl" aria-hidden="true"></div>

    <!-- Layered Icon Emblem -->
    <div class="relative mx-auto {{ $compact ? 'w-14 h-14 mb-3.5' : 'w-20 h-20 mb-5' }} rounded-3xl border {{ $colors['outerRing'] }} flex items-center justify-center">
        <div class="{{ $compact ? 'w-10 h-10 rounded-xl' : 'w-13 h-13 rounded-2xl' }} {{ $colors['iconBox'] }} flex items-center justify-center transition-transform duration-300 hover:scale-105">
            @switch($icon)
                @case('search')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @break

                @case('heart')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @break

                @case('chat')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    @break

                @case('shield')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    @break

                @case('users')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @break

                @case('sparkles')
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    @break

                @default
                    <svg class="{{ $compact ? 'w-5 h-5' : 'w-6 h-6' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
            @endswitch
        </div>
    </div>

    @if($badge)
        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border {{ $colors['badge'] }} mb-2.5">
            {{ $badge }}
        </span>
    @endif

    <h3 class="{{ $compact ? 'text-sm sm:text-base' : 'text-base sm:text-lg' }} font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
        {{ $title }}
    </h3>

    @if($description)
        <p class="{{ $compact ? 'text-xs mt-1 max-w-sm' : 'text-sm mt-1.5 max-w-md' }} text-slate-500 dark:text-slate-400 mx-auto leading-relaxed">
            {{ $description }}
        </p>
    @endif

    @if($slot->isNotEmpty())
        <div class="{{ $compact ? 'mt-4' : 'mt-6' }} flex flex-wrap items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif

    @if($showCityShortcuts)
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 max-w-md mx-auto">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2.5">
                Explore Popular Nigerian Markets
            </p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach(['Lagos', 'Abuja', 'Port Harcourt'] as $city)
                    <a
                        href="{{ route('properties.index', ['location' => $city]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-50/60 transition np-btn-interactive"
                    >
                        <svg class="w-3.5 h-3.5 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $city }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
