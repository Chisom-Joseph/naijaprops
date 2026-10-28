@props([
    'count' => 6,
    'columns' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
])

<div {{ $attributes->merge(['class' => "grid {$columns} gap-6"]) }} aria-hidden="true">
    @for($i = 0; $i < $count; $i++)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden flex flex-col">
            <!-- Image Skeleton Area -->
            <div class="relative h-52 bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer">
                <div class="absolute top-3 left-3 flex items-center gap-2">
                    <span class="h-6 w-20 rounded-lg bg-white/60 dark:bg-slate-600/60"></span>
                    <span class="h-6 w-24 rounded-lg bg-white/60 dark:bg-slate-600/60"></span>
                </div>
                <div class="absolute top-3 right-3">
                    <span class="h-6 w-16 rounded-lg bg-white/60 dark:bg-slate-600/60 block"></span>
                </div>
            </div>

            <!-- Card Body Skeleton -->
            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <!-- Price Bar -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="h-6 w-36 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-5 w-16 rounded-full bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                    </div>

                    <!-- Title Bar -->
                    <div class="h-5 w-4/5 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>

                    <!-- Location Bar -->
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                        <div class="h-3.5 w-1/2 rounded-md bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    </div>

                    <!-- Description / Why Suggested Box Skeleton -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/70 space-y-2">
                        <div class="h-3 w-24 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-3 w-full rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-3 w-3/4 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    </div>
                </div>

                <!-- Card Footer Specs & Action Skeleton -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-3.5 w-14 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-3.5 w-14 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        </div>
                        <div class="h-3.5 w-20 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-9 flex-1 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-9 w-9 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                    </div>
                </div>
            </div>
        </div>
    @endfor
</div>
