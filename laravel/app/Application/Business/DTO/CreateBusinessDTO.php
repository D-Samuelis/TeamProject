<?php

namespace App\Application\Business\DTO;

use App\Http\Requests\Business\StoreBusinessRequest;

class CreateBusinessDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?bool $is_published = false,
    ) {}

    public static function fromRequest(StoreBusinessRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            is_published: $request->boolean('is_published'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_published' => $this->is_published
        ];
    }
}
