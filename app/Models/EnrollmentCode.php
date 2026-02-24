<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EnrollmentCode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function enrollment()
    {
        return $this->hasOne(Enrollment::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->whereNull('used_by')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeUsed($query)
    {
        return $query->whereNotNull('used_by');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->whereNull('used_by');
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->is_active
            && is_null($this->used_by)
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && is_null($this->used_by);
    }

    public function isUsed(): bool
    {
        return !is_null($this->used_by);
    }

    public function markAsUsed(User $user): void
    {
        $this->update([
            'used_by' => $user->id,
            'used_at' => now(),
        ]);
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
