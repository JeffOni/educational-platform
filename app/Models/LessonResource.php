<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonResource extends Model
{
    protected $guarded = ['id'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
