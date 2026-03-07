<?php

namespace App\Application\Business\DTO;

use App\Http\Requests\Business\StoreBusinessRequest;

class CreateBusinessDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}

    public static function fromRequest(StoreBusinessRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
        );
    }

    public function toArray(): array
    {
        return ['name' => $this->name, 'description' => $this->description];
    }
}
