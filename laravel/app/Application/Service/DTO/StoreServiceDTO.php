<?php

namespace App\Application\Service\DTO;

use App\Http\Requests\Service\StoreServiceRequest;

class StoreServiceDTO
{
    public function __construct(
        public int     $business_id,
        public string  $name,
        public ?string $description          = null,
        public int     $base_duration_minutes = 0,
        public float   $base_price           = 0.0,
        public string  $location_type        = 'branch',
        public bool    $is_active            = false,
        // Kept separate — not persisted on the Service model,
        // used by StoreService to create BranchService instances.
        public array   $branch_ids           = [],
    ) {}

    public static function fromRequest(StoreServiceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            business_id:           (int)   $validated['business_id'],
            name:                          $validated['name'],
            description:                   $validated['description'] ?? null,
            base_duration_minutes: (int)   $validated['duration_minutes'],
            base_price:            (float) $validated['price'],
            location_type:                 $validated['location_type'] ?? 'branch',
            is_active:                     $request->boolean('is_active'),
            branch_ids:                    $validated['branch_ids'] ?? [],
        );
    }

    /**
     * Only the columns that exist on the `services` table.
     * branch_ids is intentionally excluded — handled separately in StoreService.
     */
    public function toTemplateArray(): array
    {
        return [
            'business_id'           => $this->business_id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'base_duration_minutes' => $this->base_duration_minutes,
            'base_price'            => $this->base_price,
            'location_type'         => $this->location_type,
            'is_active'             => $this->is_active,
        ];
    }
}
