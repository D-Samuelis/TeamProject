<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'base_duration_minutes',
        'base_price',
        'location_type',
        'is_active',
        'delete_after',
    ];

    protected $casts = [
        'base_price'            => 'decimal:2',
        'base_duration_minutes' => 'integer',
        'is_active'             => 'boolean',
        'delete_after'          => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Branch-level instances of this service template.
     * No direct user() relationship here — RBAC lives on BranchService.
     */
    public function branchServices(): HasMany
    {
        return $this->hasMany(BranchService::class, 'service_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_service', 'service_id', 'branch_id')
            ->withPivot([
                'custom_price',
                'custom_duration_minutes',
                'custom_description',
                'location_type',
                'is_enabled',
            ])
            ->withTimestamps();
    }
}
