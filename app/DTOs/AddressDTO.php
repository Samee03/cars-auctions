<?php

namespace App\DTOs;

class AddressDTO
{
    public function __construct(
        public string  $street,
        public string  $city,
        public string  $state,
        public string  $country,
        public string  $zip,
        public ?string $type,
        public ?bool   $isDefault,
        public ?string $notes
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            street: $data['street'],
            city: $data['city'],
            state: $data['state'],
            country: $data['country'],
            zip: $data['zip'],
            type: $data['type'],
            isDefault: $data['is_default'] ?? false,
            notes: $data['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value === '' ? null : $value;
        }, [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip' => $this->zip,
            'type' => $this->type,
            'is_default' => $this->isDefault,
            'notes' => $this->notes,
        ]);
    }
}
