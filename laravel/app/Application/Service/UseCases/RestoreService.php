<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Models\Auth\User;

class RestoreService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    public function execute(int $serviceId, User $user): void
    {
        DB::transaction(function () use ($serviceId, $user) {
            $service = $this->serviceRepo->findForManagement($serviceId);

            $this->authService->ensureCanUpdateService($user, $service);

            $this->serviceRepo->restore($service);
        });
    }
}
