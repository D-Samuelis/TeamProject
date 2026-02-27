<?php

namespace App\Domain\Business\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\User\Entities\User;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'name', 'description', 'duration_minutes', 'price', 'is_online', 'location_type', 'is_active'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }
}
