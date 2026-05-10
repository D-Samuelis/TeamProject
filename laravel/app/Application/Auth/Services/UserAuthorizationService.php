<?php

namespace App\Application\Auth\Services;

use App\Models\Auth\User;
use App\Models\Business\Service;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use Illuminate\Auth\Access\AuthorizationException;

class UserAuthorizationService
{
    public function ensureCanUpdateUser(User $user): void
    {
        if ($user->isAdmin()) return;

        throw new AuthorizationException('You do not have permission to update this user.');
    }

    public function ensureCanDeleteUser(User $user): void
    {
        if ($user->isAdmin()) return;

        throw new AuthorizationException('You do not have permission to delete this user.');
    }

    public function ensureCanViewUser(User $user): void
    {
        if ($user->isAdmin()) return;

        throw new AuthorizationException('You do not have permission to view this user.');
    }
}
