<?php

namespace App\Application\DTO;

class ServiceSearchDTO
{
    public function __construct(
        public readonly ?string $serviceName = null,
        public readonly ?string $description = null,
        public readonly ?int $priceMin = null,
        public readonly ?int $priceMax = null,
        public readonly ?int $durationMin = null,
        public readonly ?int $durationMax = null,
        public readonly array $statuses = [],
        public readonly ?int $businessId = null,
        public readonly ?int $userId = null,
        public readonly ?string $role = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            serviceName: $data['service_name'] ?? null,
            description: $data['description'] ?? null,
            priceMin: $data['price_min'] ?? null,
            priceMax: $data['price_max'] ?? null,
            durationMin: $data['duration_min'] ?? null,
            durationMax: $data['duration_max'] ?? null,
            statuses: $data['statuses'] ?? [],
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            role: $data['role'] ?? null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
