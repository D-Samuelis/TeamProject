<?php

namespace App\Application\Service\UseCases;

use App\Application\DTO\SearchDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;
use Illuminate\Support\Collection;

class ListServices
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(
        ?User $user = null,
        ?Business $business = null,
        string $scope = 'active',
        array $filters = []
    ): Collection {
        if ($scope === 'public') {
            $dto = SearchDTO::fromArray($filters);
            return $this->serviceRepo->search($dto)->getCollection();
        }

        if (!$user) {
            throw new \InvalidArgumentException('User is required for non-public service lists.');
        }

        return $this->serviceRepo->listForUser($user, $business, $scope);
    }
}
