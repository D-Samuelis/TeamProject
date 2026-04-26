<?php

namespace App\Application\Service\DTO;

use App\Application\Service\Services\DurationParser;
use App\Http\Requests\Service\StoreServiceRequest;

class StoreServiceDTO
{
    public function __construct(
        public int $business_id,
        public string $name,
        public ?string $description = null,
        public int $duration_minutes = 0,
        public float $price = 0.0,
        public string $location_type = 'branch',
        public bool $is_active = false,
        public array $branch_ids = [],
        public ?int $cancellation_period_minutes = null,
        public bool $requires_manual_acceptance = false,
    ) {}

    /**
     * Map the request data to the DTO.
     */
    public static function fromRequest(StoreServiceRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            business_id: (int) $validated['business_id'],
            name: $validated['name'],
            description: $validated['description'] ?? null,
            duration_minutes: (int) $validated['duration_minutes'],
            price: (float) $validated['price'],
            location_type: $validated['location_type'] ?? 'branch',
            is_active: $request->boolean('is_active'),
            branch_ids: $validated['branch_ids'] ?? [],
            cancellation_period_minutes: DurationParser::toMinutes($validated['cancellation_period'] ?? null),
            requires_manual_acceptance: $request->boolean('requires_manual_acceptance'),
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
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'location_type' => $this->location_type,
            'is_active' => $this->is_active,
            'branch_ids' => $this->branch_ids,
            'cancellation_period_minutes' => $this->cancellation_period_minutes,
            'requires_manual_acceptance' => $this->requires_manual_acceptance,
        ];
    }
}
