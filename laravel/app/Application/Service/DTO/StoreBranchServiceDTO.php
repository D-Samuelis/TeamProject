<?php

namespace App\Application\Service\DTO;

use Illuminate\Http\Request;

/**
 * Used when creating a BranchService instance with explicit overrides,
 * as opposed to AssignServiceToBranch which just creates a plain default instance.
 */
class StoreBranchServiceDTO
{
    public function __construct(
        public int     $service_id,
        public int     $branch_id,
        public ?float  $custom_price             = null,
        public ?int    $custom_duration_minutes  = null,
        public ?string $custom_description       = null,
        public ?string $location_type            = null,
        public bool    $is_enabled               = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();

        return new self(
            service_id:               (int) $validated['service_id'],
            branch_id:                (int) $validated['branch_id'],
            custom_price:             isset($validated['custom_price'])            ? (float) $validated['custom_price']            : null,
            custom_duration_minutes:  isset($validated['custom_duration_minutes']) ? (int)   $validated['custom_duration_minutes']  : null,
            custom_description:       $validated['custom_description']             ?? null,
            location_type:            $validated['location_type']                  ?? null,
            is_enabled:               $request->boolean('is_enabled', true),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'service_id'              => $this->service_id,
            'branch_id'               => $this->branch_id,
            'custom_price'            => $this->custom_price,
            'custom_duration_minutes' => $this->custom_duration_minutes,
            'custom_description'      => $this->custom_description,
            'location_type'           => $this->location_type,
            'is_enabled'              => $this->is_enabled,
        ], fn($v) => ! is_null($v));
    }
}
