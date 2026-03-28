<?php

namespace App\Application\Business\UseCases;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Business;

class UpdateBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    public function execute(UpdateBusinessDTO $dto, User $user): Business
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->id);

            $this->authService->ensureCanUpdateBusiness($user, $business);

            return $this->businessRepo->update($dto->id, $dto->toArray());
        });
    }
}
