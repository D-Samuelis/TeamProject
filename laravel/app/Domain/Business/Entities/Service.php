<?php
namespace App\Domain\Business\Entities;

final class Service
{
    public function __construct(
        public ?int $id,
        public int $business_id,
        public string $name,
        public ?string $description,
        public int $duration_minutes,
        public float $price,
        public string $location_type,
        public bool $is_active
    ) {}
}