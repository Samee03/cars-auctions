<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerResetPassword extends ResetPasswordNotification implements ShouldQueue
{
    use Queueable;

    /**
     * SPA (or web app) URL with token and email query params for POST /api/reset-password.
     *
     * @param mixed $notifiable
     */
    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        $base = rtrim((string)(config('auth.verification.frontend_url') ?: config('app.url')), '/');
        $path = '/' . ltrim((string)config('auth.password_reset.path', '/reset-password'), '/');

        return $base . $path . '?' . http_build_query([
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
    }
}
