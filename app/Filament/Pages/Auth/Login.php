<?php

namespace App\Filament\Pages\Auth;

use App\Models\Admin;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => '',
            'password' => '',
            'remember' => true,
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Check if the authenticated user is an Admin
        if ($user instanceof Admin) {
            // Determine if the Admin has the "Super Admin" role
            $isSuperAdmin = $user->roles()->where('name', 'Super Admin')->exists();

            // Bypass the status check for Super Admins
            if (!$isSuperAdmin && $user->status === 0) {
                Filament::auth()->logout();

                throw ValidationException::withMessages([
                    'data.email' => 'Your account is suspended.',
                ]);
            }
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

}
