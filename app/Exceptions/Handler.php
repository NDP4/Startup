<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Handle API errors
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak ditemukan',
                    ], 404);
                }

                if ($e instanceof AccessDeniedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak',
                    ], 403);
                }

                if ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();

                    if ($statusCode === 419) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Sesi telah kadaluarsa',
                        ], 419);
                    }

                    if ($statusCode === 429) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Terlalu banyak permintaan',
                        ], 429);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Handle web errors
            $statusCode = 500;

            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
            }

            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [], $statusCode);
            }

            if ($statusCode === 500) {
                return response()->view('errors.500', [
                    'error' => $e->getMessage()
                ], 500);
            }

            return response()->view('errors.default', [
                'error' => $e
            ], $statusCode);
        });
    }
}
