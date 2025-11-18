<?php

namespace App\Listeners;

use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class PasswordResetListener
{

    /**
     * Handle the event.
     *
     * @param PasswordReset $event
     * @return void
     */
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        if ($user instanceof Admin) {
            $user->status = true;
            $user->save();

            Log::info('Admin status updated to true for user ID: ' . $user->id);
        }
    }
}
