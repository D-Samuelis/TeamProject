<?php

namespace App\Application\Branch\DTO;

use App\Http\Requests\Branch\StoreBranchRequest;

class StoreBranchDTO
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
        public ?bool $is_active = false
    ) {}

    /**
     * Map the request data to the DTO.
     */
    public static function fromRequest(
        int $businessId,
        StoreBranchRequest $request
    ): self {
        return new self(
            business_id: $businessId,
            name: $request->validated('name'),
            type: $request->validated('type'),
            address_line_1: $request->validated('address_line_1'),
            address_line_2: $request->validated('address_line_2'),
            city: $request->validated('city'),
            postal_code: $request->validated('postal_code'),
            country: $request->validated('country'),
            is_active: false,
        );
    }

    /**
     * Convert to array, filtering out null values.
     */
    public function toArray(): array
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
