<?php

namespace App\Exceptions\Business;

use App\Exceptions\Base\DomainException;

class BusinessNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Business #{$id} was not found.");
    }
}
