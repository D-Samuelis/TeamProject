<?php

namespace App\Application\DTO;

class BranchSearchDTO
{
    public function __construct(
        public readonly ?string $branchName = null,
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?string $address = null,
        public readonly array $statuses = [],
        public readonly array $types = [],
        public readonly ?int $businessId = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
           branchName: $data['branch_name'] ?? null,
            country: $data['country'] ?? null,
            city: $data['city'] ?? null,
            address: $data['address'] ?? null,
            statuses: $data['statuses'] ?? [],
            types: $data['types'] ?? [],
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
