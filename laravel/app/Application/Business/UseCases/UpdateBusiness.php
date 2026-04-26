<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

use App\Exceptions\Business\BusinessNotFoundException;

/**
 * Use case for updating a business. It checks if the user has permission to update the business and then updates it with the provided data.
 * Throws BusinessNotFoundException if the business does not exist or the user does not have access. Returns the updated Business model instance if successful.
 * @param UpdateBusinessDTO $dto The data transfer object containing the business ID and the data to update.
 * @param User $user The user performing the update operation.
 * @return Business The updated business.
 * @throws BusinessNotFoundException If the business is not found or the user does not have permission to update it.
 */
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

            if (!$business) {
                throw new BusinessNotFoundException($dto->id);
            }

            $this->authService->ensureCanUpdateBusiness($user, $business);

            return $this->businessRepo->update($business, $dto->toArray());
        });
    }
}
