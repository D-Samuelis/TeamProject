<?php

namespace App\Application\Business\DTO;

class CreateServiceDTO
{
    public function __construct(
        public int $businessId,
        public string $name,
        public ?string $description = null,
        public int $durationMinutes = 0,
        public float $price = 0.0,
        public string $locationType = 'branch',
        public bool $isActive = false,
        public array $branchIds = []
    ) {}
}