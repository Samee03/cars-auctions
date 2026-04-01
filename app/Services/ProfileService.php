<?php

namespace App\Services;

use App\DTOs\CompanyProfileDTO;
use App\DTOs\UserDTO;
use App\Models\User;

class ProfileService
{
    /**
     * @param array<string, mixed>|null $companyProfile Validated company_profile payload, if any
     */
    public function updateUser(UserDTO $dto, ?array $companyProfile = null): User
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update($dto->toUserArray());

        if ($user->isCompanyBuyer() && is_array($companyProfile) && $companyProfile !== []) {
            $attributes = CompanyProfileDTO::attributesFromProfileInput($companyProfile);
            if ($attributes !== []) {
                $this->syncCompanyProfile($user, $attributes);
            }
        }

        return $user->load(['addresses', 'companyProfile.address']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function syncCompanyProfile(User $user, array $attributes): void
    {
        $profile = $user->companyProfile;

        if ($profile !== null) {
            $profile->update($attributes);

            return;
        }

        $defaults = [
            'company_name' => trim(implode(' ', array_filter([$user->first_name, $user->last_name]))) ?: 'Company',
            'company_phone' => $user->phone,
            'contact_first_name' => $user->first_name,
            'contact_last_name' => $user->last_name,
            'contact_email' => $user->email,
            'contact_phone' => $user->phone,
        ];

        $user->companyProfile()->create(array_merge($defaults, $attributes));
    }
}
