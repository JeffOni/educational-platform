<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle,
    Clock,
    Send,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

interface QuestionOption {
    label: string;
    value: string;
}

interface ExamQuestion {
    id: number;
    question_text: string;
    question_type: 'multiple_choice' | 'true_false';
    options: string[] | null;
    points: number;
}

interface Exam {
    id: number;
    title: string;
    time_limit: number | null;
    questions: ExamQuestion[];
}

interface Attempt {
    id: number;
    started_at: string;
}

interface Course {
    id: number;
    title: string;
}

interface Props {
    course: Course;
    exam: Exam;
    attempt: Attempt;
    canAttempt: boolean;
}

const props = defineProps<Props>();

// Respuestas del estudiante
const answers = reactive<Record<number, string>>({});

// Timer
const remainingSeconds = ref<number | null>(null);
const timerInterval = ref<ReturnType<typeof setInterval> | null>(null);
const submitting = ref(false);
const showConfirmSubmit = ref(false);

// Pregunta actual (para navegacion)
const currentQuestionIndex = ref(0);

const totalQuestions = computed(() => props.exam.questions.length);

const answeredCount = computed(() => {
    return Object.keys(answers).filter((key) => answers[Number(key)] && answers[Number(key)].trim() !== '').length;
});

const allAnswered = computed(() => answeredCount.value === totalQuestions.value);

const progressPercentage = computed(() => {
    if (totalQuestions.value === 0) return 0;
    return Math.round((answeredCount.value / totalQuestions.value) * 100);
});

