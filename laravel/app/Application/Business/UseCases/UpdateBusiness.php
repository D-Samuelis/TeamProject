<?php

namespace App\Application\Business\UseCases;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateBusiness
{
    public function __construct(private BusinessRepositoryInterface $businessRepo, private UserRepositoryInterface $userRepo, private BusinessAuthorizationService $authService) {}

    public function execute(UpdateBusinessDTO $dto, int $userId): void
    {
        $user = $this->userRepo->findById($userId);
        
        $business = $this->businessRepo->findForManagement($dto->id);

        $this->authService->ensureCanUpdateBusiness($user, $business);

        $this->businessRepo->update($dto->id, $dto->toArray());
    }
}
