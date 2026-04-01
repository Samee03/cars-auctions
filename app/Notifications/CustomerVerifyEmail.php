<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomerVerifyEmail extends VerifyEmailNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Build a signed verification URL. Uses a path-only signature so the same
     * link works when the public base URL is the SPA (or API) origin.
     *
     * @param  mixed  $notifiable
     */
    protected function verificationUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $base = rtrim((string) (config('auth.verification.frontend_url') ?: config('app.url')), '/');
        $expire = (int) config('auth.verification.expire', 60);

        $signedRelative = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($expire),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            absolute: false
        );

        return $base.$signedRelative;
    }
}
