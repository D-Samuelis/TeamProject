<?php
// app/Application/Auth/RegisterUser.php
namespace App\Application\Auth\UseCases;

use App\Domain\User\Entities\User;
use App\Domain\User\Services\PasswordHasher;

use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Application\Auth\DTO\RegisterUserDTO;

use App\Infrastructure\Auth\TokenServiceInterface;
use App\Infrastructure\Auth\SpatieRoleAssigner;

/**
 * Use case class to handle user registration logic.
 */
final class RegisterUser
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private SpatieRoleAssigner $roleAssigner,
        private PasswordHasher $hasher
    ) {}

    public function execute(RegisterUserDTO $dto): RegisteredUserDTO
    {
        $user = new User([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $this->hasher->hash($dto->password),
        ]);

        $user->save(); // Eloquent directly saves

        $this->roleAssigner->assignRole($user, 'client');

        $token = $this->tokenService->createTokenFor($user);

        return new RegisteredUserDTO($user, $token);
    }
}
