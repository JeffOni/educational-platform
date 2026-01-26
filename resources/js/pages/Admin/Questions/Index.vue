<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, MessageCircle, Send } from 'lucide-vue-next';
import { ref } from 'vue';

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

interface Course {
    id: number;
    title: string;
}

interface Section {
    id: number;
    name: string;
    course: Course;
}

interface Lesson {
    id: number;
    name: string;
    section: Section;
}

interface Question {
    id: number;
    question: string;
    user: User;
    lesson: Lesson;
    answers: Answer[];
    created_at: string;
}

interface PaginatedQuestions {
    data: Question[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    questions: PaginatedQuestions;
}

const props = defineProps<Props>();

const answeringQuestion = ref<number | null>(null);
const answerForms = ref<{ [key: number]: ReturnType<typeof useForm> }>({});

const startAnswering = (questionId: number) => {
    answeringQuestion.value = questionId;
    if (!answerForms.value[questionId]) {
        answerForms.value[questionId] = useForm({
            answer: '',
        });
    }
};

const submitAnswer = (questionId: number) => {
    const form = answerForms.value[questionId];
    if (form) {
        form.post(route('admin.questions.answer', questionId), {
            preserveScroll: true,
            onSuccess: () => {
                answeringQuestion.value = null;
                form.reset();
            },
        });
    }
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
</script>

<template>
    <Head title="Preguntas de Estudiantes" />

    <AppLayout>
        <div class="py-12">
            <div class="w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b p-6">
                        <div class="flex items-center gap-3">
                            <MessageCircle class="h-8 w-8 text-indigo-600" />
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Preguntas de Estudiantes
                                </h2>
                                <p class="mt-1 text-gray-600">
                                    Responde las preguntas de tus estudiantes
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Sin preguntas -->
                        <div
                            v-if="questions.data.length === 0"
                            class="py-12 text-center"
                        >
                            <MessageCircle
                                class="mx-auto mb-4 h-16 w-16 text-gray-300"
                            />
                            <p class="text-lg text-gray-500">
                                No hay preguntas pendientes
                            </p>
                        </div>

                        <!-- Lista de preguntas -->
                        <div v-else class="space-y-6">
                            <div
                                v-for="question in questions.data"
                                :key="question.id"
                                class="rounded-lg border border-gray-200 p-6 transition hover:border-indigo-300"
                            >
                                <!-- Información del curso y lección -->
                                <div
                                    class="mb-3 flex items-center gap-2 text-sm text-gray-600"
                                >
                                    <BookOpen class="h-4 w-4" />
                                    <span class="font-semibold">{{
                                        question.lesson.section.course.title
                                    }}</span>
                                    <span>›</span>
                                    <span>{{
                                        question.lesson.section.name
                                    }}</span>
                                    <span>›</span>
                                    <span>{{ question.lesson.name }}</span>
                                </div>

                                <!-- Pregunta -->
                                <div class="mb-4 flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100"
                                        >
                                            <span
                                                class="text-lg font-semibold text-indigo-600"
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
                                                >{{ question.user.name }}</span
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
                                        <p class="text-lg text-gray-700">
                                            {{ question.question }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Respuestas existentes -->
                                <div
                                    v-if="question.answers.length > 0"
                                    class="mb-4 ml-16 space-y-3"
                                >
                                    <div
                                        v-for="answer in question.answers"
                                        :key="answer.id"
                                        class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4"
                                    >
                                        <div
                                            class="mb-2 flex items-center gap-2"
                                        >
                                            <span
                                                class="text-sm font-semibold text-gray-900"
                                                >{{ answer.user.name }}</span
                                            >
                                            <span
                                                class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-700"
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
                                        <p class="text-gray-700">
                                            {{ answer.answer }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Formulario de respuesta -->
                                <div class="ml-16">
                                    <div
                                        v-if="answeringQuestion === question.id"
                                        class="rounded-lg bg-gray-50 p-4"
                                    >
                                        <form
                                            @submit.prevent="
                                                submitAnswer(question.id)
                                            "
                                            class="space-y-3"
                                        >
                                            <textarea
                                                v-model="
                                                    answerForms[question.id]
                                                        .answer
                                                "
                                                rows="3"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Escribe tu respuesta..."
                                                required
                                            ></textarea>
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    type="button"
                                                    @click="
                                                        answeringQuestion = null
                                                    "
                                                    class="rounded-lg px-4 py-2 text-gray-700 transition hover:bg-gray-200"
                                                >
                                                    Cancelar
                                                </button>
                                                <button
                                                    type="submit"
                                                    :disabled="
                                                        answerForms[question.id]
                                                            ?.processing
                                                    "
                                                    class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-50"
                                                >
                                                    <Send class="h-4 w-4" />
                                                    Enviar respuesta
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <button
                                        v-else
                                        @click="startAnswering(question.id)"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                    >
                                        {{
                                            question.answers.length > 0
                                                ? 'Añadir otra respuesta'
                                                : 'Responder'
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div
                            v-if="questions.last_page > 1"
                            class="mt-6 flex justify-center"
                        >
                            <nav class="flex gap-2">
                                <a
                                    v-for="page in questions.last_page"
                                    :key="page"
                                    :href="`?page=${page}`"
                                    :class="[
                                        'rounded-lg px-4 py-2 font-medium transition',
                                        page === questions.current_page
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                    ]"
                                >
                                    {{ page }}
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
