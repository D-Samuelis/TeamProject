<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\CreateBusinessDTO;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Business;
use App\Enums\BusinessRole;

class CreateBusiness
{
    public function execute(CreateBusinessDTO $dto, int $userId): void
    {
        DB::transaction(function () use ($dto, $userId) {
            $business = Business::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'is_published' => $dto->isPublished
            ]);

            $business->users()->attach($userId, [
                'role' => BusinessRole::OWNER->value
            ]);
        });
    }
}
