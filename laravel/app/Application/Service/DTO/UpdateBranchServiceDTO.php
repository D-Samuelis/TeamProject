<?php

namespace App\Application\Service\DTO;

use Illuminate\Http\Request;

/**
 * Patch-style DTO — only non-null fields are written.
 * Pass null explicitly (via nullable validation) to clear a custom override
 * and revert that field back to the service template's base value.
 *
 * Example: sending custom_price = null removes the branch price override,
 * so the instance falls back to service->base_price.
 */
class UpdateBranchServiceDTO
{
    public function __construct(
        public int    $id,
        // Use a sentinel to distinguish "not sent" from "explicitly set to null"
        public mixed  $custom_price             = self::NOT_SET,
        public mixed  $custom_duration_minutes  = self::NOT_SET,
        public mixed  $custom_description       = self::NOT_SET,
        public mixed  $location_type            = self::NOT_SET,
        public ?bool  $is_enabled               = null,
    ) {}

    public const NOT_SET = '__NOT_SET__';

    public static function fromRequest(int $branchServiceId, Request $request): self
    {
        $validated = $request->validated();

        return new self(
            id:                      $branchServiceId,
            custom_price:            array_key_exists('custom_price',            $validated) ? ($validated['custom_price'] !== null ? (float) $validated['custom_price'] : null) : self::NOT_SET,
            custom_duration_minutes: array_key_exists('custom_duration_minutes', $validated) ? ($validated['custom_duration_minutes'] !== null ? (int) $validated['custom_duration_minutes'] : null) : self::NOT_SET,
            custom_description:      array_key_exists('custom_description',      $validated) ? $validated['custom_description']  : self::NOT_SET,
            location_type:           array_key_exists('location_type',           $validated) ? $validated['location_type']       : self::NOT_SET,
            is_enabled:              $request->has('is_enabled') ? $request->boolean('is_enabled') : null,
        );
    }

    /**
     * Returns only the fields that were explicitly included in the request.
     * Fields set to null are included so overrides can be cleared.
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->custom_price            !== self::NOT_SET) $data['custom_price']            = $this->custom_price;
        if ($this->custom_duration_minutes !== self::NOT_SET) $data['custom_duration_minutes'] = $this->custom_duration_minutes;
        if ($this->custom_description      !== self::NOT_SET) $data['custom_description']      = $this->custom_description;
        if ($this->location_type           !== self::NOT_SET) $data['location_type']           = $this->location_type;
        if ($this->is_enabled              !== null)          $data['is_enabled']              = $this->is_enabled;

        return $data;
    }
}
