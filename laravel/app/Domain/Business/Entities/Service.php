<?php
namespace App\Domain\Business\Entities;

final class Service
{
    public function __construct(
        public int $id,
        public int $businessId,
        public string $name,
        public ?string $description,
        public int $durationMinutes,
        public float $price,
        public string $locationType,
        public bool $isActive
    ) {}
}