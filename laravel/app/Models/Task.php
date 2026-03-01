<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'completed',
        'completed_at',
    ];
 
    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
 
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }
 
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }
 
    public function markComplete(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }
}