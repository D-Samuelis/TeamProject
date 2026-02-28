<?php
namespace App\Domain\Business\Entities;

final class Asset
{
    public function __construct(
        public int $id,
        public int $branchId,
        public int $serviceId,
        public string $name,
        public ?string $description
    ) {}
}
