<?php

namespace App\Application\Service\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Service;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class GetService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the get service use case. It checks if the user has permission to view the service and returns it if found.
     * @param int $serviceId The ID of the service to retrieve.
     * @param User|null $user The user requesting the service, or null for unauthenticated access.
     * @return Service The retrieved service.
     */
    public function execute(int $serviceId, ?User $user = null): Service
    {
        if ($user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $this->authService->ensureCanViewService($user, $service);
            return $service;
        }

        $service = $this->serviceRepo->findActive($serviceId);
        return $service;
    }
}
