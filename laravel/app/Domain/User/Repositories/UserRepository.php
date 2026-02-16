<?php
// app/Domain/User/Repositories/UserRepository.php
namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

// Purpose: domain defines the interface. Implementation lives in Infrastructure.
interface UserRepository
{
    public function save(User $user): User;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
}
