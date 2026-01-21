<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function activeCategories()
    {
        return $this->categories()->where('is_active', true);
    }
}
