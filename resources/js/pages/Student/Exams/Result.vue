<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Award,
    BookOpen,
    CheckCircle,
    RefreshCw,
    Target,
    Trophy,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Course {
    id: number;
    title: string;
}

interface Exam {
    title: string;
    passing_score: number;
    max_attempts: number;
}

interface AttemptAnswer {
    question_text: string;
    student_answer: string | null;
    correct_answer: string;
    is_correct: boolean;
    points: number;
    earned_points: number;
}

interface Attempt {
    score: number;
    passed: boolean;
    answers: AttemptAnswer[];
    completed_at: string;
}

interface Props {
    course: Course;
    exam: Exam;
    attempt: Attempt;
    canRetry: boolean;
    totalAttempts: number;
}

const props = defineProps<Props>();

const scoreColor = computed(() => {
    if (props.attempt.passed) return 'text-green-600';
    if (props.attempt.score >= props.exam.passing_score * 0.7) return 'text-amber-600';
    return 'text-red-600';
});

const scoreBgColor = computed(() => {
    if (props.attempt.passed) return 'from-green-50 to-emerald-50 border-green-200';
    return 'from-red-50 to-orange-50 border-red-200';
});

const scoreRingColor = computed(() => {
    if (props.attempt.passed) return 'ring-green-500';
    return 'ring-red-500';
});

const correctAnswersCount = computed(() => {
    if (!props.attempt.answers) return 0;
    return props.attempt.answers.filter((a) => a.is_correct).length;
});

