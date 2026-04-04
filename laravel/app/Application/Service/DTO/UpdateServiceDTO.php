<?php

namespace App\Application\Service\DTO;

use App\Http\Requests\Service\UpdateServiceRequest;

class UpdateServiceDTO
{
    public function __construct(
        public int      $id,
        public ?string  $name              = null,
        public ?string  $description       = null,
        public ?int     $base_duration_minutes = null,
        public ?float   $base_price        = null,
        public ?string  $location_type     = null,
        public ?bool    $is_active         = null,
    ) {}

    public static function fromRequest(int $serviceId, UpdateServiceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            id: $serviceId,
            name: $validated['name'] ?? null,
            description: $validated['description'] ?? null,
            base_duration_minutes: isset($validated['duration_minutes']) ? (int) $validated['duration_minutes'] : null,
            base_price: isset($validated['price']) ? (float) $validated['price'] : null,
            location_type: $validated['location_type'] ?? null,
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'                  => $this->name,
            'description'           => $this->description,
            'base_duration_minutes' => $this->base_duration_minutes,
            'base_price'            => $this->base_price,
            'location_type'         => $this->location_type,
            'is_active'             => $this->is_active,
        ], fn($value) => ! is_null($value));
    }
}
