<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use App\Policies\EnquiryPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\ReportPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Property::class, PropertyPolicy::class);
        Gate::policy(Enquiry::class, EnquiryPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);

        Gate::define('access-admin', fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-properties', fn (User $user): bool => $user->canListProperties());
    }
}

