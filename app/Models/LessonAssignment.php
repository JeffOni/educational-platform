<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonAssignment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}
