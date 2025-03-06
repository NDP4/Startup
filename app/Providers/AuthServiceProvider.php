<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sanctum::authenticateAccessTokensUsing(function ($token, $isValid) {
            if (!$isValid) {
                return false;
            }

            if ($token->expires_at && $token->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'error' => 'Token telah kadaluarsa'
                ], 401);
            }

            return true;
        });
    }
}
