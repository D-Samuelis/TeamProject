<?php

namespace App\Application\Asset\DTO;

class UpdateAssetDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public array $branch_ids = [],
        public array $service_ids = []
    ) {}

    public function toArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'branch_ids' => $this->branch_ids,
            'service_ids' => $this->service_ids
        ];
    }
}
