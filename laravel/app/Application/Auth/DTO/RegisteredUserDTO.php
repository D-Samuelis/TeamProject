<?php
// app/Application/Auth/DTO/RegisteredUserDTO.php
namespace App\Application\Auth\DTO;

use App\Domain\User\Entities\User;

// Purpose: typed data transfer objects that Application layer uses as input/output.
final class RegisteredUserDTO
{
    public function __construct(
        public User $user,
        public ?string $token = null
    ) {}
}
