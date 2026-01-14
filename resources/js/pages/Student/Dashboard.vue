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
import { Progress } from '@/components/ui/progress';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    Award,
    BookOpen,
    Calendar,
    CheckCircle2,
    Clock,
    TrendingUp,
} from 'lucide-vue-next';

interface Course {
    id: number;
    title: string;
    image_path: string;
    progress: number;
    total_lessons: number;
    completed_lessons: number;
    instructor: {
        name: string;
    };
    last_accessed: string;
}

interface Assignment {
    id: number;
    title: string;
    due_date: string;
    lesson: {
        name: string;
        section: {
            course: {
                id: number;
                title: string;
            };
        };
    };
    submission?: {
        grade: number | null;
    };
}

interface Props {
    enrolledCourses: Course[];
    pendingAssignments: Assignment[];
    stats: {
        total_courses: number;
        completed_courses: number;
        total_hours: number;
        certificates: number;
    };
}

const props = defineProps<Props>();

const goToCourse = (courseId: number) => {
    router.visit(`/student/courses/${courseId}`);
};

const formatDueDate = (date: string) => {
    const dueDate = new Date(date);
    const now = new Date();
    const diff = dueDate.getTime() - now.getTime();
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    if (days < 0) return 'Vencida';
    if (days === 0) return 'Vence hoy';
    if (days === 1) return 'Vence mañana';
    return `Vence en ${days} días`;
};

const getDueDateColor = (date: string) => {
    const dueDate = new Date(date);
    const now = new Date();
    const diff = dueDate.getTime() - now.getTime();
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));

    if (days < 0) return 'bg-red-100 text-red-800';
    if (days <= 2) return 'bg-orange-100 text-orange-800';
    return 'bg-blue-100 text-blue-800';
};
</script>

<template>
    <Head title="Mi Dashboard" />

    <StudentLayout>
        <div class="">
            <div class="">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">
                        ¡Bienvenido de nuevo! 👋
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Continúa tu aprendizaje y alcanza tus metas
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-sm font-medium text-gray-600"
                                >
                                    Cursos Activos
                                </CardTitle>
                                <BookOpen class="h-5 w-5 text-blue-600" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats.total_courses }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-sm font-medium text-gray-600"
                                >
                                    Completados
                                </CardTitle>
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats.completed_courses }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-sm font-medium text-gray-600"
                                >
                                    Horas Aprendidas
                                </CardTitle>
                                <Clock class="h-5 w-5 text-purple-600" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats.total_hours }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-sm font-medium text-gray-600"
                                >
                                    Certificados
                                </CardTitle>
                                <Award class="h-5 w-5 text-yellow-600" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ stats.certificates }}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Continue Learning -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <TrendingUp class="h-5 w-5" />
                                    Continuar Aprendiendo
                                </CardTitle>
                                <CardDescription>
                                    Retoma donde lo dejaste
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="enrolledCourses.length === 0"
                                    class="py-12 text-center"
                                >
                                    <BookOpen
                                        class="mx-auto h-16 w-16 text-gray-300"
                                    />
                                    <p class="mt-4 text-gray-600">
                                        Aún no estás inscrito en ningún curso
                                    </p>
                                    <Button
                                        class="mt-4"
                                        @click="router.visit('/')"
                                    >
                                        Explorar Cursos
                                    </Button>
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="course in enrolledCourses"
                                        :key="course.id"
                                        class="flex cursor-pointer gap-4 rounded-lg border p-4 transition-all hover:shadow-md"
                                        @click="goToCourse(course.id)"
                                    >
                                        <img
                                            :src="`/storage/${course.image_path}`"
                                            :alt="course.title"
                                            class="h-24 w-32 rounded-lg object-cover"
                                        />
                                        <div class="flex-1">
                                            <h3
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ course.title }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ course.instructor.name }}
                                            </p>
                                            <div class="mt-2">
                                                <div
                                                    class="flex items-center justify-between text-sm"
                                                >
                                                    <span class="text-gray-600">
                                                        {{
                                                            course.completed_lessons
                                                        }}/{{
                                                            course.total_lessons
                                                        }}
                                                        lecciones
                                                    </span>
                                                    <span
                                                        class="font-medium text-blue-600"
                                                    >
                                                        {{ course.progress }}%
                                                    </span>
                                                </div>
                                                <Progress
                                                    :model-value="
                                                        course.progress
                                                    "
                                                    class="mt-2"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Pending Assignments -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Calendar class="h-5 w-5" />
                                    Tareas Pendientes
                                </CardTitle>
                                <CardDescription>
                                    Próximas entregas
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div
                                    v-if="pendingAssignments.length === 0"
                                    class="py-8 text-center"
                                >
                                    <CheckCircle2
                                        class="mx-auto h-12 w-12 text-green-300"
                                    />
                                    <p class="mt-2 text-sm text-gray-600">
                                        ¡Todo al día!
                                    </p>
                                </div>

                                <div v-else class="space-y-3">
                                    <div
                                        v-for="assignment in pendingAssignments"
                                        :key="assignment.id"
                                        class="rounded-lg border p-3 hover:bg-gray-50"
                                    >
                                        <div
                                            class="mb-2 flex items-start justify-between"
                                        >
                                            <h4
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ assignment.title }}
                                            </h4>
                                            <Badge
                                                :class="
                                                    getDueDateColor(
                                                        assignment.due_date,
                                                    )
                                                "
                                                class="ml-2 shrink-0"
                                            >
                                                {{
                                                    formatDueDate(
                                                        assignment.due_date,
                                                    )
                                                }}
                                            </Badge>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{
                                                assignment.lesson.section.course
                                                    .title
                                            }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ assignment.lesson.name }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
