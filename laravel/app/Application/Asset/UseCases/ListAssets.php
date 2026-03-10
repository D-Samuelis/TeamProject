<?php

namespace App\Application\Asset\UseCases;

use App\Application\Business\DTO\SearchDTO;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

class ListAssets
{
    public function __construct(
        private readonly AssetRepositoryInterface $assetRepo
    ) {}

    public function execute(array $filters = [])
    {
        $dto = SearchDTO::fromArray($filters);
        return $this->assetRepo->search($dto);
    }
}
