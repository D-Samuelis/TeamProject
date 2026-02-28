<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBusinessDTO;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;

class CreateBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(CreateBusinessDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            $business = $this->businessRepo->create([
                'name' => $dto->name,
                'description' => $dto->description,
                'state' => BusinessStateEnum::PENDING->value,
                'is_published' => false,
            ]);

            $this->businessRepo->attachOwner($business->id, $userId);

            return $business;
        });
    }
}
