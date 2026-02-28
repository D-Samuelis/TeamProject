<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'branch_id', 'service_id'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

}
