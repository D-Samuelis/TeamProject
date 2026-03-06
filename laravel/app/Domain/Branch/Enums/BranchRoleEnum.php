<?php

namespace App\Domain\Branch\Enums;

enum BranchRoleEnum: string
{
    case MANAGER = 'manager';
    case STAFF = 'staff';
}