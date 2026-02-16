<?php
// app/Infrastructure/Auth/SpatieRoleAssigner.php
namespace App\Infrastructure\Auth;

use App\Domain\User\Entities\User as DomainUser;
use App\Models\User as EloquentUser;

final class SpatieRoleAssigner
{
    public function assignRole(DomainUser $user, string $role): void
    {
        $eloquent = EloquentUser::find($user->getId());
        if (!$eloquent) {
            throw new \RuntimeException('User not found for role assignment.');
        }

        $eloquent->assignRole($role); // spatie method
    }
}
