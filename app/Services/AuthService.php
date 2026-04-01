<?php

namespace App\Services;

use App\DTOs\RegisterCustomerDTO;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use ApiResponse;

    public function attemptLogin(string $email, string $password, bool $remember): array
    {
        $user = User::with('companyProfile.address')
            ->where('email', $email)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        $maxTokens = 5;

        $user->tokens()
            ->where('expires_at', '<', now())
            ->delete();

        if ($user->tokens()->count() >= $maxTokens) {
            $user->tokens()->oldest()->first()?->delete();
        }

        $token = $user->createToken(
            'auth-token',
            [],
            $remember ? now()->addWeek() : now()->addDay()
        );

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
            'user' => new CustomerResource($user),
        ];
    }

    public function register(RegisterCustomerDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create(
                $dto->user->toRegistrationUserAttributes(
                    Hash::make($dto->password),
                    $dto->accountType
                )
            );

            if ($dto->companyProfile !== null) {
                $user->companyProfile()->create($dto->companyProfile->toCreateArray());
            }

            event(new Registered($user));

            $token = $user->createToken('customer-auth-token', [], now()->addDay());

            return [
                'token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at,
                'user' => new CustomerResource($user->load('companyProfile.address')),
                'message' => 'Registration successful. Please check your email to verify your account.',
            ];
        });
    }

    public function changePassword($password, $current_password): JsonResponse|bool
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if (!Hash::check($current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect'],
            ]);
        }

        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function resetPassword(array $credentials): void
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                ]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function sendResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
