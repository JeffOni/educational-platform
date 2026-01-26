<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentDiscussion extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_solution' => 'boolean',
    ];

    /**
     * Tarea asociada
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(LessonAssignment::class);
    }

    /**
     * Usuario que escribió el comentario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comentario padre (si es una respuesta)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AssignmentDiscussion::class, 'parent_id');
    }

    /**
     * Respuestas a este comentario
     */
    public function replies(): HasMany
    {
        return $this->hasMany(AssignmentDiscussion::class, 'parent_id')->with('user', 'replies');
    }

    /**
     * Likes de este comentario
     */
    public function likes()
    {
        return $this->hasMany(DiscussionLike::class);
    }

    /**
     * Verifica si es un comentario de nivel superior (no es respuesta)
     */
    public function isTopLevel(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Verifica si fue marcado como solución por el profesor
     */
    public function isSolution(): bool
    {
        return $this->is_solution;
    }

    /**
     * Verifica si el usuario dado ha dado like a este comentario
     */
    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Scope para obtener solo comentarios de nivel superior
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope para obtener comentarios marcados como solución
     */
    public function scopeSolutions($query)
    {
        return $query->where('is_solution', true);
    }

    /**
     * Incrementa el contador de likes
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Decrementa el contador de likes
     */
    public function decrementLikes(): void
    {
        $this->decrement('likes_count');
    }
}
