<?php

namespace App\DTOs;

class RegisterCustomerDTO
{
    public function __construct(
        public readonly UserDTO            $user,
        public readonly string             $password,
        public readonly string             $accountType,
        public readonly ?CompanyProfileDTO $companyProfile = null,
    )
    {
    }

    /**
     * @param array<string, mixed> $data Output of RegisterRequest::validated()
     */
    public static function fromValidated(array $data): self
    {
        $user = UserDTO::fromArray([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $company = ($data['account_type'] ?? '') === 'company'
            ? CompanyProfileDTO::fromRegistration($data)
            : null;

        return new self(
            user: $user,
            password: $data['password'],
            accountType: $data['account_type'],
            companyProfile: $company,
        );
    }
}
