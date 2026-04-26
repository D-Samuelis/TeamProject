<?php

namespace App\Exceptions\Business;

use App\Exceptions\Base\DomainException;

class InvalidBusinessScopeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('User is required for non-public business lists.');
    }
}