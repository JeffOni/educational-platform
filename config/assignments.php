<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tipos de Entrega de Tareas
    |--------------------------------------------------------------------------
    |
    | Define los tipos de entrega permitidos para las tareas.
    | Valores: 'file', 'text', 'link', 'file_and_text', 'forum'
    |
    */

    'submission_types' => [
        'file' => 'Archivo',
        'text' => 'Texto',
        'link' => 'Enlace',
        'file_and_text' => 'Archivo y Texto',
        'forum' => 'Foro/Discusión',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dominios Permitidos para Enlaces
    |--------------------------------------------------------------------------
    |
    | Whitelist de dominios seguros para entregas tipo 'link'.
    | Solo se permiten enlaces de estos dominios.
    |
    */

    'allowed_domains' => [
        'github.com',
        'gitlab.com',
        'bitbucket.org',
        'youtube.com',
        'youtu.be',
        'vimeo.com',
        'drive.google.com',
        'docs.google.com',
        'dropbox.com',
        'onedrive.live.com',
        'sharepoint.com',
        'figma.com',
        'notion.so',
        'codepen.io',
        'jsfiddle.net',
        'codesandbox.io',
        'replit.com',
        'stackblitz.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Archivo Permitidos
    |--------------------------------------------------------------------------
    |
    | Define las extensiones y tipos MIME permitidos por categoría.
    |
    */

    'allowed_file_types' => [
        'documents' => [
            'extensions' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'],
            'mime_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
                'application/rtf',
                'application/vnd.oasis.opendocument.text',
            ],
        ],
        'spreadsheets' => [
            'extensions' => ['xls', 'xlsx', 'csv', 'ods'],
            'mime_types' => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'application/vnd.oasis.opendocument.spreadsheet',
            ],
        ],
        'presentations' => [
            'extensions' => ['ppt', 'pptx', 'odp'],
            'mime_types' => [
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.presentation',
            ],
        ],
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'],
            'mime_types' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/svg+xml',
                'image/webp',
                'image/bmp',
            ],
        ],
        'code' => [
            'extensions' => ['zip', 'rar', '7z', 'tar', 'gz', 'py', 'js', 'java', 'cpp', 'c', 'php', 'html', 'css', 'json', 'xml'],
            'mime_types' => [
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/x-tar',
                'application/gzip',
                'text/x-python',
                'application/javascript',
                'text/x-java-source',
                'text/x-c++src',
                'text/x-c',
                'application/x-httpd-php',
                'text/html',
                'text/css',
                'application/json',
                'application/xml',
            ],
        ],
        'video' => [
            'extensions' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            'mime_types' => [
                'video/mp4',
                'video/x-msvideo',
                'video/quicktime',
                'video/x-ms-wmv',
                'video/x-flv',
                'video/x-matroska',
                'video/webm',
            ],
        ],
        'audio' => [
            'extensions' => ['mp3', 'wav', 'ogg', 'flac', 'm4a'],
            'mime_types' => [
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'audio/flac',
                'audio/mp4',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tamaño Máximo de Archivo
    |--------------------------------------------------------------------------
    |
    | Tamaño máximo en KB para archivos subidos.
    | Por defecto: 10MB = 10240KB
    |
    */

    'max_file_size' => env('ASSIGNMENT_MAX_FILE_SIZE', 10240), // 10MB en KB

    /*
    |--------------------------------------------------------------------------
    | Número Máximo de Archivos
    |--------------------------------------------------------------------------
    |
    | Número máximo de archivos que un estudiante puede subir por tarea.
    |
    */

    'max_files' => env('ASSIGNMENT_MAX_FILES', 5),

    /*
    |--------------------------------------------------------------------------
    | Longitud Máxima de Enlace
    |--------------------------------------------------------------------------
    |
    | Número máximo de caracteres permitidos en un enlace.
    |
    */

    'max_link_length' => 2048,

    /*
    |--------------------------------------------------------------------------
    | Longitud Máxima de Texto
    |--------------------------------------------------------------------------
    |
    | Número máximo de caracteres para respuestas de texto.
    |
    */

    'max_text_length' => 50000, // ~50KB de texto

];
