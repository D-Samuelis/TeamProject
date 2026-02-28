<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBusinessDTO;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;

class CreateBusiness
{
    public function execute(CreateBusinessDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            $business = Business::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'state' => BusinessStateEnum::PENDING->value,
                'is_published' => false,
            ]);

            $business->users()->attach($userId, [
                'role' => BusinessRoleEnum::OWNER->value
            ]);
        });
    }
}
