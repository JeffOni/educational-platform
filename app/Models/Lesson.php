<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'video_type',
        'video_url',
        'duration',
        'section_id',
        'is_preview'
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'duration' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function users_completed()
    {
        return $this->belongsToMany(User::class);
    }

    public function questions()
    {
        return $this->hasMany(LessonQuestion::class);
    }

    public function resources()
    {
        return $this->hasMany(LessonResource::class);
    }

    public function assignments()
    {
        return $this->hasMany(LessonAssignment::class);
    }
}
