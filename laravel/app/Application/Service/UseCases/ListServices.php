<?php

namespace App\Application\Service\UseCases;

use App\Application\Business\DTO\SearchDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

class ListServices
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(array $filters = [])
    {
        $dto = SearchDTO::fromArray($filters);
        return $this->serviceRepo->search($dto);
    }
}
