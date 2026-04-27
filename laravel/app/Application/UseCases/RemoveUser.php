<?php

namespace App\Application\UseCases;

use App\Models\Auth\User;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Notifications\EntityRemovedNotification;

use Exception;

class RemoveUser
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(int $businessId, int $userId, string $targetType, int $targetId): void
    {
        $target = match ($targetType) {
            'business' => $this->businessRepo->findForManagement($businessId),
            'branch'   => $this->branchRepo->findWithinBusiness($targetId, $businessId),
            'service'  => $this->serviceRepo->findWithinBusiness($targetId, $businessId),
        };

        $member = $target->users()->where('user_id', $userId)->first();

        if (!$member) throw new Exception('User not found in this scope.');

        if ($targetType === 'business' && $member->pivot->role === 'owner') {
            throw new Exception('Cannot remove the business owner.');
        }

        $target->users()->detach($userId);

        User::find($userId)->notify(new EntityRemovedNotification($target));
    }
}
