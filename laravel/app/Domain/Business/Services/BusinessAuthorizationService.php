<?php

namespace App\Domain\Business\Services;

use App\Domain\Business\Entities\Business;
use App\Enums\BusinessRole;

class BusinessAuthorizationService
{
    public function ensureOwner(Business $business, int $userId): void
    {
        $isOwner = $business->users()
            ->where('user_id', $userId)
            ->wherePivot('role', BusinessRole::OWNER->value)
            ->exists();

        if (!$isOwner) {
            throw new \DomainException('Not business owner');
        }
    }
}
