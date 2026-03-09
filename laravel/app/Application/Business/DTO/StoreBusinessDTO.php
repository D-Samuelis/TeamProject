<?php

namespace App\Application\Business\DTO;

use App\Http\Requests\Business\StoreBusinessRequest;

class StoreBusinessDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?bool $is_published = false,
    ) {}

    /**
     * Map the request data to the DTO.
     */
    public static function fromRequest(StoreBusinessRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            is_published: $request->boolean('is_published'),
        );
    }

    /**
     * Convert to array, filtering out null values.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_published' => $this->is_published
        ];
    }
}
