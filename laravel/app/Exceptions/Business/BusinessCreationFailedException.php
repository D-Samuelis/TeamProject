<?php

namespace App\Exceptions\Business;

use App\Exceptions\Base\DomainException;

class BusinessCreationFailedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Failed to create business.');
    }
}
