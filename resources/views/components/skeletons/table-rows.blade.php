@props([
    'rows' => 5,
    'showThumbnail' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden']) }} aria-hidden="true">
    <!-- Skeleton Table Header -->
    <div class="bg-slate-50 dark:bg-slate-900/70 border-b border-slate-200 dark:border-slate-700 px-5 py-3.5 grid grid-cols-12 gap-4">
        <div class="col-span-5 h-3.5 w-28 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
        <div class="col-span-2 h-3.5 w-20 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
        <div class="col-span-2 h-3.5 w-20 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
        <div class="col-span-3 h-3.5 w-24 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer ml-auto"></div>
    </div>

    <!-- Skeleton Table Body Rows -->
    <div class="divide-y divide-slate-100 dark:divide-slate-700">
        @for($i = 0; $i < $rows; $i++)
            <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                <!-- Primary Column -->
                <div class="sm:col-span-5 flex items-center space-x-3.5">
                    @if($showThumbnail)
                        <div class="w-16 h-12 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                    @endif
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="h-4 w-3/4 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-3 w-1/2 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                    </div>
                </div>

                <!-- Secondary Info Column -->
                <div class="sm:col-span-2 space-y-1.5">
                    <div class="h-3.5 w-24 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    <div class="h-3 w-16 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                </div>

                <!-- Status / Badge Column -->
                <div class="sm:col-span-2">
                    <div class="h-6 w-24 rounded-full bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                </div>

                <!-- Actions Column -->
                <div class="sm:col-span-3 flex items-center justify-end gap-2">
                    <div class="h-7 w-16 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    <div class="h-7 w-14 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    <div class="h-7 w-16 rounded-lg bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                </div>
            </div>
        @endfor
    </div>
</div>
