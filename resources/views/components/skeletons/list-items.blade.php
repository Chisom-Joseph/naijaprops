@props([
    'items' => 4,
    'variant' => 'listing', // 'listing' | 'enquiry' | 'compact'
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }} aria-hidden="true">
    @for($i = 0; $i < $items; $i++)
        @if($variant === 'enquiry')
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                                <div class="space-y-1.5">
                                    <div class="h-4 w-36 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                                    <div class="h-3 w-48 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                                </div>
                            </div>
                            <div class="h-6 w-24 rounded-full bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700/70 space-y-2">
                            <div class="h-3 w-24 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-3.5 w-full rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-3.5 w-4/5 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <div class="flex gap-2">
                                <div class="h-7 w-28 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                                <div class="h-7 w-24 rounded-lg bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="h-7 w-24 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                                <div class="h-7 w-16 rounded-lg bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-80 shrink-0 bg-slate-50 dark:bg-slate-900/60 rounded-xl p-3.5 border border-slate-100 dark:border-slate-700 flex items-start space-x-3.5">
                        <div class="w-20 h-20 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-16 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-3.5 w-full rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-3 w-2/3 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            <div class="h-4 w-24 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-start space-x-4 flex-1">
                    <div class="w-28 h-20 rounded-xl bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer shrink-0"></div>
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="h-4 w-16 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                            <div class="h-4 w-20 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            <div class="h-4 w-16 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        </div>
                        <div class="h-5 w-3/5 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-3.5 w-2/5 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                        <div class="flex items-center gap-3 pt-1">
                            <div class="h-3 w-16 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            <div class="h-3 w-20 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                            <div class="h-3 w-16 rounded bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                        </div>
                    </div>
                </div>
                <div class="flex sm:flex-row lg:flex-col items-center lg:items-end justify-between gap-3 shrink-0">
                    <div class="h-6 w-32 rounded bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-24 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                        <div class="h-8 w-14 rounded-lg bg-slate-100 dark:bg-slate-700/70 np-skeleton-shimmer"></div>
                        <div class="h-8 w-14 rounded-lg bg-slate-200 dark:bg-slate-700 np-skeleton-shimmer"></div>
                    </div>
                </div>
            </div>
        @endif
    @endfor
</div>
