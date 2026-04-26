<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'location_type',
        'is_active'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }
}
