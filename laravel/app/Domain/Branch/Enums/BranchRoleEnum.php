<?php

namespace App\Domain\Branch\Enums;

enum BranchRoleEnum: string
{
    case MANAGER = 'manager';
    case SUPERVISOR = 'supervisor';
    case STAFF = 'staff';

    public function canUpdate(): bool
    {
        return in_array($this, [self::MANAGER, self::SUPERVISOR]);
    }

    public function canManageStaff(): bool
    {
        return $this === self::MANAGER;
    }

    public function canViewFinancials(): bool
    {
        return $this === self::MANAGER;
    }

    public function canAssign(): bool
    {
        return $this === self::MANAGER;
    }
}
