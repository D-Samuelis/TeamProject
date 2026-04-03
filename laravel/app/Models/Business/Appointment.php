<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

use App\Models\Auth\User;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'branch_service_id',
        'asset_id',
        'status',
        'duration',
        'date',
        'start_at',
    ];

    protected $casts = [
        'date'     => 'date',
        'start_at' => 'datetime',
        'duration' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function branchService(): BelongsTo
    {
        return $this->belongsTo(BranchService::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function endTime(): string
    {
        return Carbon::parse($this->start_at)
            ->addMinutes($this->duration)
            ->format('H:i');
    }

    // ── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        /**
         * Guard against branch_id and branch_service.branch_id mismatch.
         * Ensures the denormalized branch_id is always consistent.
         */
        static::creating(function (Appointment $appointment) {
            $branchService = BranchService::find($appointment->branch_service_id);

            if ($branchService && $branchService->branch_id !== $appointment->branch_id) {
                throw new \InvalidArgumentException(
                    "branch_id ({$appointment->branch_id}) does not match the branch_service's branch ({$branchService->branch_id})."
                );
            }
        });
    }
}
