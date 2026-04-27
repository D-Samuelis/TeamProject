<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Service;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class RestoreService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the restore service use case. It checks if the user has permission to update the service and then restores it.
     * @param int $serviceId The ID of the service to restore.
     * @param User $user The user performing the restore operation.
     * @return Service The restored service.
     */
    public function execute(int $serviceId, User $user): Service
    {
        return DB::transaction(function () use ($serviceId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $this->authService->ensureCanUpdateService($user, $service);
            $this->serviceRepo->restore($service);
            return $service;
        });
    }
}
