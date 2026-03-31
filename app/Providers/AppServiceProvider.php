<?php

namespace App\Providers;

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
        // Register authorization policies
        $this->registerPolicies();
    }

    /**
     * Register the application's authorization policies.
     */
    protected function registerPolicies(): void
    {
        // Use Gate::policy() or register policies through gate facade
        // In Laravel 11+, policies in app/Policies directory are auto-discovered
        // But we can be explicit here for clarity
    }
}
