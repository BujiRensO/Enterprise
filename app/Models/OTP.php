<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'code',
        'expires_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    public static function generateForPayment(Payment $payment)
    {
        // Generate a 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Create OTP with 5-minute expiration
        return self::create([
            'payment_id' => $payment->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);
    }
} 