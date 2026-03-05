<?php

namespace App\Application\Business\DTO;

use App\Domain\Business\Enums\BusinessStateEnum;

class CreateBusinessDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?BusinessStateEnum $state = BusinessStateEnum::PENDING,
        public ?bool $isPublished = false,
    ) {}

    public function toArray(){
        return ['name'=> $this->name, 'description' => $this->description];
    }
}