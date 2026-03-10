<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\AssignUserDTO;
use App\Repositories\Business\BusinessRepository;
use App\Repositories\User\UserRepository;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Notifications\EntityAssignedNotification;

class AssignUser
{
    public function __construct(
        private BusinessRepository $businessRepo,
        private UserRepository $userRepo,
    ) {}

    public function execute(AssignUserDTO $dto): void
    {
        $business = $this->businessRepo->findForManagement($dto->businessId);
        $user = $this->userRepo->findById($dto->userId);

        $this->businessRepo->attachUser($business, $user->id, BusinessRoleEnum::from($dto->role));

        $user->notify(new EntityAssignedNotification($business, $dto->role));
    }
}
