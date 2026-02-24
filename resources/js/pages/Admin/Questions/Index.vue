<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    CheckCircle,
    Clock,
    CornerDownRight,
    MessageCircle,
    Send,
} from 'lucide-vue-next';
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

interface Lesson {
    id: number;
    name: string;
    section: {
        id: number;
        name: string;
        course: {
            id: number;
            title: string;
        };
    };
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
    per_page: number;
    total: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

defineProps<{
    questions: PaginatedQuestions;
}>();

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
    form.post(`/admin/questions/${questionId}/answer`, {
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
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Preguntas', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Preguntas de Estudiantes" />

        <div class="w-full p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle
                                class="flex items-center gap-3 text-lg"
                            >
                                <MessageCircle
                                    class="h-5 w-5 text-indigo-600"
                                />
                                Preguntas de Estudiantes
                            </CardTitle>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ questions.total }} pregunta{{
                                    questions.total !== 1 ? 's' : ''
                                }}
                                en tus cursos
                            </p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Sin preguntas -->
                    <div
                        v-if="questions.data.length === 0"
                        class="py-16 text-center"
                    >
                        <MessageCircle
                            class="mx-auto h-12 w-12 text-gray-300"
                        />
                        <p class="mt-4 text-lg font-medium text-gray-600">
                            No hay preguntas pendientes
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Las preguntas de tus estudiantes aparecerán aquí
                        </p>
                    </div>

                    <!-- Lista de preguntas -->
                    <div v-else class="space-y-4">
                        <div
                            v-for="question in questions.data"
                            :key="question.id"
                            class="rounded-lg border"
                            :class="
                                question.answers.length === 0
                                    ? 'border-amber-200 bg-amber-50/30'
                                    : 'border-gray-200 bg-white'
                            "
                        >
                            <!-- Header: Curso y Lección -->
                            <div
                                class="flex items-center justify-between border-b px-5 py-3"
                                :class="
                                    question.answers.length === 0
                                        ? 'border-amber-100 bg-amber-50/50'
                                        : 'border-gray-100 bg-gray-50'
                                "
                            >
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="font-medium text-gray-900">
                                        {{
                                            question.lesson.section.course.title
                                        }}
                                    </span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-600">
                                        {{ question.lesson.section.name }}
                                    </span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-600">
                                        {{ question.lesson.name }}
                                    </span>
                                </div>
                                <Badge
                                    :variant="
                                        question.answers.length === 0
                                            ? 'destructive'
                                            : 'default'
                                    "
                                    class="text-xs"
                                >
                                    <template
                                        v-if="question.answers.length === 0"
                                    >
                                        <Clock class="mr-1 h-3 w-3" />
                                        Sin responder
                                    </template>
                                    <template v-else>
                                        <CheckCircle class="mr-1 h-3 w-3" />
                                        {{
                                            question.answers.length
                                        }}
                                        respuesta{{
                                            question.answers.length !== 1
                                                ? 's'
                                                : ''
                                        }}
                                    </template>
                                </Badge>
                            </div>

                            <!-- Pregunta -->
                            <div class="p-5">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100"
                                        >
                                            <span
                                                class="font-semibold text-indigo-600"
                                            >
                                                {{ question.user.name[0] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="mb-1 flex items-center gap-2"
                                        >
                                            <span
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ question.user.name }}
                                            </span>
                                            <Badge
                                                variant="secondary"
                                                class="text-xs"
                                            >
                                                Estudiante
                                            </Badge>
                                            <span class="text-xs text-gray-500">
                                                {{
                                                    formatDate(
                                                        question.created_at,
                                                    )
                                                }}
                                            </span>
                                        </div>
                                        <p
                                            class="whitespace-pre-wrap text-gray-700"
                                        >
                                            {{ question.question }}
                                        </p>

                                        <!-- Botón responder -->
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="mt-3 text-indigo-600 hover:text-indigo-700"
                                            @click="toggleReply(question.id)"
                                        >
                                            <CornerDownRight
                                                class="mr-1.5 h-3.5 w-3.5"
                                            />
                                            {{
                                                replyingTo === question.id
                                                    ? 'Cancelar'
                                                    : question.answers.length >
                                                        0
                                                      ? 'Añadir respuesta'
                                                      : 'Responder'
                                            }}
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <!-- Respuestas existentes -->
                            <div
                                v-if="
                                    question.answers.length > 0 ||
                                    replyingTo === question.id
                                "
                                class="border-t border-gray-100 bg-gray-50/50"
                            >
                                <div class="divide-y divide-gray-100">
                                    <div
                                        v-for="answer in question.answers"
                                        :key="answer.id"
                                        class="flex gap-3 px-5 py-4 pl-14"
                                    >
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100"
                                            >
                                                <span
                                                    class="text-sm font-semibold text-emerald-600"
                                                >
                                                    {{ answer.user.name[0] }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div
                                                class="mb-1 flex items-center gap-2"
                                            >
                                                <span
                                                    class="text-sm font-semibold text-gray-900"
                                                >
                                                    {{ answer.user.name }}
                                                </span>
                                                <Badge
                                                    variant="secondary"
                                                    class="text-xs"
                                                >
                                                    Instructor
                                                </Badge>
                                                <span
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            answer.created_at,
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-sm whitespace-pre-wrap text-gray-700"
                                            >
                                                {{ answer.answer }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulario de respuesta -->
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
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100"
                                            >
                                                <span
                                                    class="text-sm font-semibold text-emerald-600"
                                                    >T</span
                                                >
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <textarea
                                                v-model="
                                                    answerForms[question.id]
                                                        .answer
                                                "
                                                rows="3"
                                                class="w-full rounded-lg border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="Escribe tu respuesta al estudiante..."
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
                                                class="mt-2 flex justify-end gap-2"
                                            >
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    @click="replyingTo = null"
                                                >
                                                    Cancelar
                                                </Button>
                                                <Button
                                                    type="submit"
                                                    size="sm"
                                                    :disabled="
                                                        answerForms[question.id]
                                                            ?.processing
                                                    "
                                                >
                                                    <Send
                                                        class="mr-1.5 h-3.5 w-3.5"
                                                    />
                                                    Enviar respuesta
                                                </Button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div
                            v-if="questions.last_page > 1"
                            class="flex items-center justify-between border-t pt-4"
                        >
                            <p class="text-sm text-gray-600">
                                Página {{ questions.current_page }} de
                                {{ questions.last_page }}
                            </p>
                            <div class="flex gap-2">
                                <Button
                                    v-if="questions.prev_page_url"
                                    variant="outline"
                                    size="sm"
                                    as="a"
                                    :href="questions.prev_page_url"
                                >
                                    Anterior
                                </Button>
                                <Button
                                    v-if="questions.next_page_url"
                                    variant="outline"
                                    size="sm"
                                    as="a"
                                    :href="questions.next_page_url"
                                >
                                    Siguiente
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
