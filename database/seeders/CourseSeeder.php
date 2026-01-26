<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\LessonResource;
use App\Models\LessonAssignment;
use App\Models\User;
use App\Models\Subcategory;
use App\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::role('teacher')->first();
        $level = Level::first();

        $courses = [
            [
                'title' => 'Desarrollo Web con Laravel y Vue.js',
                'subtitle' => 'Aprende a crear aplicaciones web modernas desde cero',
                'description' => 'Un curso completo que te enseñará a construir aplicaciones web full-stack utilizando Laravel como backend y Vue.js como frontend.',
                'price' => 89.99,
                'subcategory' => 'full-stack',
                'sections' => [
                    [
                        'name' => 'Introducción a Laravel',
                        'lessons' => [
                            ['name' => 'Instalación y configuración', 'video_url' => 'https://www.youtube.com/watch?v=MFh0Fd7BsjE', 'duration' => 15],
                            ['name' => 'Estructura de un proyecto Laravel', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'duration' => 20],
                            ['name' => 'Rutas y controladores', 'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE', 'duration' => 25],
                        ]
                    ],
                    [
                        'name' => 'Vue.js Básico',
                        'lessons' => [
                            ['name' => 'Componentes y reactividad', 'video_url' => 'https://www.youtube.com/watch?v=nhBVL41-_Cw', 'duration' => 30],
                            ['name' => 'Props y eventos', 'video_url' => 'https://www.youtube.com/watch?v=bzlFvd0b65c', 'duration' => 22],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Python para Data Science',
                'subtitle' => 'Domina el análisis de datos con Python',
                'description' => 'Aprende Python desde cero y conviértete en un experto en análisis de datos utilizando pandas, numpy y matplotlib.',
                'price' => 79.99,
                'subcategory' => 'python',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Python',
                        'lessons' => [
                            ['name' => 'Variables y tipos de datos', 'video_url' => 'https://www.youtube.com/watch?v=rfscVS0vtbw', 'duration' => 18],
                            ['name' => 'Estructuras de control', 'video_url' => 'https://www.youtube.com/watch?v=DlkMs4ZHHr8', 'duration' => 20],
                            ['name' => 'Funciones y módulos', 'video_url' => 'https://www.youtube.com/watch?v=9Os0o3wzS_I', 'duration' => 25],
                        ]
                    ],
                    [
                        'name' => 'Pandas y NumPy',
                        'lessons' => [
                            ['name' => 'Introducción a Pandas', 'video_url' => 'https://www.youtube.com/watch?v=vmEHCJofslg', 'duration' => 35],
                            ['name' => 'Manipulación de DataFrames', 'video_url' => 'https://www.youtube.com/watch?v=PcvsOaixUh8', 'duration' => 40],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Machine Learning con Python',
                'subtitle' => 'Aprende los fundamentos del aprendizaje automático',
                'description' => 'Descubre cómo crear modelos de machine learning utilizando scikit-learn y TensorFlow.',
                'price' => 99.99,
                'subcategory' => 'machine-learning',
                'sections' => [
                    [
                        'name' => 'Introducción al ML',
                        'lessons' => [
                            ['name' => '¿Qué es Machine Learning?', 'video_url' => 'https://www.youtube.com/watch?v=I74ymkoNTnw', 'duration' => 20],
                            ['name' => 'Tipos de aprendizaje', 'video_url' => 'https://www.youtube.com/watch?v=xtOg44r6dsE', 'duration' => 18],
                        ]
                    ],
                    [
                        'name' => 'Algoritmos Supervisados',
                        'lessons' => [
                            ['name' => 'Regresión Lineal', 'video_url' => 'https://www.youtube.com/watch?v=CtsRRUddV2s', 'duration' => 30],
                            ['name' => 'Árboles de Decisión', 'video_url' => 'https://www.youtube.com/watch?v=_L39rN6gz7Y', 'duration' => 28],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'React desde Cero',
                'subtitle' => 'Construye aplicaciones web modernas con React',
                'description' => 'Aprende React desde los fundamentos hasta conceptos avanzados como hooks, context y Redux.',
                'price' => 84.99,
                'subcategory' => 'react',
                'sections' => [
                    [
                        'name' => 'Fundamentos de React',
                        'lessons' => [
                            ['name' => 'JSX y componentes', 'video_url' => 'https://www.youtube.com/watch?v=7DC-7g-kbPE', 'duration' => 25],
                            ['name' => 'Estado y props', 'video_url' => 'https://www.youtube.com/watch?v=IYvD9oBCuJI', 'duration' => 22],
                            ['name' => 'Eventos y formularios', 'video_url' => 'https://www.youtube.com/watch?v=SdzMBWT2CDQ', 'duration' => 20],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Diseño UX/UI con Figma',
                'subtitle' => 'Crea interfaces profesionales',
                'description' => 'Aprende a diseñar interfaces de usuario atractivas y funcionales utilizando Figma.',
                'price' => 69.99,
                'subcategory' => 'prototipado',
                'sections' => [
                    [
                        'name' => 'Introducción a Figma',
                        'lessons' => [
                            ['name' => 'Interfaz y herramientas', 'video_url' => 'https://www.youtube.com/watch?v=FTFaQWZBqQ8', 'duration' => 15],
                            ['name' => 'Creando tu primer diseño', 'video_url' => 'https://www.youtube.com/watch?v=PeGfX7W1mJk', 'duration' => 30],
                        ]
                    ],
                    [
                        'name' => 'Principios de UX',
                        'lessons' => [
                            ['name' => 'Jerarquía visual', 'video_url' => 'https://www.youtube.com/watch?v=qZWDJqY27bw', 'duration' => 20],
                            ['name' => 'Diseño responsive', 'video_url' => 'https://www.youtube.com/watch?v=srvUrASNj0s', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Spring Boot para Microservicios',
                'subtitle' => 'Arquitectura de Microservicios con Java',
                'description' => 'Aprende a crear microservicios escalables utilizando Spring Boot y las mejores prácticas de arquitectura.',
                'price' => 94.99,
                'subcategory' => 'java',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Spring Boot',
                        'lessons' => [
                            ['name' => 'Introducción a Spring Boot', 'video_url' => 'https://www.youtube.com/watch?v=9SGDpanrc8U', 'duration' => 20],
                            ['name' => 'Configuración y estructura', 'video_url' => 'https://www.youtube.com/watch?v=vtPkZShrvXQ', 'duration' => 25],
                            ['name' => 'REST APIs con Spring', 'video_url' => 'https://www.youtube.com/watch?v=UgX5lgv4uVM', 'duration' => 30],
                        ]
                    ],
                    [
                        'name' => 'Microservicios',
                        'lessons' => [
                            ['name' => 'Arquitectura de Microservicios', 'video_url' => 'https://www.youtube.com/watch?v=CdBtNQZH8a4', 'duration' => 28],
                            ['name' => 'Service Discovery', 'video_url' => 'https://www.youtube.com/watch?v=nUFPCyH2zxE', 'duration' => 22],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'JavaScript Moderno ES6+',
                'subtitle' => 'Domina JavaScript y sus características modernas',
                'description' => 'Aprende las características más recientes de JavaScript incluyendo async/await, destructuring, y más.',
                'price' => 74.99,
                'subcategory' => 'javascript',
                'sections' => [
                    [
                        'name' => 'ES6 Fundamentals',
                        'lessons' => [
                            ['name' => 'Let, const y arrow functions', 'video_url' => 'https://www.youtube.com/watch?v=sjyJBL5fkp8', 'duration' => 20],
                            ['name' => 'Template literals y destructuring', 'video_url' => 'https://www.youtube.com/watch?v=NIq3qLaHCIs', 'duration' => 18],
                            ['name' => 'Spread y rest operators', 'video_url' => 'https://www.youtube.com/watch?v=1INe_jCWq1Q', 'duration' => 15],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Node.js y Express',
                'subtitle' => 'Desarrolla APIs RESTful con Node.js',
                'description' => 'Aprende a crear servidores y APIs utilizando Node.js y el framework Express.',
                'price' => 79.99,
                'subcategory' => 'backend',
                'sections' => [
                    [
                        'name' => 'Introducción a Node.js',
                        'lessons' => [
                            ['name' => '¿Qué es Node.js?', 'video_url' => 'https://www.youtube.com/watch?v=i3OdKwuBjeM', 'duration' => 12],
                            ['name' => 'NPM y package.json', 'video_url' => 'https://www.youtube.com/watch?v=P3aKRdUyr0s', 'duration' => 18],
                            ['name' => 'Módulos en Node.js', 'video_url' => 'https://www.youtube.com/watch?v=xHLd36QoS4k', 'duration' => 20],
                        ]
                    ],
                    [
                        'name' => 'Express Framework',
                        'lessons' => [
                            ['name' => 'Primer servidor con Express', 'video_url' => 'https://www.youtube.com/watch?v=pKd0Rpw7O48', 'duration' => 25],
                            ['name' => 'Rutas y middleware', 'video_url' => 'https://www.youtube.com/watch?v=lY6icfhap2o', 'duration' => 30],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'MongoDB y Bases de Datos NoSQL',
                'subtitle' => 'Aprende bases de datos no relacionales',
                'description' => 'Domina MongoDB y aprende cuándo y cómo usar bases de datos NoSQL en tus proyectos.',
                'price' => 69.99,
                'subcategory' => 'mongodb',
                'sections' => [
                    [
                        'name' => 'Fundamentos de MongoDB',
                        'lessons' => [
                            ['name' => 'Instalación y configuración', 'video_url' => 'https://www.youtube.com/watch?v=-56x56UppqQ', 'duration' => 15],
                            ['name' => 'Documentos y colecciones', 'video_url' => 'https://www.youtube.com/watch?v=ofme2o29ngU', 'duration' => 20],
                            ['name' => 'CRUD Operations', 'video_url' => 'https://www.youtube.com/watch?v=VELru-FCWDM', 'duration' => 30],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Git y GitHub para Desarrolladores',
                'subtitle' => 'Control de versiones profesional',
                'description' => 'Aprende a usar Git y GitHub para gestionar tus proyectos y colaborar con otros desarrolladores.',
                'price' => 49.99,
                'subcategory' => 'devops',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Git',
                        'lessons' => [
                            ['name' => 'Introducción a Git', 'video_url' => 'https://www.youtube.com/watch?v=HiXLkL42tMU', 'duration' => 18],
                            ['name' => 'Commits y branches', 'video_url' => 'https://www.youtube.com/watch?v=9GKpbI1siow', 'duration' => 22],
                            ['name' => 'Merge y resolución de conflictos', 'video_url' => 'https://www.youtube.com/watch?v=xNVM5UxlFSA', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Docker para Desarrolladores',
                'subtitle' => 'Containerización de aplicaciones',
                'description' => 'Aprende a usar Docker para crear, desplegar y ejecutar aplicaciones en contenedores.',
                'price' => 89.99,
                'subcategory' => 'devops',
                'sections' => [
                    [
                        'name' => 'Introducción a Docker',
                        'lessons' => [
                            ['name' => '¿Qué es Docker?', 'video_url' => 'https://www.youtube.com/watch?v=CV_Uf3Dq-EU', 'duration' => 15],
                            ['name' => 'Instalación y primeros pasos', 'video_url' => 'https://www.youtube.com/watch?v=pTFZFxd4hOI', 'duration' => 20],
                            ['name' => 'Dockerfile y build', 'video_url' => 'https://www.youtube.com/watch?v=LQjaJINkQXY', 'duration' => 30],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'TypeScript para JavaScript Developers',
                'subtitle' => 'JavaScript con superpoderes',
                'description' => 'Aprende TypeScript y lleva tus habilidades de JavaScript al siguiente nivel con tipado estático.',
                'price' => 74.99,
                'subcategory' => 'javascript',
                'sections' => [
                    [
                        'name' => 'Introducción a TypeScript',
                        'lessons' => [
                            ['name' => 'Por qué TypeScript', 'video_url' => 'https://www.youtube.com/watch?v=BCg4U1FzODs', 'duration' => 12],
                            ['name' => 'Tipos básicos', 'video_url' => 'https://www.youtube.com/watch?v=d56mG7DezGs', 'duration' => 20],
                            ['name' => 'Interfaces y tipos', 'video_url' => 'https://www.youtube.com/watch?v=1jOFAMEEcvE', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Tailwind CSS - Diseño Moderno',
                'subtitle' => 'Utility-first CSS framework',
                'description' => 'Aprende a crear interfaces hermosas y responsivas usando Tailwind CSS.',
                'price' => 59.99,
                'subcategory' => 'frontend',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Tailwind',
                        'lessons' => [
                            ['name' => 'Instalación y configuración', 'video_url' => 'https://www.youtube.com/watch?v=pfaSUYaSgRo', 'duration' => 15],
                            ['name' => 'Utilidades básicas', 'video_url' => 'https://www.youtube.com/watch?v=mr15Xzb1Ook', 'duration' => 25],
                            ['name' => 'Responsive design', 'video_url' => 'https://www.youtube.com/watch?v=hX1zUdj4Dw4', 'duration' => 20],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'PostgreSQL Avanzado',
                'subtitle' => 'Domina la base de datos más poderosa',
                'description' => 'Aprende PostgreSQL desde consultas básicas hasta optimización de rendimiento.',
                'price' => 84.99,
                'subcategory' => 'postgresql',
                'sections' => [
                    [
                        'name' => 'SQL Fundamentals',
                        'lessons' => [
                            ['name' => 'SELECT y filtros', 'video_url' => 'https://www.youtube.com/watch?v=qw--VYLpxG4', 'duration' => 20],
                            ['name' => 'JOINS', 'video_url' => 'https://www.youtube.com/watch?v=9yeOJ0ZMUYw', 'duration' => 25],
                            ['name' => 'Agregaciones', 'video_url' => 'https://www.youtube.com/watch?v=4EajrPgJAk0', 'duration' => 22],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'SEO y Marketing Digital',
                'subtitle' => 'Posiciona tu sitio web en Google',
                'description' => 'Aprende las mejores prácticas de SEO y marketing digital para mejorar tu presencia online.',
                'price' => 69.99,
                'subcategory' => 'email-marketing',
                'sections' => [
                    [
                        'name' => 'Fundamentos de SEO',
                        'lessons' => [
                            ['name' => 'Introducción al SEO', 'video_url' => 'https://www.youtube.com/watch?v=hF515-0Tduk', 'duration' => 18],
                            ['name' => 'Palabras clave', 'video_url' => 'https://www.youtube.com/watch?v=Vm_fBTa6xEY', 'duration' => 20],
                            ['name' => 'SEO On-Page', 'video_url' => 'https://www.youtube.com/watch?v=aHbLXOd6pKg', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Ilustración Digital con Procreate',
                'subtitle' => 'Crea arte digital profesional',
                'description' => 'Aprende a crear ilustraciones digitales impresionantes usando Procreate en iPad.',
                'price' => 79.99,
                'subcategory' => 'ilustracion',
                'sections' => [
                    [
                        'name' => 'Introducción a Procreate',
                        'lessons' => [
                            ['name' => 'Interfaz y herramientas', 'video_url' => 'https://www.youtube.com/watch?v=zsUf1kNHVO4', 'duration' => 20],
                            ['name' => 'Pinceles y capas', 'video_url' => 'https://www.youtube.com/watch?v=b3mMkki49yQ', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Fotografía Digital Profesional',
                'subtitle' => 'Captura imágenes impresionantes',
                'description' => 'Aprende los fundamentos de la fotografía digital y cómo usar tu cámara en modo manual.',
                'price' => 89.99,
                'subcategory' => 'fotografia',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Fotografía',
                        'lessons' => [
                            ['name' => 'Exposición: ISO, apertura y velocidad', 'video_url' => 'https://www.youtube.com/watch?v=F8T94sdiNjc', 'duration' => 30],
                            ['name' => 'Composición', 'video_url' => 'https://www.youtube.com/watch?v=7ZVyNjKSr0M', 'duration' => 25],
                            ['name' => 'Iluminación', 'video_url' => 'https://www.youtube.com/watch?v=j_vMeKaPFqM', 'duration' => 28],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Excel Avanzado para Negocios',
                'subtitle' => 'Análisis de datos con Excel',
                'description' => 'Domina Excel y conviértete en un experto en análisis de datos empresariales.',
                'price' => 64.99,
                'subcategory' => 'startups',
                'sections' => [
                    [
                        'name' => 'Fórmulas Avanzadas',
                        'lessons' => [
                            ['name' => 'BUSCARV y BUSCARH', 'video_url' => 'https://www.youtube.com/watch?v=d3BYVQ6xIE4', 'duration' => 20],
                            ['name' => 'Tablas dinámicas', 'video_url' => 'https://www.youtube.com/watch?v=UsdedFoTA68', 'duration' => 30],
                            ['name' => 'Gráficos profesionales', 'video_url' => 'https://www.youtube.com/watch?v=DAQNLKJzEzU', 'duration' => 25],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Content Marketing y Copywriting',
                'subtitle' => 'Escribe contenido que vende',
                'description' => 'Aprende a crear contenido persuasivo y estrategias de content marketing efectivas.',
                'price' => 74.99,
                'subcategory' => 'content-marketing',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Copywriting',
                        'lessons' => [
                            ['name' => 'Estructuras persuasivas', 'video_url' => 'https://www.youtube.com/watch?v=Z9eCmRfonTk', 'duration' => 22],
                            ['name' => 'Headlines que convierten', 'video_url' => 'https://www.youtube.com/watch?v=q-6B7fTQfWY', 'duration' => 18],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Animación 3D con Blender',
                'subtitle' => 'Crea animaciones profesionales',
                'description' => 'Aprende a crear modelos y animaciones 3D usando Blender, el software gratuito más poderoso.',
                'price' => 94.99,
                'subcategory' => 'diseno-3d',
                'sections' => [
                    [
                        'name' => 'Introducción a Blender',
                        'lessons' => [
                            ['name' => 'Interfaz y navegación', 'video_url' => 'https://www.youtube.com/watch?v=nIoXOplUvAw', 'duration' => 25],
                            ['name' => 'Modelado básico', 'video_url' => 'https://www.youtube.com/watch?v=YW-EDZ67Yro', 'duration' => 35],
                            ['name' => 'Materiales y texturas', 'video_url' => 'https://www.youtube.com/watch?v=9QntZe0xFUo', 'duration' => 30],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Productividad y Gestión del Tiempo',
                'subtitle' => 'Maximiza tu eficiencia personal',
                'description' => 'Aprende técnicas y herramientas para ser más productivo y gestionar mejor tu tiempo.',
                'price' => 54.99,
                'subcategory' => 'productividad',
                'sections' => [
                    [
                        'name' => 'Fundamentos de Productividad',
                        'lessons' => [
                            ['name' => 'Método Pomodoro', 'video_url' => 'https://www.youtube.com/watch?v=VFW3Ld7JO0w', 'duration' => 15],
                            ['name' => 'GTD (Getting Things Done)', 'video_url' => 'https://www.youtube.com/watch?v=gCswMsONkwY', 'duration' => 20],
                            ['name' => 'Herramientas digitales', 'video_url' => 'https://www.youtube.com/watch?v=Pg0YzYSpJNM', 'duration' => 18],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($courses as $courseData) {
            $subcategory = Subcategory::where('slug', $courseData['subcategory'])->first();

            if (!$subcategory)
                continue;

            $course = Course::create([
                'title' => $courseData['title'],
                'subtitle' => $courseData['subtitle'],
                'description' => $courseData['description'],
                'slug' => Str::slug($courseData['title']),
                'price' => $courseData['price'],
                'status' => 3, // Publicado
                'user_id' => $teacher->id,
                'subcategory_id' => $subcategory->id,
                'level_id' => $level->id,
            ]);

            $sectionOrder = 1;
            foreach ($courseData['sections'] as $sectionData) {
                $section = Section::create([
                    'name' => $sectionData['name'],
                    'course_id' => $course->id,
                    'order' => $sectionOrder++,
                ]);

                $lessonOrder = 1;
                foreach ($sectionData['lessons'] as $lessonData) {
                    $lesson = Lesson::create([
                        'name' => $lessonData['name'],
                        'section_id' => $section->id,
                        'order' => $lessonOrder++,
                        'video_url' => $lessonData['video_url'],
                        'duration' => $lessonData['duration'],
                    ]);

                    // Agregar recursos a algunas lecciones
                    if (rand(0, 1)) {
                        LessonResource::create([
                            'lesson_id' => $lesson->id,
                            'name' => 'Código fuente - ' . $lessonData['name'],
                            'file_path' => 'resources/lesson_' . $lesson->id . '_code.zip',
                        ]);
                    }

                    // Agregar tareas a algunas lecciones
                    if ($lessonOrder > 2 && rand(0, 2) == 0) {
                        LessonAssignment::create([
                            'lesson_id' => $lesson->id,
                            'title' => 'Ejercicio práctico: ' . $lessonData['name'],
                            'description' => 'Completa los ejercicios prácticos relacionados con ' . $lessonData['name'] . '. Sube tu solución en formato ZIP.',
                            'due_date' => now()->addDays(7),
                            'max_points' => 100,
                        ]);
                    }
                }
            }
        }
    }
}
