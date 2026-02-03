<?php

namespace App\Providers;

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
        // Register policies
        Gate::policy(\App\Models\Item::class, \App\Policies\ItemPolicy::class);

        // Register observers
        \App\Models\Item::observe(\App\Observers\ItemObserver::class);
        \App\Models\Receiving::observe(\App\Observers\ReceivingObserver::class);
    }
}
