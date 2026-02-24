<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    AlertCircle,
    CheckCheck,
    CheckCircle,
    ChevronRight,
    Circle,
    ClipboardList,
    Clock,
    CornerDownRight,
    Download,
    FileText,
    MessageCircle,
    PlayCircle,
    Send,
    Upload,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface User {
    id: number;
    name: string;
}

interface Answer {
    id: number;
    answer: string;
    user: User;
    created_at: string;
}

interface Question {
    id: number;
    question: string;
    user: User;
    answers: Answer[];
    created_at: string;
}

interface Resource {
    id: number;
    name: string;
    file_type: string;
    file_size: number;
    file_path: string;
}

interface Submission {
    id: number;
    content: string;
    file_path: string | null;
    file_name: string | null;
    grade: number | null;
    feedback: string | null;
    created_at: string;
    updated_at: string;
}

interface Assignment {
    id: number;
    title: string;
    description: string;
    due_date: string | null;
    max_points: number;
    submissions: Submission[];
}

interface Lesson {
    id: number;
    name: string;
    video_type: 'youtube' | 'vimeo' | 'file' | 'spaces';
    video_url: string;
    duration: number | null;
    is_preview: boolean;
    resources: Resource[];
    assignments: Assignment[];
}

interface Section {
    id: number;
    name: string;
    lessons: Lesson[];
}

interface Course {
    id: number;
    title: string;
    subtitle: string;
    sections: Section[];
    teacher: {
        name: string;
    };
}

interface Props {
    course: Course;
    lesson: Lesson;
    questions: Question[];
    isCompleted: boolean;
    progress: number;
}

const props = defineProps<Props>();

const processing = ref(false);
const showQuestions = ref(false);
const showResources = ref(true);
const showAssignments = ref(true);

const questionForm = useForm({
    question: '',
});

const submissionForms = ref<Record<number, any>>({});

// Inicializar formularios de envío para cada tarea
props.lesson.assignments.forEach((assignment) => {
    const existingSubmission = assignment.submissions[0];

    submissionForms.value[assignment.id] = useForm({
        content: existingSubmission?.content || '',
        file: null as File | null,
    });
});

// Generar URL del video embebido
const embedUrl = computed(() => {
    const url = props.lesson.video_url;
    const type = props.lesson.video_type;

    if (type === 'youtube') {
        const videoId = url.includes('watch?v=')
            ? url.split('watch?v=')[1]?.split('&')[0]
            : url.split('youtu.be/')[1]?.split('?')[0] || url;
        return `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1`;
    }

    if (type === 'vimeo') {
        const videoId = url.split('/').pop();
        return `https://player.vimeo.com/video/${videoId}`;
    }

    return url;
});

// Obtener todas las lecciones en orden
const allLessons = computed(() => {
    const lessons: Array<{
        lesson: Lesson;
        sectionName: string;
        sectionId: number;
    }> = [];
    props.course.sections.forEach((section) => {
        section.lessons.forEach((lesson) => {
            lessons.push({
                lesson,
                sectionName: section.name,
                sectionId: section.id,
            });
        });
    });
    return lessons;
});

// Encontrar la lección actual y siguiente
const currentIndex = computed(() =>
    allLessons.value.findIndex((item) => item.lesson.id === props.lesson.id),
);

const nextLesson = computed(() => {
    if (currentIndex.value < allLessons.value.length - 1) {
        return allLessons.value[currentIndex.value + 1];
    }
    return null;
});

const previousLesson = computed(() => {
    if (currentIndex.value > 0) {
        return allLessons.value[currentIndex.value - 1];
    }
    return null;
});

const toggleComplete = () => {
    processing.value = true;
    router.post(
        `/student/lessons/${props.lesson.id}/toggle-complete`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = false;
            },
        },
    );
};

const goToLesson = (courseId: number, lessonId: number) => {
    router.visit(`/student/courses/${courseId}/lessons/${lessonId}`);
};

const formatDuration = (seconds: number | null) => {
    if (!seconds) return 'N/A';
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
};

const submitQuestion = () => {
    questionForm.post(`/student/lessons/${props.lesson.id}/questions`, {
        preserveScroll: true,
        onSuccess: () => {
            questionForm.reset();
        },
    });
};

const page = usePage();
const currentUserId = computed(() => (page.props.auth as any)?.user?.id);

const replyingTo = ref<number | null>(null);
const answerForms = ref<Record<number, ReturnType<typeof useForm>>>({});

