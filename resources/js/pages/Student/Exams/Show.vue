<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Award,
    BookOpen,
    CheckCircle,
    Clock,
    FileText,
    Hash,
    PlayCircle,
    Target,
    Trophy,
    XCircle,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Course {
    id: number;
    title: string;
}

interface Exam {
    title: string;
    description: string | null;
    passing_score: number;
    time_limit: number | null;
    max_attempts: number;
    total_questions: number;
    total_points: number;
}

interface PreviousAttempt {
    id: number;
    score: number;
    passed: boolean;
    completed_at: string;
}

interface Props {
    course: Course;
    exam: Exam;
    canAttempt: boolean;
    hasPassingAttempt: boolean;
    previousAttempts: PreviousAttempt[];
}

const props = defineProps<Props>();

const startingExam = ref(false);

const startExam = () => {
    startingExam.value = true;
    router.post(route('student.exam.start', props.course.id), {}, {
        onError: () => {
            startingExam.value = false;
        },
    });
};

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
    <Head :title="`Examen - ${course.title}`" />

    <StudentLayout>
        <div class="min-h-screen bg-gray-50">
            <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                    <Link :href="`/student/courses/${course.id}`" class="hover:text-indigo-600">
                        {{ course.title }}
                    </Link>
                    <span>/</span>
                    <span class="text-gray-900">Examen</span>
                </div>

                <!-- Mensaje de aprobado -->
                <div
                    v-if="hasPassingAttempt"
                    class="mb-6 rounded-xl border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                            <Trophy class="h-7 w-7 text-green-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-green-900">Ya aprobaste este examen</h3>
                            <p class="text-sm text-green-700">
                                Has completado exitosamente el examen de este curso.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de información del examen -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b bg-gradient-to-r from-indigo-600 to-blue-600 p-6 text-white">
                        <div class="flex items-center gap-3">
                            <ClipboardList class="h-8 w-8" />
                            <div>
                                <h1 class="text-2xl font-bold">{{ exam.title }}</h1>
                                <p class="mt-1 text-indigo-100">{{ course.title }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Descripción -->
                        <div v-if="exam.description" class="mb-6">
                            <h2 class="mb-2 flex items-center gap-2 font-semibold text-gray-900">
                                <BookOpen class="h-5 w-5 text-indigo-600" />
                                Descripción
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-700">{{ exam.description }}</p>
                        </div>

                        <!-- Detalles del examen -->
                        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-lg border bg-gray-50 p-4 text-center">
                                <FileText class="mx-auto mb-2 h-6 w-6 text-indigo-600" />
                                <p class="text-2xl font-bold text-gray-900">{{ exam.total_questions }}</p>
                                <p class="text-sm text-gray-600">Preguntas</p>
                            </div>
                            <div class="rounded-lg border bg-gray-50 p-4 text-center">
                                <Target class="mx-auto mb-2 h-6 w-6 text-indigo-600" />
                                <p class="text-2xl font-bold text-gray-900">{{ exam.passing_score }}%</p>
                                <p class="text-sm text-gray-600">Puntaje para Aprobar</p>
                            </div>
                            <div class="rounded-lg border bg-gray-50 p-4 text-center">
                                <Clock class="mx-auto mb-2 h-6 w-6 text-indigo-600" />
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ exam.time_limit ? `${exam.time_limit} min` : 'Sin límite' }}
                                </p>
                                <p class="text-sm text-gray-600">Tiempo Límite</p>
                            </div>
                            <div class="rounded-lg border bg-gray-50 p-4 text-center">
                                <Hash class="mx-auto mb-2 h-6 w-6 text-indigo-600" />
                                <p class="text-2xl font-bold text-gray-900">{{ exam.max_attempts }}</p>
                                <p class="text-sm text-gray-600">Intentos Máximos</p>
                            </div>
                        </div>

                        <!-- Puntos totales -->
                        <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                            <div class="flex items-center gap-2 text-indigo-900">
                                <Award class="h-5 w-5" />
                                <span class="font-semibold">Puntos Totales: {{ exam.total_points }}</span>
                            </div>
                        </div>

                        <!-- Acción principal -->
                        <div class="flex justify-center border-t pt-6">
                            <!-- Puede intentar -->
                            <button
                                v-if="canAttempt && !hasPassingAttempt"
                                @click="startExam"
                                :disabled="startingExam"
                                class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-4 text-lg font-bold text-white shadow-lg transition-all hover:from-indigo-700 hover:to-blue-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <PlayCircle class="h-6 w-6" />
                                {{ startingExam ? 'Iniciando...' : 'Iniciar Examen' }}
                            </button>

                            <!-- Intentos agotados -->
                            <div
                                v-if="!canAttempt && !hasPassingAttempt"
                                class="flex items-center gap-3 rounded-xl border-2 border-red-200 bg-red-50 px-8 py-4 text-red-800"
                            >
                                <AlertTriangle class="h-6 w-6" />
                                <div>
                                    <p class="font-bold">Has agotado tus intentos</p>
                                    <p class="text-sm">No puedes realizar más intentos de este examen.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de intentos -->
                <div v-if="previousAttempts.length > 0" class="mt-8">
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-gray-900">
                        <Clock class="h-6 w-6 text-indigo-600" />
                        Historial de Intentos
                    </h2>
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Intento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Puntaje
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Resultado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Fecha
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="(attempt, index) in previousAttempts"
                                    :key="attempt.id"
                                    class="transition hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        Intento {{ index + 1 }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            class="text-lg font-bold"
                                            :class="attempt.passed ? 'text-green-600' : 'text-red-600'"
                                        >
                                            {{ attempt.score }}%
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            v-if="attempt.passed"
                                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800"
                                        >
                                            <CheckCircle class="h-4 w-4" />
                                            Aprobado
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800"
                                        >
                                            <XCircle class="h-4 w-4" />
                                            Reprobado
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ formatDate(attempt.completed_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Enlace al curso -->
                <div class="mt-6 text-center">
                    <Link
                        :href="`/student/courses/${course.id}`"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                    >
                        Volver al curso
                    </Link>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
