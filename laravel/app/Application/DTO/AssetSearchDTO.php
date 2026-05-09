<?php

namespace App\Application\DTO;

class AssetSearchDTO
{
    public function __construct(
        public readonly ?string $assetName = null,
        public readonly ?string $description = null,
        public readonly array $statuses = [],
        public readonly ?int $serviceId = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            assetName: $data['asset_name'] ?? null,
            description: $data['description'] ?? null,
            statuses: $data['statuses'] ?? [],
            serviceId: isset($data['service_id']) ? (int) $data['service_id'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
