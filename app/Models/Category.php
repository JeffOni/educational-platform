<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function activeSubcategories()
    {
        return $this->subcategories()->where('is_active', true);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
