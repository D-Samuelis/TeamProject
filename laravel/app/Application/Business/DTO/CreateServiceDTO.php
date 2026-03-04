<?php

namespace App\Application\Business\DTO;

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
}