<?php

namespace App\Application\DTO;

class SearchDTO
{
    public function __construct(
        public readonly string $target = 'business', // business | service | branch
        public readonly ?string $query = null,
        public readonly ?int $businessId = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $city = null,
        public readonly ?float $maxPrice = null,
        public readonly ?int $maxDuration = null,
        public readonly array $locationTypes = [],
        public readonly int $perPage = 10,
        public readonly int $page = 1,
    ) {}

    public static function fromRequest(array $data, string $defaultTarget = 'business'): self
    {
        return new self(
            target: $data['target'] ?? $defaultTarget,
            query: $data['q'] ?? null,
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            city: $data['city'] ?? null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            maxDuration: isset($data['max_duration']) ? (int) $data['max_duration'] : null,
            locationTypes: $data['location_types'] ?? [],
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 10,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }

    public static function fromArray(array $data, string $defaultTarget = 'business'): self
    {
        return new self(
            target: $data['target'] ?? $defaultTarget,
            query: $data['q'] ?? null,
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            city: $data['city'] ?? null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            maxDuration: isset($data['max_duration']) ? (int) $data['max_duration'] : null,
            locationTypes: $data['location_types'] ?? [],
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 10,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
