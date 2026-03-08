<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(User $user, string $scope = 'active')
    {
        return $this->businessRepo->listForUser($user, $scope);
    }
}
