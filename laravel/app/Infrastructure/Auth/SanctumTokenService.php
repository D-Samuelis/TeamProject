<?php
// app/Infrastructure/Auth/SanctumTokenService.php
namespace App\Infrastructure\Auth;

use App\Models\Auth\User as EloquentUser;
use App\Domain\User\Entities\User as DomainUser;

final class SanctumTokenService implements TokenServiceInterface
{
    public function createTokenFor(DomainUser $user): string
    {
        $eloquentUser = EloquentUser::find($user->id);
        return $eloquentUser->createToken('api')->plainTextToken;
    }

    public function revokeAllTokensFor(DomainUser $user): void
    {
        $eloquentUser = EloquentUser::find($user->id);
        $eloquentUser?->tokens()->delete();
    }
}
