<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmission extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'submission_files' => 'array',
        'is_draft' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(LessonAssignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica si la entrega está completa (no es borrador)
     */
    public function isSubmitted(): bool
    {
        return !$this->is_draft && $this->submitted_at !== null;
    }

    /**
     * Verifica si la entrega ha sido calificada
     */
    public function isGraded(): bool
    {
        return $this->graded_at !== null && $this->grade !== null;
    }

    /**
     * Obtiene el estado de la entrega
     */
    public function getStatus(): string
    {
        if ($this->is_draft) {
            return 'Borrador';
        }

        if ($this->isGraded()) {
            return 'Calificado';
        }

        if ($this->isSubmitted()) {
            return 'Entregado';
        }

        return 'Pendiente';
    }

    /**
     * Obtiene todos los archivos de la entrega
     */
    public function getFiles(): array
    {
        $files = [];

        // Archivo único (retrocompatibilidad)
        if ($this->file_path) {
            $files[] = [
                'path' => $this->file_path,
                'url' => Storage::url($this->file_path),
                'name' => basename($this->file_path),
            ];
        }

        // Archivos múltiples
        if (!empty($this->submission_files)) {
            foreach ($this->submission_files as $file) {
                $files[] = [
                    'path' => $file,
                    'url' => Storage::url($file),
                    'name' => basename($file),
                ];
            }
        }

        return $files;
    }

    /**
     * Elimina todos los archivos asociados
     */
    public function deleteFiles(): void
    {
        // Eliminar archivo único
        if ($this->file_path) {
            Storage::delete($this->file_path);
        }

        // Eliminar archivos múltiples
        if (!empty($this->submission_files)) {
            foreach ($this->submission_files as $file) {
                Storage::delete($file);
            }
        }
    }

    /**
     * Verifica si la entrega está retrasada
     */
    public function isLate(): bool
    {
        if (!$this->submitted_at || !$this->assignment->due_date) {
            return false;
        }

        return $this->submitted_at->isAfter($this->assignment->due_date);
    }

    /**
     * Calcula los días de retraso
     */
    public function getDaysLate(): int
    {
        if (!$this->isLate()) {
            return 0;
        }

        return $this->submitted_at->diffInDays($this->assignment->due_date);
    }
}
