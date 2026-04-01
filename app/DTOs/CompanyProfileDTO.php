<?php

namespace App\DTOs;

class CompanyProfileDTO
{
    public function __construct(
        public ?int    $addressId,
        public string  $companyName,
        public ?string $registrationNumber,
        public string  $companyPhone,
        public ?string $companyAddress,
        public string  $contactFirstName,
        public string  $contactLastName,
        public string  $contactEmail,
        public ?string $contactPhone,
    )
    {
    }

    /**
     * @param array<string, mixed> $data Validated company registration payload (includes account holder names/email).
     */
    public static function fromRegistration(array $data): self
    {
        return new self(
            addressId: isset($data['company_address_id']) ? (int)$data['company_address_id'] : null,
            companyName: $data['company_name'],
            registrationNumber: $data['registration_number'] ?? null,
            companyPhone: $data['company_phone'],
            companyAddress: $data['company_address'] ?? null,
            contactFirstName: $data['contact_first_name'] ?? $data['first_name'],
            contactLastName: $data['contact_last_name'] ?? $data['last_name'],
            contactEmail: $data['contact_email'] ?? $data['email'],
            contactPhone: $data['contact_phone'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toCreateArray(): array
    {
        return [
            'address_id' => $this->addressId,
            'company_name' => $this->companyName,
            'registration_number' => $this->registrationNumber,
            'company_phone' => $this->companyPhone,
            'company_address' => $this->companyAddress,
            'contact_first_name' => $this->contactFirstName,
            'contact_last_name' => $this->contactLastName,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
        ];
    }

    /**
     * Partial profile update: only keys present in $data are returned (API uses company_address_id → address_id).
     *
     * @param array<string, mixed> $data Validated company_profile subset
     * @return array<string, mixed>
     */
    public static function attributesFromProfileInput(array $data): array
    {
        $stringKeys = [
            'company_name' => 'company_name',
            'registration_number' => 'registration_number',
            'company_phone' => 'company_phone',
            'company_address' => 'company_address',
            'contact_first_name' => 'contact_first_name',
            'contact_last_name' => 'contact_last_name',
            'contact_email' => 'contact_email',
            'contact_phone' => 'contact_phone',
        ];

        $out = [];

        foreach ($stringKeys as $inputKey => $column) {
            if (!array_key_exists($inputKey, $data)) {
                continue;
            }

            $value = $data[$inputKey];
            $out[$column] = ($value === '' || $value === null) ? null : $value;
        }

        if (array_key_exists('company_address_id', $data)) {
            $v = $data['company_address_id'];
            $out['address_id'] = ($v === '' || $v === null) ? null : (int)$v;
        }

        return $out;
    }
}
