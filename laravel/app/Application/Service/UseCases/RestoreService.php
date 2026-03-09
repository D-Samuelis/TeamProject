<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;
use Illuminate\Support\Facades\DB;

class RestoreService
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepo,
        private UserRepositoryInterface $userRepo,
        //private ServiceAuthorizationService $authService
    ) {}

    public function execute(int $serviceId, int $userId): void
    {
        DB::transaction(function () use ($serviceId, $userId) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            
            $user = $this->userRepo->findById($userId);

            //$this->authService->ensureCanUpdateService($user, $service);

            $this->serviceRepo->restore($service);
        });
    }
}
