<?php
 
namespace App\Models\Builders;
 
use Illuminate\Database\Eloquent\Builder;
 
class TaskBuilder extends Builder
{
    public function forUser(int $userId): self
    {
        return $this->where('user_id', $userId);
    }
 
    public function incomplete(): self
    {
        return $this->where('completed', false);
    }
 
    public function completed(): self
    {
        return $this->where('completed', true);
    }
 
    public function priority(string $priority): self
    {
        return $this->where('priority', $priority);
    }
 
    public function search(?string $keyword): self
    {
        if (empty($keyword)) {
            return $this;
        }
 
        return $this->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
 
    public function highPriority(): self
    {
        return $this->where('priority', 'high');
    }
 
    public function recentlyCompleted(int $days = 30): self
    {
        return $this->completed()
                    ->where('completed_at', '>=', now()->subDays($days));
    }
 
    public function createdInPeriod(int $days): self
    {
        return $this->where('created_at', '>=', now()->subDays($days));
    }
 
    public function orderByPriority(): self
    {
        return $this->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
    }
}