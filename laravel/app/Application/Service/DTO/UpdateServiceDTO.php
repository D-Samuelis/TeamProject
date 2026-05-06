<?php

namespace App\Application\Service\DTO;

use App\Application\Service\Services\DurationParser;
use App\Http\Requests\Service\UpdateServiceRequest;

class UpdateServiceDTO
{
    public function __construct(
        public int $id,
        public ?int $category_id = null,
        public bool $category_id_provided = false,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $duration_minutes = null,
        public ?float $price = null,
        public ?string $location_type = null,
        public ?bool $is_active = null,
        public ?array $branch_ids = null,
        public ?int $cancellation_period_minutes = null,
        public bool $requires_manual_acceptance = false,
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
            category_id: isset($validated['category_id']) ? (int) $validated['category_id'] : null,
            category_id_provided: array_key_exists('category_id', $validated),
            name: $validated['name'] ?? null,
            description: $validated['description'] ?? null,
            duration_minutes: isset($validated['duration_minutes']) ? (int) $validated['duration_minutes'] : null,
            price: isset($validated['price']) ? (float) $validated['price'] : null,
            location_type: $validated['location_type'] ?? null,
            is_active: $request->has('is_active') ? $request->boolean('is_active') : null,
            branch_ids: $validated['branch_ids'] ?? null,
            cancellation_period_minutes: DurationParser::toMinutes($validated['cancellation_period'] ?? null),
            requires_manual_acceptance: $request->boolean('requires_manual_acceptance'),
        );
    }

    public function toArray(): array
    {
        $data =  array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'location_type' => $this->location_type,
            'is_active' => $this->is_active,
            'branch_ids' => $this->branch_ids,
            'requires_manual_acceptance' => $this->requires_manual_acceptance,
        ], fn($value) => !is_null($value));

        $data['cancellation_period_minutes'] = $this->cancellation_period_minutes;

        if ($this->category_id_provided) {
            $data['category_id'] = $this->category_id;
        }

        return $data;
    }
}
