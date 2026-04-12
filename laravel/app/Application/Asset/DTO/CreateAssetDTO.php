<?php

namespace App\Application\Asset\DTO;

class CreateAssetDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $is_active = false,
        public int $branch_id,
        public array $service_ids = []
    ) {}

    public function toArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'branch_id' => $this->branch_id,
            'service_ids' => $this->service_ids
        ];
    }
}
