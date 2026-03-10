<?php

namespace App\Application\Business\DTO;

use App\Models\Auth\User;
use Illuminate\Http\Request;

class AssignUserDTO
{
    public function __construct(
        public int $businessId,
        public int $userId,
        public string $role
    ) {}

    public static function fromRequest(Request $request, int $businessId): self
    {
        $user = User::where('email', $request->email)->firstOrFail();

        return new self(
            businessId: $businessId,
            userId: $user->id,
            role: $request->role
        );
    }
}