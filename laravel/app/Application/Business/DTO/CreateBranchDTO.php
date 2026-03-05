<?php

namespace App\Application\Business\DTO;

class CreateBranchDTO
{
    public function __construct(
        public int $business_id,
        public string $name,
        public string $type,
        public string $address_line_1,
        public ?string $address_line_2 = null,
        public string $city,
        public string $postal_code,
        public string $country,
        public ?bool $is_active = true,
    ) {}

    public function toArray()
    {
        return [
            'business_id' => $this->business_id,
            'name' => $this->name,
            'type' => $this->type,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_active' => $this->is_active,
        ];
    }
}
