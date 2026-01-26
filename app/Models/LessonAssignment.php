<?php

namespace App\Models;

use App\Enums\AssignmentType;
use Illuminate\Database\Eloquent\Model;

class LessonAssignment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'datetime',
        'submission_type' => AssignmentType::class,
        'allowed_file_types' => 'array',
        'config' => 'array',
        'requires_text' => 'boolean',
        'enable_comments' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }

    public function discussions()
    {
        return $this->hasMany(AssignmentDiscussion::class, 'assignment_id');
    }

    /**
     * Verifica si el tipo de entrega requiere archivos
     */
    public function requiresFile(): bool
    {
        return $this->submission_type->requiresFile();
    }

    /**
     * Verifica si el tipo de entrega requiere texto
     */
    public function requiresTextSubmission(): bool
    {
        return $this->submission_type->requiresText();
    }

    /**
     * Verifica si el tipo de entrega requiere enlace
     */
    public function requiresLinkSubmission(): bool
    {
        return $this->submission_type->requiresLink();
    }

    /**
     * Verifica si permite comentarios/discusiones
     */
    public function allowsDiscussions(): bool
    {
        return $this->submission_type->allowsComments() && $this->enable_comments;
    }

    /**
     * Obtiene los tipos de archivo permitidos formateados
     */
    public function getAllowedFileTypesString(): string
    {
        if (empty($this->allowed_file_types)) {
            return 'Todos los tipos';
        }

        return implode(', ', $this->allowed_file_types);
    }

    /**
     * Verifica si un tipo de archivo está permitido
     */
    public function isFileTypeAllowed(string $extension): bool
    {
        if (empty($this->allowed_file_types)) {
            return true; // Si no hay restricciones, todo está permitido
        }

        return in_array(strtolower($extension), array_map('strtolower', $this->allowed_file_types));
    }
}
