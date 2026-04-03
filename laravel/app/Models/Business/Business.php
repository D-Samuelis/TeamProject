<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Domain\Business\Enums\BusinessStateEnum;
use App\Models\Auth\User;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'state',
        'is_published',
        'delete_after',
    ];

    protected $casts = [
        'state'        => BusinessStateEnum::class,
        'is_published' => 'boolean',
        'delete_after' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function users()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Business-level service templates.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
