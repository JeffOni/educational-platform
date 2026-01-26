<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionLike extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function discussion()
    {
        return $this->belongsTo(AssignmentDiscussion::class, 'discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
