<?php

namespace App\Application\Business\DTO;

use App\Http\Requests\Business\UpdateBusinessRequest;

class UpdateBusinessDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $description = null,
        public ?bool $is_published = null,
    ) {}

    /**
     * Map the request data to the DTO.
     */
    public static function fromRequest(int $businessId, UpdateBusinessRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            id: $businessId,
            name: $validated['name'] ?? null,
            description: $validated['description'] ?? null,
            is_published: $request->has('is_published') ? $request->boolean('is_published') : null,
        );
    }

    /**
     * Convert to array, filtering out null values.
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'is_published' => $this->is_published,
        ], fn($value) => !is_null($value));
    }
}
