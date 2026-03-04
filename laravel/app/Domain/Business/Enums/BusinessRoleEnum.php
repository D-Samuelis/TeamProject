<?php

namespace App\Domain\Business\Enums;

enum BusinessRoleEnum: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
}
