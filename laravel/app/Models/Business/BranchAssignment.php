<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class BranchAssignment extends Model
{
    protected $table = 'branch_user';

    protected $fillable = [
        'branch_id',
        'user_id',
        'role',
    ];
}