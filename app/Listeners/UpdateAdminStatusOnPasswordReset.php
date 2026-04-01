<?php

namespace App\Listeners;

use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class UpdateAdminStatusOnPasswordReset
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param PasswordReset $event
     * @return void
     */


    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        // Check if the user is an admin
        if ($user instanceof Admin) {
            // Update the status to true
            $user->status = true;
            $user->save();

            Log::info('Admin status updated to true for user ID: ' . $user->id);
        }
    }
}
