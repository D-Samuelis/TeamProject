<?php

namespace App\Application\Business\DTO;

class SearchDTO
{
    public function __construct(
        public readonly string $target = 'business', // business | service | branch
        public readonly ?string $query = null,
        public readonly ?int $businessId = null,
        public readonly ?string $city = null,
        public readonly ?float $maxPrice = null,
        public readonly ?int $maxDuration = null,
        public readonly array $locationTypes = [],
    ) {}

    public static function fromRequest(array $data, string $defaultTarget = 'business'): self
    {
        return new self(
            target: $data['target'] ?? $defaultTarget,
            query: $data['q'] ?? null,
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            city: $data['city'] ?? null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            maxDuration: isset($data['max_duration']) ? (int) $data['max_duration'] : null,
            locationTypes: $data['location_types'] ?? [],
        );
    }

    public static function fromArray(array $data, string $defaultTarget = 'business'): self
    {
        return new self(
            target: $data['target'] ?? $defaultTarget,
            query: $data['q'] ?? null,
            businessId: isset($data['business_id']) ? (int) $data['business_id'] : null,
            city: $data['city'] ?? null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            maxDuration: isset($data['max_duration']) ? (int) $data['max_duration'] : null,
            locationTypes: $data['location_types'] ?? [],
        );
    }
}