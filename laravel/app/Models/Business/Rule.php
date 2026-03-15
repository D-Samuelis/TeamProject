<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'valid_from',
        'valid_to',
        'rule_set',
        'asset_id',
        'delete_after',
        'priority'
    ];

    protected $casts = [
        'rule_set'   => 'array',
        'valid_from' => 'datetime',
        'valid_to'   => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
