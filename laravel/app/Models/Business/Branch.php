<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'latitude',
        'longitude',
        'is_active',
        'delete_after',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
        'delete_after' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Branch-level service instances.
     */
    public function branchServices(): HasMany
    {
        return $this->hasMany(BranchService::class);
    }

    /**
     * Convenience: enabled branch service instances only.
     */
    public function enabledServices(): HasMany
    {
        return $this->hasMany(BranchService::class)->where('is_enabled', true);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'asset_branch')
            ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }
}
