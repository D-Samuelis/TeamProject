<?php

namespace App\Application\Business\DTO;

class CreateBranchDTO
{
    public function __construct(
        public int $business_id,
        public string $name,
        public string $type,
        public string $address_line_1,
        public ?string $address_line_2 = null,
        public string $city,
        public string $postal_code,
        public string $country,
    ) {}
}