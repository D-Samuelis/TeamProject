<?php

namespace App\Application\DTO;

class BusinessSearchDTO
{
    public function __construct(
        public readonly ?string $businessName = null,
        public readonly ?string $description = null,
        public readonly array $statuses = [],
        public readonly ?string $published = null,
        public readonly ?int $userId = null,
        public readonly ?string $role = null,
        public readonly ?int $categoryId = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            businessName: $data['business_name'] ?? null,
            description: $data['description'] ?? null,
            statuses: $data['statuses'] ?? [],
            published: $data['published'] ?? null,
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            role: $data['role'] ?? null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
