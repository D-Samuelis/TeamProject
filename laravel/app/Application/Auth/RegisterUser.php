<?php
// app/Application/Auth/RegisterUser.php
namespace App\Application\Auth;

use App\Application\Auth\DTO\RegisterUserDTO;
use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Entities\User as DomainUser;
use App\Infrastructure\Auth\TokenServiceInterface;
use App\Infrastructure\Auth\SpatieRoleAssigner;
use Illuminate\Support\Facades\Hash;

/**
 * Use case class to handle user registration logic. This class encapsulates the business rules for registering a user. It is called by the AuthController to separate concerns. 
 * @package App\Application\Auth
 */
final class RegisterUser
{
    public function __construct(
        private UserRepository $users,
        private TokenServiceInterface $tokenService,
        private SpatieRoleAssigner $roleAssigner
    ) {}

    public function execute(RegisterUserDTO $dto): RegisteredUserDTO
    {
        // map DTO -> domain entity (apply domain rules here if needed)
        $domainUser = new DomainUser(
            null,
            $dto->name,
            $dto->email,
            Hash::make($dto->password)
        );

        $saved = $this->users->save($domainUser);

        // assign default role (infrastructure detail)
        $this->roleAssigner->assignRole($saved, 'client');

        // create token (infrastructure)
        $token = $this->tokenService->createTokenFor($saved);

        return new RegisteredUserDTO($saved, $token);
    }
}
