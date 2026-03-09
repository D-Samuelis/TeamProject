<?php

namespace App\Application\Service\DTO;

use App\Http\Requests\Service\UpdateServiceRequest;

class UpdateServiceDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $duration_minutes = null,
        public ?float $price = null,
        public ?string $location_type = null,
        public ?bool $is_active = null,
        public ?array $branch_ids = null,
    ) {}

    /**
     * Map the request data to the DTO.
     * Note: We use $branchId from the route parameter.
     */
    public static function fromRequest(int $serviceId, UpdateServiceRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            id: $serviceId,
            name: $validated['name'] ?? null,
            description: $validated['description'] ?? null,
            duration_minutes: isset($validated['duration_minutes']) ? (int) $validated['duration_minutes'] : null,
            price: isset($validated['price']) ? (float) $validated['price'] : null,
            location_type: $validated['location_type'] ?? null,
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null,
            branch_ids: $validated['branch_ids'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'location_type' => $this->location_type,
            'is_active' => $this->is_active,
            'branch_ids' => $this->branch_ids,
        ], fn($value) => !is_null($value));
    }
}
