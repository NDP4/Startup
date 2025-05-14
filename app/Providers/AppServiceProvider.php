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
        if (config('app.force_https', true)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));

            // Trust x-forwarded headers for HTTPS
            request()->server->set('HTTPS', true);

            if ($this->app->environment('production')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
