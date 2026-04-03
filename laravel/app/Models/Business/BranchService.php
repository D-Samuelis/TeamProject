<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchService extends Model
{
    protected $table = 'branch_service';

    protected $fillable = [
        'branch_id',
        'service_id',
        'custom_price',
        'custom_duration_minutes',
        'custom_description',
        'location_type',
        'is_enabled',
    ];

    protected $casts = [
        'custom_price' => 'decimal:2',
        'custom_duration_minutes' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function users()

    {

        return $this->morphToMany(\App\Models\Auth\User::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'asset_service', 'branch_service_id', 'asset_id')
            ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'branch_service_id');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->custom_name ?? $this->service?->name,
        );
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->custom_price ?? $this->service?->base_price,
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->custom_duration_minutes ?? $this->service?->base_duration_minutes,
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->custom_description ?? $this->service?->description,
        );
    }

    protected function locationType(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->location_type ?? $this->service?->location_type,
        );
    }
}
