<?php

namespace App\Application\UseCases;

use App\Application\DTO\AssignUserDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Notifications\EntityAssignedNotification;

class AssignUser
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly UserRepositoryInterface $userRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(AssignUserDTO $dto): void
    {
        $user = $this->userRepo->findByEmail($dto->email);

        $target = match ($dto->targetType) {
            'business' => $this->businessRepo->findForManagement($dto->businessId),
            'branch'   => $this->branchRepo->findWithinBusiness($dto->targetId, $dto->businessId),
            'service'  => $this->serviceRepo->findWithinBusiness($dto->targetId, $dto->businessId),
            default    => throw new \InvalidArgumentException("Invalid assignment type"),
        };

        $target->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $dto->role,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $user->notify(new EntityAssignedNotification($target, $dto->role));
    }
}
