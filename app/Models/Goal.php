<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'status',
        'type',
    ];

    protected $casts = [
        'target_date' => 'date',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute()
    {
        return min(100, round(($this->current_amount / $this->target_amount) * 100));
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysRemainingAttribute()
    {
        return max(0, now()->diffInDays($this->target_date, false));
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'blue',
        };
    }

    public function getProgressBarColorAttribute()
    {
        return match(true) {
            $this->progress_percentage >= 100 => 'bg-green-600',
            $this->progress_percentage >= 75 => 'bg-blue-600',
            $this->progress_percentage >= 50 => 'bg-yellow-500',
            $this->progress_percentage >= 25 => 'bg-orange-500',
            default => 'bg-red-500',
        };
    }
}
