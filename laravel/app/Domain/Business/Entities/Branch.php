<?php

namespace App\Domain\Business\Entities;

final class Branch
{
    public function __construct(
        public ?int $id,
        public int $business_id,
        public string $name,
        public string $type,
        public ?string $address_line_1,
        public ?string $address_line_2,
        public string $city,
        public string $postal_code,
        public string $country,
        public bool $is_active
    ) {}
}