const totalQuestionsCount = computed(() => {
    if (!props.attempt.answers) return 0;
    return props.attempt.answers.length;
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Resultado - ${exam.title}`" />

    <StudentLayout>
        <div class="min-h-screen bg-gray-50">
            <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                    <Link :href="`/student/courses/${course.id}`" class="hover:text-indigo-600">
                        {{ course.title }}
                    </Link>
                    <span>/</span>
                    <Link :href="route('student.exam.show', course.id)" class="hover:text-indigo-600">
                        Examen
                    </Link>
                    <span>/</span>
                    <span class="text-gray-900">Resultado</span>
                </div>

                <!-- Tarjeta principal de resultado -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl border bg-gradient-to-br shadow-lg"
                    :class="scoreBgColor"
                >
                    <div class="p-8 text-center">
                        <!-- Icono principal -->
                        <div class="mb-6 flex justify-center">
                            <div
                                v-if="attempt.passed"
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-green-100 ring-4"
                                :class="scoreRingColor"
                            >
                                <Trophy class="h-12 w-12 text-green-600" />
                            </div>
                            <div
                                v-else
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-red-100 ring-4"
                                :class="scoreRingColor"
                            >
                                <XCircle class="h-12 w-12 text-red-600" />
                            </div>
                        </div>

                        <!-- Resultado -->
                        <h1 class="mb-2 text-3xl font-bold" :class="attempt.passed ? 'text-green-900' : 'text-red-900'">
                            {{ attempt.passed ? 'Examen Aprobado' : 'Examen No Aprobado' }}
                        </h1>
                        <p class="mb-1 text-sm" :class="attempt.passed ? 'text-green-700' : 'text-red-700'">
                            {{ exam.title }}
                        </p>
                        <p class="mb-6 text-xs text-gray-500">
                            Completado el {{ formatDate(attempt.completed_at) }}
                        </p>

                        <!-- Puntaje -->
                        <div class="mb-6">
                            <div class="text-7xl font-black" :class="scoreColor">
                                {{ attempt.score }}%
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                Puntaje mínimo para aprobar: <span class="font-semibold">{{ exam.passing_score }}%</span>
                            </p>
                        </div>

                        <!-- Estadísticas rápidas -->
                        <div class="mx-auto max-w-md grid grid-cols-3 gap-4">
                            <div class="rounded-lg bg-white/60 p-3">
                                <p class="text-2xl font-bold text-gray-900">{{ correctAnswersCount }}</p>
                                <p class="text-xs text-gray-600">Correctas</p>
                            </div>
                            <div class="rounded-lg bg-white/60 p-3">
                                <p class="text-2xl font-bold text-gray-900">{{ totalQuestionsCount - correctAnswersCount }}</p>
                                <p class="text-xs text-gray-600">Incorrectas</p>
                            </div>
                            <div class="rounded-lg bg-white/60 p-3">
                                <p class="text-2xl font-bold text-gray-900">{{ totalQuestionsCount }}</p>
                                <p class="text-xs text-gray-600">Total</p>
                            </div>
                        </div>

                        <!-- Intento info -->
                        <p class="mt-4 text-sm text-gray-600">
                            Intento {{ totalAttempts }} de {{ exam.max_attempts }}
                        </p>
                    </div>
                </div>

                <!-- Mensaje de aprobado -->
                <div
                    v-if="attempt.passed"
                    class="mb-8 rounded-xl border border-green-200 bg-green-50 p-6"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                            <Award class="h-6 w-6 text-green-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-green-900">Felicitaciones</h3>
                            <p class="mt-1 text-sm text-green-800">
                                Has aprobado el examen exitosamente. Tu certificado estara disponible para descarga desde la seccion de certificados cuando se genere.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de no aprobado con reintento -->
                <div
                    v-if="!attempt.passed && canRetry"
                    class="mb-8 rounded-xl border border-amber-200 bg-amber-50 p-6"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-amber-100">
                            <RefreshCw class="h-6 w-6 text-amber-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-900">Puedes intentar de nuevo</h3>
                            <p class="mt-1 text-sm text-amber-800">
                                No alcanzaste el puntaje minimo de aprobacion, pero aun tienes intentos disponibles. Revisa tus respuestas y vuelve a intentarlo.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de intentos agotados -->
                <div
                    v-if="!attempt.passed && !canRetry"
                    class="mb-8 rounded-xl border border-red-200 bg-red-50 p-6"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                            <XCircle class="h-6 w-6 text-red-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-red-900">Intentos agotados</h3>
                            <p class="mt-1 text-sm text-red-800">
                                Has utilizado todos tus intentos disponibles para este examen. Contacta a tu instructor si necesitas ayuda.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detalle de respuestas -->
                <div v-if="attempt.answers && attempt.answers.length > 0" class="mb-8">
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-gray-900">
                        <BookOpen class="h-6 w-6 text-indigo-600" />
                        Detalle de Respuestas
                    </h2>
                    <div class="space-y-4">
                        <div
                            v-for="(answer, index) in attempt.answers"
                            :key="index"
                            class="overflow-hidden rounded-lg border bg-white"
                            :class="answer.is_correct ? 'border-green-200' : 'border-red-200'"
                        >
                            <div
                                class="flex items-center justify-between px-4 py-3"
                                :class="answer.is_correct ? 'bg-green-50' : 'bg-red-50'"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white"
                                        :class="answer.is_correct ? 'bg-green-600' : 'bg-red-600'"
                                    >
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium" :class="answer.is_correct ? 'text-green-800' : 'text-red-800'">
                                        {{ answer.is_correct ? 'Correcta' : 'Incorrecta' }}
                                    </span>
                                </div>
                                <span class="text-sm font-semibold" :class="answer.is_correct ? 'text-green-700' : 'text-red-700'">
                                    {{ answer.earned_points }}/{{ answer.points }} puntos
                                </span>
                            </div>
                            <div class="p-4">
                                <p class="mb-3 font-medium text-gray-900">{{ answer.question_text }}</p>

                                <div class="space-y-2 text-sm">
                                    <div class="flex items-start gap-2">
                                        <span class="mt-0.5 font-medium text-gray-500">Tu respuesta:</span>
                                        <span
                                            :class="answer.is_correct ? 'text-green-700' : 'text-red-700'"
                                            class="font-semibold"
                                        >
                                            {{ answer.student_answer || 'Sin respuesta' }}
                                        </span>
                                    </div>
                                    <div v-if="!answer.is_correct" class="flex items-start gap-2">
                                        <span class="mt-0.5 font-medium text-gray-500">Respuesta correcta:</span>
                                        <span class="font-semibold text-green-700">
                                            {{ answer.correct_answer }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link
                        v-if="canRetry && !attempt.passed"
                        :href="route('student.exam.show', course.id)"
                        class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-3 font-bold text-white shadow-lg transition-all hover:from-indigo-700 hover:to-blue-700 hover:shadow-xl"
                    >
                        <RefreshCw class="h-5 w-5" />
                        Reintentar
                    </Link>

                    <Link
                        :href="`/student/courses/${course.id}`"
                        class="flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-8 py-3 font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <ArrowLeft class="h-5 w-5" />
                        Volver al Curso
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
