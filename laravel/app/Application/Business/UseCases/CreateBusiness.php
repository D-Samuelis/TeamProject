<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBusinessDTO;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;

class CreateBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(CreateBusinessDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {

            $business = new Business(
                id: null,
                name: $dto->name,
                description: $dto->description,
                state: BusinessStateEnum::PENDING,
                isPublished: false
            );

            $business = $this->businessRepo->save($business);

            $this->businessRepo->attachUser(
                $business->id,
                $userId,
                BusinessRoleEnum::OWNER
            );
        });
    }
}