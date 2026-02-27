<?php
// app/Infrastructure/Auth/SanctumTokenService.php
namespace App\Infrastructure\Auth;

use App\Domain\User\Entities\User;

final class SanctumTokenService implements TokenServiceInterface
{
    public function createTokenFor(User $user, string $name = 'api-token'): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    public function revokeAllTokensFor(User $user): void
    {
        $user->tokens()->delete();
    }
}
