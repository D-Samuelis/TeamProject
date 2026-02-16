<?php
// app/Infrastructure/Auth/TokenServiceInterface.php
namespace App\Infrastructure\Auth;

use App\Domain\User\Entities\User;

// Keep token creation and role assignment in infrastructure layer so domain/use cases remain infrastructure-agnostic.
interface TokenServiceInterface
{
    public function createTokenFor(User $user, string $name = 'api-token'): string;
    public function revokeAllTokensFor(User $user): void;
}
