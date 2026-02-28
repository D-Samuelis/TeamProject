<?php

namespace App\Domain\Business\Enums;

enum BranchRoleEnum: string
{
    case MANAGER = 'manager';
    case STAFF = 'staff';
}