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
     */
    public static function fromRequest(int $branchId, UpdateBranchRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            id: $branchId,
            name: $validated['name'] ?? null,
            type: $validated['type'] ?? null,
            address_line_1: $validated['address_line_1'] ?? null,
            address_line_2: $validated['address_line_2'] ?? null,
            city: $validated['city'] ?? null,
            postal_code: $validated['postal_code'] ?? null,
            country: $validated['country'] ?? null,
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null,
        );
    }

    /**
     * Convert to array, filtering out null values.
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