const toggleReply = (questionId: number) => {
    if (replyingTo.value === questionId) {
        replyingTo.value = null;
        return;
    }
    replyingTo.value = questionId;
    if (!answerForms.value[questionId]) {
        answerForms.value[questionId] = useForm({ answer: '' });
    }
};

const submitAnswer = (questionId: number) => {
    const form = answerForms.value[questionId];
    if (!form) return;
    form.post(`/student/questions/${questionId}/answer`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            replyingTo.value = null;
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / 1048576).toFixed(2) + ' MB';
};

const downloadResource = (resourceId: number) => {
    window.location.href = `/student/resources/${resourceId}/download`;
};

const handleSubmissionFile = (assignmentId: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        submissionForms.value[assignmentId].file = target.files[0];
    }
};

const submitAssignment = (assignmentId: number) => {
    const form = submissionForms.value[assignmentId];
    form.post(`/student/assignments/${assignmentId}/submit`, {
        preserveScroll: true,
        onSuccess: () => {
            form.file = null;
        },
    });
};

const getSubmission = (assignment: Assignment) => {
    return assignment.submissions[0] || null;
};

const isAssignmentOverdue = (assignment: Assignment) => {
    if (!assignment.due_date) return false;
    return new Date(assignment.due_date) < new Date();
};

const canEditSubmission = (submission: Submission | null) => {
    return !submission || submission.grade === null;
};
</script>

