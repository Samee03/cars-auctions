<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiEmailIsVerified
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return self::error('Unauthenticated.', 401, ['error_code' => 'unauthenticated']);
        }

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return self::error(
                'Please verify your email address before using this feature. Check your inbox for the verification link, or use POST /api/email/resend while signed in.',
                403,
                ['error_code' => 'email_not_verified']
            );
        }

        return $next($request);
    }
}
