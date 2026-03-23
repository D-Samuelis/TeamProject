<?php

namespace App\Application\UseCases;

use App\Application\DTO\AssignUserDTO;
use App\Repositories\Business\BusinessRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Branch\BranchRepository; // Assuming you have these
use App\Repositories\Service\ServiceRepository;
use App\Notifications\EntityAssignedNotification;

class AssignUser
{
    public function __construct(
        private BusinessRepository $businessRepo,
        private UserRepository $userRepo,
        private BranchRepository $branchRepo,
        private ServiceRepository $serviceRepo
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