<template>
    <Head :title="lesson.name" />

    <StudentLayout>
        <!-- Video Player -->
        <div class="mt-2 overflow-hidden rounded-xl bg-black">
            <div class="mx-auto max-w-5xl">
                <div class="relative aspect-video">
                    <!-- YouTube/Vimeo Embed -->
                    <iframe
                        v-if="
                            lesson.video_type === 'youtube' ||
                            lesson.video_type === 'vimeo'
                        "
                        :src="embedUrl"
                        class="h-full w-full"
                        frameborder="0"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                        "
                        allowfullscreen
                    />

                    <!-- Video HTML5 para archivos locales -->
                    <video
                        v-else
                        :src="embedUrl"
                        class="h-full w-full"
                        controls
                        controlsList="nodownload"
                    >
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Contenido Principal -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Título y Acciones -->
                <div>
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">
                                {{ lesson.name }}
                            </h1>
                            <p class="mt-2 text-gray-600">
                                {{ course.title }}
                            </p>
                        </div>

                        <button
                            @click="toggleComplete"
                            :disabled="processing"
                            class="flex shrink-0 items-center gap-2 rounded-lg px-6 py-3 font-semibold transition-all duration-200"
                            :class="
                                isCompleted
                                    ? 'bg-green-600 text-white hover:bg-green-700'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            "
                        >
                            <CheckCircle v-if="isCompleted" class="h-5 w-5" />
                            <Circle v-else class="h-5 w-5" />
                            {{
                                isCompleted
                                    ? 'Completada'
                                    : 'Marcar como completada'
                            }}
                        </button>
                    </div>

                    <!-- Duración -->
                    <div
                        v-if="lesson.duration"
                        class="flex items-center gap-2 text-gray-600"
                    >
                        <Clock class="h-4 w-4" />
                        <span>{{ formatDuration(lesson.duration) }}</span>
                    </div>
                </div>

                <!-- Navegación entre lecciones -->
                <div class="flex items-center justify-between border-t pt-6">
                    <button
                        v-if="previousLesson"
                        @click="goToLesson(course.id, previousLesson.lesson.id)"
                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <ChevronRight class="h-5 w-5 rotate-180" />
                        <span>Lección anterior</span>
                    </button>
                    <div v-else></div>

                    <button
                        v-if="nextLesson"
                        @click="goToLesson(course.id, nextLesson.lesson.id)"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white transition hover:bg-indigo-700"
                    >
                        <span>Siguiente lección</span>
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>

                <!-- Recursos de la Lección -->
                <div
                    v-if="lesson.resources && lesson.resources.length > 0"
                    class="border-t pt-6"
                >
                    <div
                        class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3"
                    >
                        <h2
                            class="flex items-center gap-3 text-xl font-bold text-gray-900"
                        >
                            <FileText class="h-5 w-5 text-indigo-600" />
                            Recursos de la Lección
                        </h2>
                        <button
                            @click="showResources = !showResources"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                        >
                            {{ showResources ? 'Ocultar' : 'Mostrar' }}
                            ({{ lesson.resources.length }})
                        </button>
                    </div>

                    <div v-show="showResources" class="space-y-3">
                        <div
                            v-for="resource in lesson.resources"
                            :key="resource.id"
                            class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 transition hover:border-indigo-300"
                        >
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-indigo-50 p-2">
                                    <FileText class="h-5 w-5 text-indigo-600" />
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">
                                        {{ resource.name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ resource.file_type }} &bull;
                                        {{ formatFileSize(resource.file_size) }}
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="downloadResource(resource.id)"
                                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-700"
                            >
                                <Download class="h-4 w-4" />
                                Descargar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tareas de la Lección -->
                <div
                    v-if="lesson.assignments && lesson.assignments.length > 0"
                    class="border-t pt-6"
                >
                    <div
                        class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3"
                    >
                        <h2
                            class="flex items-center gap-3 text-xl font-bold text-gray-900"
                        >
                            <ClipboardList class="h-5 w-5 text-indigo-600" />
                            Tareas
                        </h2>
                        <button
                            @click="showAssignments = !showAssignments"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                        >
                            {{ showAssignments ? 'Ocultar' : 'Mostrar' }}
                            ({{ lesson.assignments.length }})
                        </button>
                    </div>

                    <div v-show="showAssignments" class="space-y-6">
                        <div
                            v-for="assignment in lesson.assignments"
                            :key="assignment.id"
                            class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                        >
                            <!-- Encabezado de la tarea -->
                            <div class="border-b bg-gray-50 p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3
                                            class="text-xl font-bold text-gray-900"
                                        >
                                            {{ assignment.title }}
                                        </h3>
                                        <p class="mt-2 text-gray-700">
                                            {{ assignment.description }}
                                        </p>

                                        <div
                                            class="mt-3 flex items-center gap-4 text-sm"
                                        >
                                            <span class="text-gray-600">
                                                <strong>Puntos:</strong>
                                                {{ assignment.max_points }}
                                            </span>
                                            <span
                                                v-if="assignment.due_date"
                                                class="flex items-center gap-1"
                                                :class="
                                                    isAssignmentOverdue(
                                                        assignment,
                                                    )
                                                        ? 'font-semibold text-red-600'
                                                        : 'text-gray-600'
                                                "
                                            >
                                                <Clock class="h-4 w-4" />
                                                <strong>Vence:</strong>
                                                {{
                                                    formatDate(
                                                        assignment.due_date,
                                                    )
                                                }}
                                                <span
                                                    v-if="
                                                        isAssignmentOverdue(
                                                            assignment,
                                                        )
                                                    "
                                                    class="ml-1"
                                                    >(Vencida)</span
                                                >
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Estado de entrega -->
                                    <div
                                        v-if="getSubmission(assignment)"
                                        class="ml-4"
                                    >
                                        <div
                                            v-if="
                                                getSubmission(assignment)!
                                                    .grade !== null
                                            "
                                            class="flex flex-col items-end"
                                        >
                                            <div
                                                class="flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800"
                                            >
                                                <CheckCheck class="h-4 w-4" />
                                                Calificada
                                            </div>
                                            <div
                                                class="mt-2 text-2xl font-bold text-green-600"
                                            >
                                                {{
                                                    getSubmission(assignment)!
                                                        .grade
                                                }}/{{ assignment.max_points }}
                                            </div>
                                        </div>
                                        <div
                                            v-else
                                            class="flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800"
                                        >
                                            <Upload class="h-4 w-4" />
                                            Enviada
                                        </div>
                                    </div>
                                    <div
                                        v-else-if="
                                            isAssignmentOverdue(assignment)
                                        "
                                        class="ml-4"
                                    >
                                        <div
                                            class="flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800"
                                        >
                                            <AlertCircle class="h-4 w-4" />
                                            No enviada
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario de entrega -->
                            <div class="p-6">
                                <div
                                    v-if="
                                        getSubmission(assignment) &&
                                        getSubmission(assignment)!.grade !==
                                            null
                                    "
                                >
                                    <!-- Mostrar feedback si está calificada -->
                                    <div
                                        class="rounded-lg border border-green-200 bg-green-50 p-4"
                                    >
                                        <h4
                                            class="mb-2 font-semibold text-green-900"
                                        >
                                            Retroalimentación del profesor
                                        </h4>
                                        <p class="text-green-800">
                                            {{
                                                getSubmission(assignment)!
                                                    .feedback ||
                                                'Sin comentarios adicionales'
                                            }}
                                        </p>
                                    </div>

                                    <!-- Mostrar contenido enviado -->
                                    <div class="mt-4 rounded-lg bg-gray-50 p-4">
                                        <h4
                                            class="mb-2 font-semibold text-gray-900"
                                        >
                                            Tu entrega:
                                        </h4>
                                        <p
                                            class="whitespace-pre-wrap text-gray-700"
                                        >
                                            {{
                                                getSubmission(assignment)!
                                                    .content
                                            }}
                                        </p>

                                        <div
                                            v-if="
                                                getSubmission(assignment)!
                                                    .file_path
                                            "
                                            class="mt-3 border-t pt-3"
                                        >
                                            <a
                                                :href="`/student/submissions/${getSubmission(assignment)!.id}/download`"
                                                class="flex items-center gap-2 font-medium text-indigo-600 hover:text-indigo-700"
                                            >
                                                <Download class="h-4 w-4" />
                                                {{
                                                    getSubmission(assignment)!
                                                        .file_name
                                                }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <form
                                    v-else
                                    @submit.prevent="
                                        submitAssignment(assignment.id)
                                    "
                                    class="space-y-4"
                                >
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            Tu respuesta *
                                        </label>
                                        <textarea
                                            v-model="
                                                submissionForms[assignment.id]
                                                    .content
                                            "
                                            rows="6"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Escribe tu respuesta aquí..."
                                            required
                                            :disabled="
                                                !canEditSubmission(
                                                    getSubmission(assignment),
                                                )
                                            "
                                        ></textarea>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                        >
                                            Archivo adjunto (opcional, máx.
                                            10MB)
                                        </label>
                                        <input
                                            type="file"
                                            @change="
                                                handleSubmissionFile(
                                                    assignment.id,
                                                    $event,
                                                )
                                            "
                                            class="w-full"
                                            :disabled="
                                                !canEditSubmission(
                                                    getSubmission(assignment),
                                                )
                                            "
                                            accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">
                                            Formatos: PDF, DOC, DOCX, ZIP, RAR,
                                            JPG, PNG
                                        </p>

                                        <!-- Mostrar archivo existente -->
                                        <div
                                            v-if="
                                                getSubmission(assignment)
                                                    ?.file_path
                                            "
                                            class="mt-2 rounded border bg-gray-50 p-3"
                                        >
                                            <a
                                                :href="`/student/submissions/${getSubmission(assignment)!.id}/download`"
                                                class="flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                            >
                                                <Download class="h-4 w-4" />
                                                {{
                                                    getSubmission(assignment)!
                                                        .file_name
                                                }}
                                            </a>
                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                            >
                                                Archivo actual - Sube uno nuevo
                                                para reemplazarlo
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="
                                                submissionForms[assignment.id]
                                                    .processing ||
                                                !canEditSubmission(
                                                    getSubmission(assignment),
                                                )
                                            "
                                            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <Upload class="h-5 w-5" />
                                            {{
                                                getSubmission(assignment)
                                                    ? 'Actualizar Entrega'
                                                    : 'Enviar Tarea'
                                            }}
                                        </button>
                                    </div>

                                    <p
                                        v-if="getSubmission(assignment)"
                                        class="text-center text-sm text-gray-500"
                                    >
                                        Última actualización:
                                        {{
                                            formatDate(
                                                getSubmission(assignment)!
                                                    .updated_at,
                                            )
                                        }}
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Preguntas y Respuestas -->
                <div class="border-t pt-6">
                    <div
                        class="mb-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3"
                    >
                        <h2
                            class="flex items-center gap-3 text-xl font-bold text-gray-900"
                        >
                            <MessageCircle
                                class="h-5 w-5 text-indigo-600"
                            />
                            Preguntas y Respuestas
                        </h2>
                        <button
                            @click="showQuestions = !showQuestions"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                        >
                            {{ showQuestions ? 'Ocultar' : 'Mostrar' }}
                            ({{ questions.length }})
                        </button>
                    </div>

                    <div v-show="showQuestions" class="space-y-6">
                        <!-- Formulario de nueva pregunta -->
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 p-5"
                        >
                            <h3 class="mb-3 font-semibold text-gray-900">
                                Hacer una pregunta
                            </h3>
                            <form
                                @submit.prevent="submitQuestion"
                                class="space-y-3"
                            >
                                <textarea
                                    v-model="questionForm.question"
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Escribe tu pregunta aquí (mínimo 10 caracteres)..."
                                    required
                                ></textarea>
                                <div class="flex items-center justify-between">
                                    <p
                                        v-if="questionForm.errors.question"
                                        class="text-sm text-red-600"
                                    >
                                        {{ questionForm.errors.question }}
                                    </p>
                                    <span v-else></span>
                                    <button
                                        type="submit"
                                        :disabled="questionForm.processing"
                                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-50"
                                    >
                                        <Send class="h-4 w-4" />
                                        Enviar pregunta
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de preguntas -->
                        <div
                            v-if="questions.length === 0"
                            class="py-8 text-center text-gray-500"
                        >
                            No hay preguntas aún. Sé el primero en preguntar.
                        </div>

                        <div v-else class="space-y-5">
                            <div
                                v-for="question in questions"
                                :key="question.id"
                                class="rounded-lg border border-gray-200 bg-white"
                            >
                                <!-- Pregunta -->
                                <div class="p-5">
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100"
                                            >
                                                <span
                                                    class="font-semibold text-indigo-600"
                                                    >{{
                                                        question.user.name[0]
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div
                                                class="mb-1 flex items-center gap-2"
                                            >
                                                <span
                                                    class="font-semibold text-gray-900"
                                                    >{{
                                                        question.user.name
                                                    }}</span
                                                >
                                                <span
                                                    v-if="
                                                        question.user.id ===
                                                        currentUserId
                                                    "
                                                    class="rounded bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700"
                                                    >Tú</span
                                                >
                                                <span
                                                    class="text-xs text-gray-500"
                                                    >{{
                                                        formatDate(
                                                            question.created_at,
                                                        )
                                                    }}</span
                                                >
                                            </div>
                                            <p
                                                class="whitespace-pre-wrap text-gray-700"
                                            >
                                                {{ question.question }}
                                            </p>

                                            <!-- Botón responder -->
                                            <div
                                                class="mt-3 flex items-center gap-3"
                                            >
                                                <button
                                                    v-if="
                                                        question.user.id !==
                                                            currentUserId ||
                                                        question.answers
                                                            .length > 0
                                                    "
                                                    @click="
                                                        toggleReply(question.id)
                                                    "
                                                    class="flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                                >
                                                    <CornerDownRight
                                                        class="h-3.5 w-3.5"
                                                    />
                                                    {{
                                                        replyingTo ===
                                                        question.id
                                                            ? 'Cancelar'
                                                            : 'Responder'
                                                    }}
                                                </button>
                                                <span
                                                    v-if="
                                                        question.answers
                                                            .length > 0
                                                    "
                                                    class="text-sm text-gray-400"
                                                >
                                                    {{
                                                        question.answers.length
                                                    }}
                                                    {{
                                                        question.answers
                                                            .length === 1
                                                            ? 'respuesta'
                                                            : 'respuestas'
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Respuestas -->
                                <div
                                    v-if="
                                        question.answers.length > 0 ||
                                        replyingTo === question.id
                                    "
                                    class="border-t border-gray-100 bg-gray-50/50"
                                >
                                    <div
                                        class="space-y-0 divide-y divide-gray-100"
                                    >
                                        <div
                                            v-for="answer in question.answers"
                                            :key="answer.id"
                                            class="flex gap-3 px-5 py-4 pl-14"
                                        >
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="flex h-8 w-8 items-center justify-center rounded-full"
                                                    :class="
                                                        answer.user.id ===
                                                        currentUserId
                                                            ? 'bg-indigo-100'
                                                            : 'bg-emerald-100'
                                                    "
                                                >
                                                    <span
                                                        class="text-sm font-semibold"
                                                        :class="
                                                            answer.user.id ===
                                                            currentUserId
                                                                ? 'text-indigo-600'
                                                                : 'text-emerald-600'
                                                        "
                                                        >{{
                                                            answer.user.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div
                                                    class="mb-1 flex flex-wrap items-center gap-2"
                                                >
                                                    <span
                                                        class="text-sm font-semibold text-gray-900"
                                                        >{{
                                                            answer.user.name
                                                        }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            answer.user.id ===
                                                            currentUserId
                                                        "
                                                        class="rounded bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700"
                                                        >Tú</span
                                                    >
                                                    <span
                                                        v-else-if="
                                                            answer.user.id !==
                                                            question.user.id
                                                        "
                                                        class="rounded bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700"
                                                        >Instructor</span
                                                    >
                                                    <span
                                                        class="text-xs text-gray-500"
                                                        >{{
                                                            formatDate(
                                                                answer.created_at,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                                <p
                                                    class="text-sm whitespace-pre-wrap text-gray-700"
                                                >
                                                    {{ answer.answer }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Formulario de respuesta inline -->
                                    <div
                                        v-if="replyingTo === question.id"
                                        class="border-t border-gray-200 px-5 py-4 pl-14"
                                    >
                                        <form
                                            @submit.prevent="
                                                submitAnswer(question.id)
                                            "
                                            class="flex gap-3"
                                        >
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100"
                                                >
                                                    <span
                                                        class="text-sm font-semibold text-indigo-600"
                                                    >
                                                        {{
                                                            (
                                                                page.props
                                                                    .auth as any
                                                            )?.user
                                                                ?.name?.[0] ||
                                                            '?'
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <textarea
                                                    v-model="
                                                        answerForms[question.id]
                                                            .answer
                                                    "
                                                    rows="2"
                                                    class="w-full rounded-lg border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="Escribe tu respuesta..."
                                                    required
                                                ></textarea>
                                                <p
                                                    v-if="
                                                        answerForms[question.id]
                                                            ?.errors?.answer
                                                    "
                                                    class="mt-1 text-xs text-red-600"
                                                >
                                                    {{
                                                        answerForms[question.id]
                                                            .errors.answer
                                                    }}
                                                </p>
                                                <div
                                                    class="mt-2 flex justify-end"
                                                >
                                                    <button
                                                        type="submit"
                                                        :disabled="
                                                            answerForms[
                                                                question.id
                                                            ]?.processing
                                                        "
                                                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-1.5 text-sm text-white transition hover:bg-indigo-700 disabled:opacity-50"
                                                    >
                                                        <Send
                                                            class="h-3.5 w-3.5"
                                                        />
                                                        Responder
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Curriculum -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 rounded-xl border bg-white shadow-sm">
                    <!-- Header -->
                    <div class="border-b p-6">
                        <h2 class="mb-2 text-lg font-bold text-gray-900">
                            Contenido del curso
                        </h2>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Progreso</span>
                            <span class="font-semibold text-indigo-600"
                                >{{ progress }}%</span
                            >
                        </div>
                        <div
                            class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200"
                        >
                            <div
                                class="h-full bg-indigo-600 transition-all duration-500"
                                :style="{ width: `${progress}%` }"
                            />
                        </div>
                    </div>

                    <!-- Lista de Secciones y Lecciones -->
                    <div class="max-h-[600px] overflow-y-auto">
                        <div
                            v-for="section in course.sections"
                            :key="section.id"
                            class="border-b last:border-b-0"
                        >
                            <!-- Sección -->
                            <div class="bg-gray-50 px-6 py-4">
                                <h3 class="font-semibold text-gray-900">
                                    {{ section.name }}
                                </h3>
                                <p class="mt-1 text-xs text-gray-600">
                                    {{ section.lessons.length }}
                                    {{
                                        section.lessons.length === 1
                                            ? 'lección'
                                            : 'lecciones'
                                    }}
                                </p>
                            </div>

                            <!-- Lecciones -->
                            <div class="divide-y">
                                <button
                                    v-for="sectionLesson in section.lessons"
                                    :key="sectionLesson.id"
                                    @click="
                                        goToLesson(course.id, sectionLesson.id)
                                    "
                                    class="flex w-full items-start gap-3 px-6 py-3 text-left transition-colors hover:bg-gray-50"
                                    :class="
                                        sectionLesson.id === lesson.id
                                            ? 'border-l-4 border-indigo-600 bg-indigo-50'
                                            : ''
                                    "
                                >
                                    <PlayCircle
                                        class="mt-0.5 h-5 w-5 flex-shrink-0"
                                        :class="
                                            sectionLesson.id === lesson.id
                                                ? 'text-indigo-600'
                                                : 'text-gray-400'
                                        "
                                    />
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-medium"
                                            :class="
                                                sectionLesson.id === lesson.id
                                                    ? 'text-indigo-900'
                                                    : 'text-gray-900'
                                            "
                                        >
                                            {{ sectionLesson.name }}
                                        </p>
                                        <div
                                            class="mt-1 flex items-center gap-2"
                                        >
                                            <span
                                                v-if="sectionLesson.duration"
                                                class="text-xs text-gray-500"
                                            >
                                                {{
                                                    formatDuration(
                                                        sectionLesson.duration,
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="sectionLesson.is_preview"
                                                class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700"
                                            >
                                                Preview
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
