<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    public function branchServices(): HasMany
    {
        return $this->hasMany(BranchService::class);
    }

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

    // ── Query Scopes ─────────────────────────────────────────────

    /**
     * Only active branches.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Include soft-deleted records optionally.
     */
    public function scopeTrashedScope(Builder $query, string $scope = 'active'): Builder
    {
        return match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all'     => $query->withTrashed(),
            default   => $query,
        };
    }

    /**
     * Only branches belonging to published businesses.
     */
    public function scopePublishedBusiness(Builder $query): Builder
    {
        return $query->whereHas('business', fn($q) => $q->where('is_published', true));
    }

    /**
     * Filter branches for a specific user (admin bypasses).
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) return $query;

        return $query->where(function ($q) use ($user) {
            $q->whereHas('business.users', fn($sub) => $sub->where('user_id', $user->id))
                ->orWhereHas('users', fn($sub) => $sub->where('user_id', $user->id))
                ->orWhereHas('branchServices.users', fn($sub) => $sub->where('user_id', $user->id));
        });
    }

    /**
     * Apply search filters from a SearchDTO.
     */
    public function scopeSearch(Builder $query, \App\Application\DTO\SearchDTO $dto): Builder
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhereHas('business', fn($b) => $b->where('name', 'like', "%{$keyword}%"));
            });
        }

        if ($dto->city) {
            $query->where('city', $dto->city);
        }

        if ($dto->businessId) {
            $query->where('business_id', $dto->businessId);
        }

        if (!empty($dto->locationTypes)) {
            $query->whereIn('type', $dto->locationTypes);
        }

        return $query;
    }
}
