<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'name', 'type', 'address_line_1', 'address_line_2', 'city', 'postal_code', 'country', 'is_active'];

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
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function isManager(int $userId): bool
    {
        return $this->managers()->where('user_id', $userId)->exists();
    }
}
