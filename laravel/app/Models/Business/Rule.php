<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'priority',
        'title',
        'description',
        'valid_from',
        'valid_to',
        'rule_set',
        'delete_after',
    ];

    protected $casts = [
        'rule_set'     => 'array',
        'valid_from'   => 'datetime',
        'valid_to'     => 'datetime',
        'delete_after' => 'datetime',
        'priority'     => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
