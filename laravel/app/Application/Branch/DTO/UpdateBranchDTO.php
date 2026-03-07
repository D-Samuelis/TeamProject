<?php

namespace App\Application\Branch\DTO;

use App\Http\Requests\Branch\UpdateBranchRequest;

class UpdateBranchDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $type = null,
        public ?string $address_line_1 = null,
        public ?string $address_line_2 = null,
        public ?string $city = null,
        public ?string $postal_code = null,
        public ?string $country = null,
        public ?bool $is_active = null
    ) {}

    /**
     * Map the request data to the DTO.
     * Note: We use $branchId from the route parameter.
     */
    public static function fromRequest(int $branchId, UpdateBranchRequest $request): self
    {
        return new self(
            id: $branchId,
            name: $request->validated('name'),
            type: $request->validated('type'),
            address_line_1: $request->validated('address_line_1'),
            address_line_2: $request->validated('address_line_2'),
            city: $request->validated('city'),
            postal_code: $request->validated('postal_code'),
            country: $request->validated('country'),
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null
        );
    }

    /**
     * Convert to array, filtering out null values.
     * This ensures we only update the fields provided in the request (Patch style).
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'type' => $this->type,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_active' => $this->is_active,
        ], fn($value) => !is_null($value));
    }
}