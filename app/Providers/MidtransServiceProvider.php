<?php

namespace App\Providers;

use App\Services\MidtransService;
use Illuminate\Support\ServiceProvider;

class MidtransServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MidtransService::class);
    }
}
