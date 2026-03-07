<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBusinessDTO;
use Illuminate\Support\Facades\DB;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Business\Business;

class CreateBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(CreateBusinessDTO $dto, int $userId): Business
    {
        return DB::transaction(function () use ($dto, $userId) {
            $business = $this->businessRepo->save($dto->toArray());

            $this->businessRepo->attachUser(
                $business,
                $userId,
                BusinessRoleEnum::OWNER
            );

            return $business;
        });
    }
}
