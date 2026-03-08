<?php

namespace App\Application\Business\UseCases;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\CreateBusinessDTO;
use Illuminate\Support\Facades\DB;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Business\Business;

class CreateBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly UserRepositoryInterface $userRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    public function execute(CreateBusinessDTO $dto, int $userId): Business
    {
        return DB::transaction(function () use ($dto, $userId) {
            $user = $this->userRepo->findById($userId);
            $this->authService->ensureCanCreateBusiness($user);
            
            $business = $this->businessRepo->save($dto->toArray());
            $this->businessRepo->attachUser($business, $userId, BusinessRoleEnum::OWNER);

            return $business;
        });
    }
}
