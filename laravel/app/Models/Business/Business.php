<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // ── Query Scopes ─────────────────────────────────────────────

    /**
     * Only published businesses.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Filter businesses by active branches.
     */
    public function scopeWithActiveBranches(Builder $query): Builder
    {
        return $query->with(['branches' => fn($q) => $q->where('is_active', true)]);
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
     * Filter businesses for a specific user (admin bypasses).
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if (! $user->isAdmin()) {
            return $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    /**
     * Apply search filters from SearchDTO.
     */
    public function scopeSearch(Builder $query, \App\Application\DTO\SearchDTO $dto): Builder
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas(
                        'branches',
                        fn($b) => $b->where('is_active', true)->where(
                            fn($q) => $q->where('name', 'like', "%{$keyword}%")
                                ->orWhere('city', 'like', "%{$keyword}%")
                        )
                    )
                    ->orWhereHas(
                        'services',
                        fn($s) => $s->where('is_active', true)->where(
                            fn($q) => $q->where('name', 'like', "%{$keyword}%")
                                ->orWhere('description', 'like', "%{$keyword}%")
                        )
                    );
            });
        }

        if ($dto->city) {
            $query->whereHas('branches', fn($q) => $q->where('is_active', true)->where('city', $dto->city));
        }

        if ($dto->maxPrice) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('base_price', '<=', $dto->maxPrice));
        }

        if ($dto->maxDuration) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->where('base_duration_minutes', '<=', $dto->maxDuration));
        }

        if (!empty($dto->locationTypes)) {
            $query->whereHas('services', fn($q) => $q->where('is_active', true)->whereIn('location_type', $dto->locationTypes));
        }

        return $query;
    }
}
