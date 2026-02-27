<?php

namespace App\Application\Business\DTO;

class CreateBusinessDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $isPublished = false
    ) {}
}