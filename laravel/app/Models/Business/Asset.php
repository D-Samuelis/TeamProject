<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'delete_after',
    ];

    protected $casts = [
        'delete_after' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Branch service instances that use this asset.
     */
    public function branchServices(): BelongsToMany
    {
        return $this->belongsToMany(
            BranchService::class,
            'asset_service',
            'asset_id',
            'branch_service_id'
        )
            ->withTimestamps();
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'asset_branch')
            ->withTimestamps();
    }

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
