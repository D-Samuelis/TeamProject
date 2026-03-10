<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Service;

class GetService
{
    public function __construct(private readonly ServiceRepositoryInterface $serviceRepo) {}

    public function execute(int $serviceId, ?User $user = null): Service
    {
        if ($user) {
            return $this->serviceRepo->findForManagement($serviceId);
        }

        return $this->serviceRepo->findActive($serviceId);
    }
}
