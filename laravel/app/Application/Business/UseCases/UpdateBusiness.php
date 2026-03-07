<?php

namespace App\Application\Business\UseCases;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo,
        private UserRepositoryInterface $userRepo,
        private BusinessAuthorizationService $authService
    ) {}

    public function execute(UpdateBusinessDTO $dto, int $userId): void
    {
        $business = $this->businessRepo->findById($dto->id);
        $user = $this->userRepo->findById($userId);

        $this->authService->ensureCanUpdateBusiness($user, $business);
        
        $this->businessRepo->update($dto->id, $dto->toArray());
    }
}
