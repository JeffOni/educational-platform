<?php

namespace App\Enums;

enum AssignmentType: string
{
    case FILE = 'file';
    case TEXT = 'text';
    case LINK = 'link';
    case FILE_AND_TEXT = 'file_and_text';
    case FORUM = 'forum';

    public function label(): string
    {
        return match ($this) {
            self::FILE => 'Archivo',
            self::TEXT => 'Texto',
            self::LINK => 'Enlace Externo',
            self::FILE_AND_TEXT => 'Archivo y Texto',
            self::FORUM => 'Foro/Discusión',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FILE => 'Los estudiantes deben subir uno o más archivos',
            self::TEXT => 'Los estudiantes responden con texto (editor enriquecido)',
            self::LINK => 'Los estudiantes envían un enlace externo (GitHub, YouTube, etc.)',
            self::FILE_AND_TEXT => 'Los estudiantes deben subir archivo(s) y escribir una descripción',
            self::FORUM => 'Discusión abierta entre estudiantes con respuestas y comentarios',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FILE => 'file-up',
            self::TEXT => 'file-text',
            self::LINK => 'link',
            self::FILE_AND_TEXT => 'file-plus',
            self::FORUM => 'message-square',
        };
    }

    public function requiresFile(): bool
    {
        return in_array($this, [self::FILE, self::FILE_AND_TEXT]);
    }

    public function requiresText(): bool
    {
        return in_array($this, [self::TEXT, self::FILE_AND_TEXT, self::FORUM]);
    }

    public function requiresLink(): bool
    {
        return $this === self::LINK;
    }

    public function allowsComments(): bool
    {
        return $this === self::FORUM;
    }
}
