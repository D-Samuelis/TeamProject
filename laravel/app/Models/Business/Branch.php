<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'country',
        'is_active'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class);
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }
}
