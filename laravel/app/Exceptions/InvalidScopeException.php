<?php

namespace App\Exceptions;

class InvalidScopeException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
