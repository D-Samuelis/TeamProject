<?php

namespace App\Application\Service\UseCases;

use App\Application\DTO\SearchDTO;
use App\Application\DTO\ServiceSearchDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Business;
use Illuminate\Support\Collection;

class ListServices
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(ServiceSearchDTO $dto, ?User $user = null) {
        if ($user && !$user->isAdmin()) {
            $dto = new ServiceSearchDTO(
                serviceName: $dto->serviceName,
                description: $dto->description,
                priceMin: $dto->priceMin,
                priceMax: $dto->priceMax,
                durationMin: $dto->durationMin,
                durationMax: $dto->durationMax,
                statuses: $dto->statuses,
                businessId: $dto->businessId,
                userId: null,
                role: $dto->role,
                perPage: $dto->perPage,
                page: $dto->page,
            );
        }

        return $this->serviceRepo->search($dto, $user);
    }
}
