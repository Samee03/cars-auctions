<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Services\ProfileService;
use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProfileService $profileService) {}

    public function getProfile()
    {
        $user = auth()->user()->load(['addresses', 'companyProfile.address']);

        return $this->success(new CustomerResource($user));
    }

    public function updateUser(UpdateUserProfileRequest $request)
    {
        $validated = $request->validated();
        $dto = UserDTO::fromArray($validated);
        $companyProfile = $validated['company_profile'] ?? null;

        $updatedUser = $this->profileService->updateUser($dto, $companyProfile);

        return $this->success(
            new CustomerResource($updatedUser),
            'Customer profile updated successfully'
        );
    }
}
