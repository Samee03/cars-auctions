<?php

namespace App\Services;

use App\Http\Resources\CustomerResource;
use App\Models\CompanyProfile;
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

        $user->tokens()->delete();

        $token = $user->createToken(
            'customer-auth-token',
            [],
            $remember ? now()->addWeek() : now()->addDay()
        );

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
            'user' => new CustomerResource($user),
        ];
    }

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'account_type' => $data['account_type'],
                'status' => 'active',
                'admin_approval_status' => 'pending',
                'terms_accepted_at' => now(),
            ]);

            if ($data['account_type'] === 'company') {
                $companyProfile = new CompanyProfile();
                $companyProfile->user_id = $user->id;
                $companyProfile->address_id = $data['company_address_id'] ?? null;
                $companyProfile->company_name = $data['company_name'];
                $companyProfile->registration_number = $data['registration_number'] ?? null;
                $companyProfile->company_phone = $data['company_phone'];
                $companyProfile->company_address = $data['company_address'] ?? null;
                $companyProfile->contact_first_name = $data['contact_first_name'] ?? $data['first_name'];
                $companyProfile->contact_last_name = $data['contact_last_name'] ?? $data['last_name'];
                $companyProfile->contact_email = $data['contact_email'] ?? $data['email'];
                $companyProfile->contact_phone = $data['contact_phone'] ?? null;
                $companyProfile->save();
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
            'password' => $password,
        ]);
    }

    public function resetPassword(array $credentials): void
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $user->update([
                    'password' => $password,
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
