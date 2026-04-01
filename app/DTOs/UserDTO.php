<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class UserDTO
{
    public function __construct(
        public ?string $name,
        public ?string $firstName,
        public ?string $lastName,
        public string  $email,
        public ?string $phone = null,
        public ?string $dateOfBirth = null,
    )
    {
        if ($this->dateOfBirth !== null && $this->dateOfBirth !== '') {
            $this->dateOfBirth = Carbon::parse($this->dateOfBirth)->format('Y-m-d');
        } else {
            $this->dateOfBirth = null;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            email: $data['email'],
            phone: $data['phone'] ?? null,
            dateOfBirth: $data['date_of_birth'] ?? null,
        );
    }

    /**
     * Attributes for updating an existing user (profile).
     *
     * @return array<string, mixed>
     */
    public function toUserArray(): array
    {
        $payload = [
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->dateOfBirth,
        ];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->firstName !== null) {
            $payload['first_name'] = $this->firstName;
        }

        if ($this->lastName !== null) {
            $payload['last_name'] = $this->lastName;
        }

        return $payload;
    }

    /**
     * Core user row for customer registration (merged with hashed password and account flags).
     *
     * @return array<string, mixed>
     */
    public function toRegistrationUserAttributes(string $hashedPassword, string $accountType): array
    {
        return [
            ...$this->toUserArray(),
            'password' => $hashedPassword,
            'account_type' => $accountType,
            'status' => 'active',
            'verified_badge' => false,
            'terms_accepted_at' => Date::now(),
        ];
    }
}
