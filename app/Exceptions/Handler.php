<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ...existing code...

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => 'Token tidak valid atau telah kadaluarsa'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
