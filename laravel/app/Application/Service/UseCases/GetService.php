<?php

namespace App\Application\Service\UseCases;

use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Service;

class GetService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    public function execute(int $serviceId, ?User $user = null): Service
    {
        if ($user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $this->authService->ensureCanViewService($user, $service);
            return $service;
        }

        return $this->serviceRepo->findActive($serviceId);
    }
}
