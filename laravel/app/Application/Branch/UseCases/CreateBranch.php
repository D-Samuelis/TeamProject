<?php

namespace App\Application\Branch\UseCases;

use App\Application\Branch\DTO\CreateBranchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Branch;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class CreateBranch
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateBranchDTO $dto, int $userId): Branch
    {
        return DB::transaction(function () use ($dto, $userId) {
            return $this->branchRepo->save($dto->toArray());
        });
    }
}