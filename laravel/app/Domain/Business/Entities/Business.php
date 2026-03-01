<?php

namespace App\Domain\Business\Entities;

use App\Domain\Business\Enums\BusinessStateEnum;

final class Business
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $description,
        public BusinessStateEnum $state,
        public bool $isPublished
    ) {}

    public function isApproved(): bool
    {
        return $this->state === BusinessStateEnum::APPROVED;
    }
}
