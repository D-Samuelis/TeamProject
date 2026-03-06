<?php

namespace App\Domain\User\Interfaces;

use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Domain\Branch\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(array $data): User;

    public function delete(User $user): void;

    public function getBusinessRole(User $user, Business $business): ?BusinessRoleEnum;

    public function getBranchRole(User $user, Branch $branch): ?BranchRoleEnum;
}
