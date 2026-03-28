<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\StoreBusinessDTO;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;

class StoreBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    public function execute(StoreBusinessDTO $dto, User $user): Business
    {
        return DB::transaction(function () use ($dto, $user) {
            $this->authService->ensureCanCreateBusiness($user);

            $business = $this->businessRepo->save($dto->toArray());

            $this->businessRepo->attachUser($business, $user->id, BusinessRoleEnum::OWNER);

            return $business;
        });
    }
}
