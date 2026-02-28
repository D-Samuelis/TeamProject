<?php

namespace App\Domain\Business\Entities;

final class Branch
{
    public function __construct(
        public int $id,
        public int $businessId,
        public string $name,
        public string $type,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $city,
        public string $postalCode,
        public string $country,
        public bool $isActive
    ) {}
}
