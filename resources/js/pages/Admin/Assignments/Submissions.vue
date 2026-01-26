<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCheck, Download, FileText, User } from 'lucide-vue-next';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
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
    user: User;
}

interface Assignment {
    id: number;
    title: string;
    description: string;
    due_date: string | null;
    max_points: number;
    lesson: {
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
    };
}

interface Props {
    assignment: Assignment;
    submissions: Submission[];
}

const props = defineProps<Props>();

const gradingSubmission = ref<number | null>(null);

const gradeForm = useForm({
    grade: 0,
    feedback: '',
});

const startGrading = (submission: Submission) => {
    gradingSubmission.value = submission.id;
    gradeForm.grade = submission.grade || 0;
    gradeForm.feedback = submission.feedback || '';
};

const submitGrade = (submissionId: number) => {
    gradeForm.post(`/admin/submissions/${submissionId}/grade`, {
        preserveScroll: true,
        onSuccess: () => {
            gradingSubmission.value = null;
            gradeForm.reset();
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

const downloadFile = (submissionId: number) => {
    router.visit(`/student/submissions/${submissionId}/download`);
};

const getSubmissionStatus = (submission: Submission) => {
    if (submission.grade !== null) {
        return { text: 'Calificada', color: 'bg-green-100 text-green-800' };
    }
    return { text: 'Pendiente', color: 'bg-yellow-100 text-yellow-800' };
};

const gradedCount = props.submissions.filter((s) => s.grade !== null).length;
const pendingCount = props.submissions.length - gradedCount;
</script>

<template>
    <Head :title="`Entregas - ${assignment.title}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="w-full sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="mb-6 text-sm text-gray-600">
                    <Button
                        variant="link"
                        class="h-auto p-0 hover:text-gray-900"
                        @click="
                            router.visit(
                                `/admin/courses/${assignment.lesson.section.course.id}/edit`,
                            )
                        "
                    >
                        {{ assignment.lesson.section.course.title }}
                    </Button>
                    <span class="mx-2">/</span>
                    <span>{{ assignment.lesson.section.name }}</span>
                    <span class="mx-2">/</span>
                    <span>{{ assignment.lesson.name }}</span>
                    <span class="mx-2">/</span>
                    <span class="font-semibold text-gray-900">{{
                        assignment.title
                    }}</span>
                </div>

                <!-- Header -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="text-2xl">{{
                            assignment.title
                        }}</CardTitle>
                        <CardDescription>{{
                            assignment.description
                        }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div class="rounded-lg bg-blue-50 p-4">
                                <div class="text-sm font-medium text-blue-600">
                                    Total Entregas
                                </div>
                                <div class="text-3xl font-bold text-blue-900">
                                    {{ submissions.length }}
                                </div>
                            </div>
                            <div class="rounded-lg bg-green-50 p-4">
                                <div class="text-sm font-medium text-green-600">
                                    Calificadas
                                </div>
                                <div class="text-3xl font-bold text-green-900">
                                    {{ gradedCount }}
                                </div>
                            </div>
                            <div class="rounded-lg bg-yellow-50 p-4">
                                <div
                                    class="text-sm font-medium text-yellow-600"
                                >
                                    Pendientes
                                </div>
                                <div class="text-3xl font-bold text-yellow-900">
                                    {{ pendingCount }}
                                </div>
                            </div>
                            <div class="rounded-lg bg-purple-50 p-4">
                                <div
                                    class="text-sm font-medium text-purple-600"
                                >
                                    Puntos Máximos
                                </div>
                                <div class="text-3xl font-bold text-purple-900">
                                    {{ assignment.max_points }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submissions List -->
                <Card>
                    <CardHeader>
                        <CardTitle>Entregas de Estudiantes</CardTitle>
                        <CardDescription>
                            Revisa y califica las tareas enviadas por los
                            estudiantes
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="submissions.length === 0"
                            class="py-12 text-center text-gray-500"
                        >
                            <FileText
                                class="mx-auto mb-4 h-16 w-16 text-gray-300"
                            />
                            <p class="text-lg font-medium">
                                No hay entregas aún
                            </p>
                            <p class="text-sm">
                                Los estudiantes aún no han enviado sus tareas
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="submission in submissions"
                                :key="submission.id"
                                class="overflow-hidden rounded-lg border"
                            >
                                <!-- Header -->
                                <div
                                    class="flex items-center justify-between border-b bg-gray-50 p-4"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full bg-indigo-100 p-2"
                                        >
                                            <User
                                                class="h-5 w-5 text-indigo-600"
                                            />
                                        </div>
                                        <div>
                                            <h3
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ submission.user.name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ submission.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Badge
                                            :class="
                                                getSubmissionStatus(submission)
                                                    .color
                                            "
                                        >
                                            {{
                                                getSubmissionStatus(submission)
                                                    .text
                                            }}
                                        </Badge>
                                        <div
                                            v-if="submission.grade !== null"
                                            class="text-right"
                                        >
                                            <div
                                                class="text-2xl font-bold text-green-600"
                                            >
                                                {{ submission.grade }}/{{
                                                    assignment.max_points
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <div class="mb-4">
                                        <div class="mb-1 text-xs text-gray-500">
                                            Enviado el:
                                        </div>
                                        <div class="text-sm font-medium">
                                            {{
                                                formatDate(
                                                    submission.created_at,
                                                )
                                            }}
                                        </div>
                                        <div
                                            v-if="
                                                submission.created_at !==
                                                submission.updated_at
                                            "
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            Actualizado:
                                            {{
                                                formatDate(
                                                    submission.updated_at,
                                                )
                                            }}
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div
                                            class="mb-2 text-sm font-medium text-gray-700"
                                        >
                                            Respuesta del estudiante:
                                        </div>
                                        <div
                                            class="rounded-lg bg-gray-50 p-3 whitespace-pre-wrap text-gray-900"
                                        >
                                            {{ submission.content }}
                                        </div>
                                    </div>

                                    <!-- Archivo adjunto -->
                                    <div
                                        v-if="submission.file_path"
                                        class="mb-4 rounded-lg bg-blue-50 p-3"
                                    >
                                        <button
                                            @click="downloadFile(submission.id)"
                                            class="flex items-center gap-2 font-medium text-blue-700 hover:text-blue-900"
                                        >
                                            <Download class="h-4 w-4" />
                                            {{ submission.file_name }}
                                        </button>
                                    </div>

                                    <!-- Feedback si ya está calificada -->
                                    <div
                                        v-if="
                                            submission.grade !== null &&
                                            submission.feedback
                                        "
                                        class="mb-4"
                                    >
                                        <div
                                            class="mb-2 text-sm font-medium text-gray-700"
                                        >
                                            Tu retroalimentación:
                                        </div>
                                        <div
                                            class="rounded-lg border border-green-200 bg-green-50 p-3 text-gray-900"
                                        >
                                            {{ submission.feedback }}
                                        </div>
                                    </div>

                                    <!-- Botón de calificar -->
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button
                                                @click="
                                                    startGrading(submission)
                                                "
                                                class="w-full"
                                                :variant="
                                                    submission.grade !== null
                                                        ? 'outline'
                                                        : 'default'
                                                "
                                            >
                                                <CheckCheck
                                                    class="mr-2 h-4 w-4"
                                                />
                                                {{
                                                    submission.grade !== null
                                                        ? 'Editar Calificación'
                                                        : 'Calificar Tarea'
                                                }}
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="max-w-md">
                                            <DialogHeader>
                                                <DialogTitle
                                                    >Calificar
                                                    Tarea</DialogTitle
                                                >
                                                <DialogDescription>
                                                    Asigna una calificación y
                                                    proporciona
                                                    retroalimentación para
                                                    {{ submission.user.name }}
                                                </DialogDescription>
                                            </DialogHeader>

                                            <form
                                                @submit.prevent="
                                                    submitGrade(submission.id)
                                                "
                                                class="space-y-4"
                                            >
                                                <div class="space-y-2">
                                                    <Label
                                                        >Calificación (0-{{
                                                            assignment.max_points
                                                        }}
                                                        puntos)</Label
                                                    >
                                                    <Input
                                                        v-model.number="
                                                            gradeForm.grade
                                                        "
                                                        type="number"
                                                        :min="0"
                                                        :max="
                                                            assignment.max_points
                                                        "
                                                        step="0.5"
                                                        required
                                                    />
                                                </div>

                                                <div class="space-y-2">
                                                    <Label
                                                        >Retroalimentación
                                                        (opcional)</Label
                                                    >
                                                    <Textarea
                                                        v-model="
                                                            gradeForm.feedback
                                                        "
                                                        rows="4"
                                                        placeholder="Escribe comentarios para el estudiante..."
                                                    />
                                                </div>

                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <DialogTrigger as-child>
                                                        <Button
                                                            type="button"
                                                            variant="outline"
                                                        >
                                                            Cancelar
                                                        </Button>
                                                    </DialogTrigger>
                                                    <Button
                                                        type="submit"
                                                        :disabled="
                                                            gradeForm.processing
                                                        "
                                                    >
                                                        Guardar Calificación
                                                    </Button>
                                                </div>
                                            </form>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
