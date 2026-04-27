<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BusinessAuthorizationService;
use App\Application\Business\DTO\UpdateBusinessDTO;

class UpdateBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BusinessAuthorizationService $authService
    ) {}

    /**
     * Executes the update business use case. It checks if the user has permission to update the business and then updates it with the provided data.
     * @param UpdateBusinessDTO $dto The data transfer object containing the business ID and the data to update.
     * @param User $user The user performing the update operation.
     * @return Business The updated business.
     */
    public function execute(UpdateBusinessDTO $dto, User $user): Business
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->id);
            $this->authService->ensureCanUpdateBusiness($user, $business);
            return $this->businessRepo->update($business, $dto->toArray());
        });
    }
}
