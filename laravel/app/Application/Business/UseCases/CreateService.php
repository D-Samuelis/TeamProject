<?php

namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use App\Domain\Business\Entities\Service;

use App\Domain\Business\Services\BusinessAuthorizationService;


class CreateService
{
    public function __construct(
        private BusinessAuthorizationService $authService
    ) {}

    public function execute(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {

            $business = Business::findOrFail($data['business_id']);

            $this->authService->ensureOwner($business, $userId);

            $service = Service::create([
                'business_id' => $business->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            if (!empty($data['branch_ids'])) {

                $validBranchIds = Branch::where('business_id', $business->id)
                    ->whereIn('id', $data['branch_ids'])
                    ->pluck('id')
                    ->toArray();

                $service->branches()->attach($validBranchIds);
            }
        });
    }
}
