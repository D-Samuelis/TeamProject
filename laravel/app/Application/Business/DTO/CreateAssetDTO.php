<?php

namespace App\Application\Business\DTO;

class CreateAssetDTO
{
    public function __construct(
        public int $branchId,
        public int $serviceId,
        public string $name,
        public ?string $description = null,
    ) {}
}
