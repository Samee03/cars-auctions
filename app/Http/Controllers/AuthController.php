<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $response = $this->authService->attemptLogin(
            $request->email,
            $request->password,
            $request->boolean('remember_me')
        );

        return $this->success($response, 'Logged in successfully');
    }

    public function register(RegisterRequest $request)
    {
        $response = $this->authService->register($request->validated());

        return $this->success($response, 'Registration successful', 201);
    }

    public function getAuthUser(Request $request)
    {
        $user = $request->user()->load('companyProfile.address');

        return $this->success(new CustomerResource($user), 'User authenticated');
    }

    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $user->getKey(), (string) $id)) {
            abort(403);
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return $this->success(
            new CustomerResource($user->load('companyProfile.address')),
            'Email verified successfully. Your account is now pending admin approval.'
        );
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(null, 'Email already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->success(null, 'Verification email sent.');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword($request->password, $request->current_password);

        return $this->success(null, 'Password changed successfully');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());

        return $this->success(null, 'Password reset successful');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->sendResetLink($request->email);

        return $this->success(null, 'Password reset link sent');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(null, 'Logged out successfully');
    }
}
