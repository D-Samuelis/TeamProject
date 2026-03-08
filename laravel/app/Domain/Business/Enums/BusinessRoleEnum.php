<?php

namespace App\Domain\Business\Enums;

enum BusinessRoleEnum: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';

    public function canUpdate(): bool
    {
        return in_array($this, [self::OWNER, self::MANAGER]);
    }

    public function canDelete(): bool
    {
        return $this === self::OWNER;
    }

    public function canPublish(): bool
    {
        return in_array($this, [self::OWNER]);
    }

    public function canManageStaff(): bool
    {
        return $this === self::OWNER;
    }
}
