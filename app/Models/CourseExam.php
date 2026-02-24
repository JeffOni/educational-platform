<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseExam extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'max_attempts' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    public function userAttempts(User $user)
    {
        return $this->attempts()->where('user_id', $user->id);
    }

    public function userHasPassingAttempt(User $user): bool
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('passed', true)
            ->exists();
    }

    public function userCanAttempt(User $user): bool
    {
        $attemptCount = $this->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();

        return $attemptCount < $this->max_attempts;
    }
}
