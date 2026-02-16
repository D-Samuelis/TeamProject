<?php
// app/Infrastructure/Auth/SanctumTokenService.php
namespace App\Infrastructure\Auth;

use App\Domain\User\Entities\User as DomainUser;
use App\Models\User as EloquentUser;

final class SanctumTokenService implements TokenServiceInterface
{
    public function createTokenFor(DomainUser $user, string $name = 'api-token'): string
    {
        // find Eloquent model from domain user id
        $eloquent = EloquentUser::find($user->getId());
        if (!$eloquent) {
            throw new \RuntimeException('Cannot create token for non-existing user.');
        }

        return $eloquent->createToken($name)->plainTextToken;
    }

    public function revokeAllTokensFor(DomainUser $user): void
    {
        $eloquent = EloquentUser::find($user->getId());
        if ($eloquent) {
            $eloquent->tokens()->delete();
        }
    }
}
