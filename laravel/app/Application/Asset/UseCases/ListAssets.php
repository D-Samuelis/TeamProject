<?php

namespace App\Application\Asset\UseCases;

use App\Application\DTO\AssetSearchDTO;
use App\Application\DTO\SearchDTO;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;

class ListAssets
{
    public function __construct(
        private readonly AssetRepositoryInterface $assetRepo,
    ) {}

    public function execute(AssetSearchDTO $dto, ?User $user = null)
    {
        return $this->assetRepo->search($dto, $user);
    }
}
