<?php

namespace App\Application\Business\DTO;

use App\Http\Requests\Business\UpdateBusinessRequest;

class UpdateBusinessDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $description = null,
    ) {}

    /**
     * Map the request data and the route ID to the DTO.
     */
    public static function fromRequest(int $businessId, UpdateBusinessRequest $request): self
    {
        return new self(
            id: $businessId,
            name: $request->validated('name'),
            description: $request->validated('description'),
        );
    }

    /**
     * Convert to array, stripping out nulls to prevent 
     * overwriting existing data with empty values.
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
        ], fn($value) => !is_null($value));
    }
}
