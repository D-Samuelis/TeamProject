<?php

namespace App\Application\Asset\DTO;

class UpdateAssetDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public int $branch_id,
        public array $service_ids = []
    ) {}

    public function toArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'branch_id' => $this->branch_id,
            'service_ids' => $this->service_ids
        ];
    }
}