const formattedTime = computed(() => {
    if (remainingSeconds.value === null) return null;
    const mins = Math.floor(remainingSeconds.value / 60);
    const secs = remainingSeconds.value % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

const isTimeWarning = computed(() => {
    if (remainingSeconds.value === null) return false;
    return remainingSeconds.value <= 60;
});

const isTimeDanger = computed(() => {
    if (remainingSeconds.value === null) return false;
    return remainingSeconds.value <= 30;
});

// Inicializar timer
onMounted(() => {
    if (props.exam.time_limit) {
        const startedAt = new Date(props.attempt.started_at).getTime();
        const timeLimitMs = props.exam.time_limit * 60 * 1000;
        const endTime = startedAt + timeLimitMs;

        const updateTimer = () => {
            const now = Date.now();
            const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
            remainingSeconds.value = remaining;

            if (remaining <= 0) {
                // Auto-submit
                if (timerInterval.value) {
                    clearInterval(timerInterval.value);
                }
                submitExam();
            }
        };

        updateTimer();
        timerInterval.value = setInterval(updateTimer, 1000);
    }
});

onBeforeUnmount(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
});

const submitExam = () => {
    if (submitting.value) return;
    submitting.value = true;

    router.post(
        route('student.exam.submit', props.course.id),
        {
            attempt_id: props.attempt.id,
            answers: answers,
        },
        {
            onError: () => {
                submitting.value = false;
            },
        },
    );
};

const confirmSubmit = () => {
    if (!allAnswered.value) {
        showConfirmSubmit.value = true;
    } else {
        submitExam();
    }
};

const getOptionsForQuestion = (question: ExamQuestion): QuestionOption[] => {
    if (question.question_type === 'true_false') {
        return [
            { label: 'Verdadero', value: 'Verdadero' },
            { label: 'Falso', value: 'Falso' },
        ];
    }
    if (question.options) {
        return question.options.map((opt) => ({ label: opt, value: opt }));
    }
    return [];
};
</script>

<template>
    <Head :title="`Examen - ${exam.title}`" />

    <StudentLayout>
        <div class="min-h-screen bg-gray-50">
            <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header con timer -->
                <div class="sticky top-24 z-40 mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-lg">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">{{ exam.title }}</h1>
                            <p class="text-sm text-gray-500">{{ course.title }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Progreso -->
                            <div class="flex items-center gap-2 text-sm">
                                <CheckCircle class="h-4 w-4 text-indigo-600" />
                                <span class="font-medium">
                                    {{ answeredCount }} / {{ totalQuestions }}
                                </span>
                            </div>

                            <!-- Timer -->
                            <div
                                v-if="formattedTime !== null"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 font-mono text-lg font-bold"
                                :class="{
                                    'bg-gray-100 text-gray-900': !isTimeWarning,
                                    'bg-yellow-100 text-yellow-800': isTimeWarning && !isTimeDanger,
                                    'animate-pulse bg-red-100 text-red-800': isTimeDanger,
                                }"
                            >
                                <Clock class="h-5 w-5" />
                                {{ formattedTime }}
                            </div>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200">
                        <div
                            class="h-full bg-indigo-600 transition-all duration-300"
                            :style="{ width: `${progressPercentage}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Preguntas -->
                <div class="space-y-6">
                    <div
                        v-for="(question, index) in exam.questions"
                        :key="question.id"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all"
                        :class="answers[question.id] ? 'border-green-300' : ''"
                    >
                        <!-- Header de la pregunta -->
                        <div
                            class="flex items-center justify-between border-b px-6 py-4"
                            :class="answers[question.id] ? 'bg-green-50' : 'bg-gray-50'"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white"
                                    :class="answers[question.id] ? 'bg-green-600' : 'bg-indigo-600'"
                                >
                                    {{ index + 1 }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Pregunta {{ index + 1 }} de {{ totalQuestions }}
                                </span>
                            </div>
                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                {{ question.points }} {{ question.points === 1 ? 'punto' : 'puntos' }}
                            </span>
                        </div>

                        <!-- Cuerpo de la pregunta -->
                        <div class="p-6">
                            <p class="mb-6 text-lg font-medium text-gray-900">
                                {{ question.question_text }}
                            </p>

                            <!-- Opciones -->
                            <div class="space-y-3">
                                <label
                                    v-for="(option, optIndex) in getOptionsForQuestion(question)"
                                    :key="optIndex"
                                    class="flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-all hover:border-indigo-300 hover:bg-indigo-50/50"
                                    :class="
                                        answers[question.id] === option.value
                                            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200'
                                            : 'border-gray-200'
                                    "
                                >
                                    <input
                                        v-model="answers[question.id]"
                                        type="radio"
                                        :name="`question_${question.id}`"
                                        :value="option.value"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="flex-1 text-sm"
                                        :class="
                                            answers[question.id] === option.value
                                                ? 'font-semibold text-indigo-900'
                                                : 'text-gray-700'
                                        "
                                    >
                                        {{ option.label }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de enviar -->
                <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                        <div class="text-sm text-gray-600">
                            <span v-if="allAnswered" class="flex items-center gap-2 font-semibold text-green-700">
                                <CheckCircle class="h-4 w-4" />
                                Has respondido todas las preguntas
                            </span>
                            <span v-else class="flex items-center gap-2 text-amber-700">
                                <AlertTriangle class="h-4 w-4" />
                                Tienes {{ totalQuestions - answeredCount }} {{ (totalQuestions - answeredCount) === 1 ? 'pregunta' : 'preguntas' }} sin responder
                            </span>
                        </div>

                        <button
                            @click="confirmSubmit"
                            :disabled="submitting"
                            class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-3 font-bold text-white shadow-lg transition-all hover:from-indigo-700 hover:to-blue-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Send class="h-5 w-5" />
                            {{ submitting ? 'Enviando...' : 'Enviar Examen' }}
                        </button>
                    </div>
                </div>

                <!-- Modal de confirmación -->
                <div
                    v-if="showConfirmSubmit"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                >
                    <div class="mx-4 max-w-md rounded-xl bg-white p-6 shadow-2xl">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                                <AlertTriangle class="h-6 w-6 text-amber-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Confirmar Envío</h3>
                                <p class="text-sm text-gray-500">
                                    Tienes {{ totalQuestions - answeredCount }} {{ (totalQuestions - answeredCount) === 1 ? 'pregunta' : 'preguntas' }} sin responder.
                                </p>
                            </div>
                        </div>
                        <p class="mb-6 text-gray-700">
                            Las preguntas sin responder se contarán como incorrectas. ¿Estás seguro de que deseas enviar el examen?
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showConfirmSubmit = false"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                            >
                                Seguir Respondiendo
                            </button>
                            <button
                                @click="showConfirmSubmit = false; submitExam()"
                                :disabled="submitting"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Enviar de Todos Modos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
