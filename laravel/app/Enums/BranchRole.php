<?php

namespace App\Enums;

enum BranchRole: string
{
    case MANAGER = 'manager';
    case STAFF = 'staff';
}