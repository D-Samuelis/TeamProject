<?php

namespace App\Application\DTO;

use Illuminate\Http\Request;

class AssignUserDTO
{
    public function __construct(
        public int $businessId,
        public string $email,
        public string $role,
        public string $targetType,
        public int $targetId      
    ) {}

    public static function fromRequest(Request $request, int $businessId): self
    {
        return new self(
            businessId: $businessId,
            email: $request->email,
            role: $request->role,
            targetType: $request->input('target_type', 'business'),
            targetId: (int) $request->input('target_id', $businessId)
        );
    }
}