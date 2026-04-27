<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;

class DeleteService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    /**
     * Executes the delete service use case. It checks if the service exists and if the user has permission to delete it, then deletes the service.
     * @param int $serviceId The ID of the service to delete.
     * @param User $user The user performing the delete operation.
     * @return void
     */
    public function execute(int $serviceId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);
            $this->authService->ensureCanDeleteService($user, $service);
            $this->serviceRepo->delete($service);
        });
    }
}
