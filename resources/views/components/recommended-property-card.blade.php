@props([
    'item',
    'savedPropertyIds' => [],
    'showBreakdown' => false,
])

@php
    /** @var \App\Models\Property $property */
    $property = $item['property'];
    $rank = $item['rank'] ?? 1;
    $score = $item['score'] ?? 80;
    $matchLabel = $item['match_label'] ?? 'Recommended';
    $reasons = $item['reasons'] ?? [];
    $whySuggested = $item['why_suggested'] ?? '';
    $breakdown = $item['breakdown'] ?? [];
    $isSaved = in_array($property->id, $savedPropertyIds, true);
@endphp

<div data-animate="fade-up" class="np-card-hover bg-white dark:bg-slate-800/95 rounded-2xl border border-blue-200/80 dark:border-slate-700 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col group relative">
    <!-- Top Image + Rank & Match Score Badges -->
    <a href="{{ route('properties.show', $property) }}" class="relative h-52 bg-slate-200 dark:bg-slate-800 block overflow-hidden">
        <img
            src="{{ $property->primary_image_url }}"
            alt="{{ $property->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out"
        >

        <!-- Rank Badge & Transaction Type -->
        <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wider shadow-sm bg-amber-400 text-slate-950 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-950 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                #{{ $rank }} Pick
            </span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm
                {{ $property->transaction_type === 'sale' ? 'bg-blue-800 text-white' : 'bg-emerald-700 text-white' }}">
                For {{ ucfirst($property->transaction_type) }}
            </span>
        </div>

        <!-- Match Score Badge -->
        <div class="absolute top-3 right-3">
            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold shadow-sm backdrop-blur-sm
                @if($score >= 85) bg-emerald-600/95 text-white
                @elseif($score >= 70) bg-blue-800/95 text-white
                @else bg-slate-900/90 text-white @endif">
                {{ $score }}% Match
            </span>
        </div>

        <!-- Property Type Pill at Bottom Left -->
        <div class="absolute bottom-3 left-3">
            <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white/95 dark:bg-slate-900/90 text-slate-800 dark:text-slate-100 shadow-sm">
                {{ $property->property_type_label }}
            </span>
        </div>
    </a>

    <!-- Card Body -->
    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            <div class="flex items-baseline justify-between gap-2">
                <p class="text-xl font-extrabold text-blue-900 dark:text-blue-300">
                    {{ $property->formatted_price }}
                    @if($property->transaction_type === 'rent')
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ year</span>
                    @endif
                </p>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full
                    @if($score >= 85) bg-emerald-50 text-emerald-800 border border-emerald-200
                    @elseif($score >= 70) bg-blue-50 text-blue-800 border border-blue-200
                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                    {{ $matchLabel }}
                </span>
            </div>

            <a href="{{ route('properties.show', $property) }}" class="block mt-1">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-blue-800 dark:group-hover:text-blue-400 transition line-clamp-1">
                    {{ $property->title }}
                </h3>
            </a>

            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-blue-700 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $property->location }}
            </p>

            <!-- "Why suggested" Explanation Box -->
            <div class="mt-3.5 p-3 rounded-xl bg-blue-50/80 dark:bg-slate-900/90 border border-blue-200/80 dark:border-blue-800/60">
                <div class="flex items-center justify-between gap-1 mb-1.5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-blue-900 dark:text-blue-300 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-700 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Why suggested
                    </p>
                    <span class="text-[10px] font-semibold text-blue-700 dark:text-blue-400">Score: {{ $score }}/100</span>
                </div>

                @if(!empty($reasons))
                    <ul class="space-y-1 text-xs text-blue-950 dark:text-slate-200">
                        @foreach($reasons as $reason)
                            <li class="flex items-start gap-1.5 leading-snug">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ $reason }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-blue-900 dark:text-slate-200 leading-snug">{{ $whySuggested }}</p>
                @endif

                @if($showBreakdown && !empty($breakdown))
                    <div class="mt-2.5 pt-2 border-t border-blue-200/60 dark:border-slate-700 grid grid-cols-4 gap-1 text-[10px] text-blue-800 dark:text-blue-300 text-center">
                        <div class="bg-white/80 dark:bg-slate-800 rounded px-1 py-0.5">
                            <span class="block font-bold">{{ $breakdown['location'] ?? 0 }}/35</span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400">Location</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-800 rounded px-1 py-0.5">
                            <span class="block font-bold">{{ $breakdown['property_type'] ?? 0 }}/25</span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400">Type</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-800 rounded px-1 py-0.5">
                            <span class="block font-bold">{{ $breakdown['price_range'] ?? 0 }}/25</span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400">Price</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-800 rounded px-1 py-0.5">
                            <span class="block font-bold">{{ $breakdown['bedrooms'] ?? 0 }}/15</span>
                            <span class="text-[9px] text-slate-500 dark:text-slate-400">Beds</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card Footer: Specs + Save/Details Actions -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
            <div class="flex items-center gap-3">
                @if(!is_null($property->bedrooms))
                    <span><strong>{{ $property->bedrooms }}</strong> Beds</span>
                @endif
                @if(!is_null($property->bathrooms))
                    <span><strong>{{ $property->bathrooms }}</strong> Baths</span>
                @endif
                @if(is_null($property->bedrooms) && is_null($property->bathrooms))
                    <span>Residential Plot</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <form method="POST" action="{{ route('saved-properties.toggle', $property) }}">
                        @csrf
                        <button
                            type="submit"
                            title="{{ $isSaved ? 'Remove from Saved Properties' : 'Save Property' }}"
                            class="np-heart-btn p-1.5 rounded-lg border transition {{ $isSaved ? 'bg-red-50 border-red-200 text-red-600' : 'bg-slate-50 border-slate-200 text-slate-500 hover:text-red-600' }}"
                        >
                            <svg class="w-3.5 h-3.5 {{ $isSaved ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </form>
                @endauth
                <a href="{{ route('properties.show', $property) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-800 hover:text-blue-700 group-hover:translate-x-0.5 transition">
                    <span>Details</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
