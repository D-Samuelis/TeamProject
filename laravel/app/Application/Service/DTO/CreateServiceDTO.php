<?php

namespace App\Application\Service\DTO;

class CreateServiceDTO
{
    public function __construct(
        public int $business_id,
        public string $name,
        public ?string $description = null,
        public int $duration_minutes = 0,
        public float $price = 0.0,
        public string $location_type = 'branch',
        public bool $is_active = false,
        public array $branch_ids = []
    ) {}

    public function toArray()
    {
        return [
            'business_id' => $this->business_id,
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'location_type' => $this->location_type,
            'is_active' => $this->is_active,
            'branch_ids' => $this->branch_ids
        ];
    }
}
