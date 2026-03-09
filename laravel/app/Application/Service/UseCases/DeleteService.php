<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class DeleteService
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepo,
        private UserRepositoryInterface $userRepo,
        //private ServiceAuthorizationService $authService
    ) {}

    public function execute(int $serviceId, int $userId): void
    {
        $service = $this->serviceRepo->findForManagement($serviceId);
        
        $user = $this->userRepo->findById($userId);

        //$this->authService->ensureCanDeleteService($user, $service);
        
        $this->serviceRepo->delete($service);
    }
}