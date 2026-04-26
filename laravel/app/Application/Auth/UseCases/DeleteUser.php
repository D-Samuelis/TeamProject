<?php

namespace App\Application\Auth\UseCases;


use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Exceptions\CannotDeleteAccountException;
use App\Models\Auth\User;

class DeleteUser
{
    public function execute(User $user): void
    {
        if ($user->businesses()->wherePivot('role', BusinessRoleEnum::OWNER->value)->exists()) {
            throw new CannotDeleteAccountException(
                'You are the owner of a business. You cannot delete your account.'
            );
        }

        $user->appointments()
            ->where('status', '!=', 'cancelled')
            ->where('date', '>=', now()->toDateString())
            ->update(['status' => 'cancelled']);

        $user->delete();
    }
}