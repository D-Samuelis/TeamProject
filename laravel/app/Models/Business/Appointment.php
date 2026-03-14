<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Auth\User;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function endTime(): string
    {
        return \Carbon\Carbon::parse($this->start_at)
            ->addMinutes($this->duration)
            ->format('H:i');
    }
}
