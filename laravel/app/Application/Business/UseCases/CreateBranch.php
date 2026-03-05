<?php
namespace App\Application\Business\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Business\Branch;

use App\Application\Business\DTO\CreateBranchDTO;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Business\Repositories\BranchRepositoryInterface;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;

class CreateBranch
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private BusinessRepositoryInterface $businessRepo,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(CreateBranchDTO $dto, int $userId): Branch
    {
        return DB::transaction(function () use ($dto, $userId) {
            $business = $this->businessRepo->findById($dto->business_id);
            if (!$business) {
                throw new \DomainException('Business not found.');
            }

            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new \DomainException('User not found.');
            }

            return $this->branchRepo->save($dto->toArray());
        });
    }
}