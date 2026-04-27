<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Business;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Business\DTO\StoreBusinessDTO;
use App\Domain\Business\Enums\BusinessRoleEnum;

class StoreBusiness
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Executes the store business use case. It checks if the user has permission to create a business and then creates it with the provided data.
     * @param StoreBusinessDTO $dto The data transfer object containing the data for the new business.
     * @param User $user The user performing the create operation.
     * @return Business The created business.
     */
    public function execute(StoreBusinessDTO $dto, User $user): Business
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->save($dto->toArray());
            $this->businessRepo->attachUser($business, $user->id, BusinessRoleEnum::OWNER);
            return $business;
        });
    }
}
