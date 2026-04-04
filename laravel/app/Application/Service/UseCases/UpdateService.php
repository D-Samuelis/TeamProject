<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Service;
use App\Application\Service\DTO\UpdateServiceDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Models\Auth\User;

class UpdateService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo,
        private readonly ServiceAuthorizationService $authService
    ) {}

    public function execute(UpdateServiceDTO $dto, User $user): Service
    {
        return DB::transaction(function () use ($dto, $user) {
            $service = $this->serviceRepo->findForManagement($dto->id);

            $this->authService->ensureCanUpdateService($user, $service);

            return $this->serviceRepo->update($service, $dto->toArray());
        });
    }
}
