<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Business;
use App\Enums\BusinessRole;

class CreateBusiness
{
    public function execute(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            $business = Business::create($data);

            $business->users()->attach($userId, [
                'role' => BusinessRole::OWNER->value
            ]);
        });
    }
}
