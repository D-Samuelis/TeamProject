<?php

namespace App\Application\Business\DTO;

class UpdateBusinessDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = "",
        public bool $is_published = false,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_published' => $this->is_published,
        ];
    }
}