<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\StoreBusinessDTO;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

use App\Exceptions\Business\BusinessCreationFailedException;

/**
 * Use case for creating a new business. It checks if the user has permission to create a business and then creates it with the provided data.
 * Throws BusinessCreationFailedException if the business creation fails. Returns the created Business model instance if successful.
 * @param StoreBusinessDTO $dto The data transfer object containing the data for the new business.
 * @param User $user The user performing the create operation.
 * @return Business The created business.
 * @throws BusinessCreationFailedException If the business creation fails.
 */
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

            if (!$business) {
                throw new BusinessCreationFailedException();
            }

            $this->businessRepo->attachUser($business, $user->id, BusinessRoleEnum::OWNER);

            return $business;
        });
    }
}
