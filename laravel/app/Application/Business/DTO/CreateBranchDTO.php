<?php

namespace App\Application\Business\DTO;

class CreateBranchDTO
{
    public function __construct(
        public int $business_id,
        public string $name,
        public string $type,
        public ?string $addressLine1 = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $country = null,
    ) {}
}