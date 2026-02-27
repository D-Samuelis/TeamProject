<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Entities\Business;

class ListBusinesses
{
    public function execute()
    {
        return Business::with([
            'branches',
            'services.branches'
        ])->get();
    }
}
