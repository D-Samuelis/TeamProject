<?php
// app/Infrastructure/Auth/SanctumTokenService.php
namespace App\Infrastructure\Auth;

use App\Models\Auth\User;

final class SanctumTokenService implements TokenServiceInterface
{
    public function createTokenFor(User $user): string
    {
        $eloquentUser = User::find($user->id);
        return $eloquentUser->createToken('api')->plainTextToken;
    }

    public function revokeAllTokensFor(User $user): void
    {
        $eloquentUser = User::find($user->id);
        $eloquentUser?->tokens()->delete();
    }
}
