<?php

namespace App\Application\Asset\UseCases;

use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;

class GetAsset
{
    public function __construct(private readonly AssetRepositoryInterface $assetRepo) {}

    public function execute(int $assetId, ?User $user = null): Asset
    {
        return $this->assetRepo->findForManagement($assetId);
    }
}
