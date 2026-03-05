<?php

namespace App\Domain\Business\Enums;

enum BusinessStateEnum: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DELETED = 'deleted';
}
