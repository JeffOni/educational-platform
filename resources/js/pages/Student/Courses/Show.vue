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
    CheckCircle,
    ChevronRight,
    ClipboardList,
    Clock,
    Download,
    FileText,
    GraduationCap,
    Layers,
    PlayCircle,
    User,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Lesson {
    id: number;
    name: string;
    duration: number | null;
    is_preview: boolean;
}

interface Section {
    id: number;
    name: string;
    lessons: Lesson[];
}

interface Teacher {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
}

interface Level {
    id: number;
    name: string;
}

interface Course {
    id: number;
    title: string;
    subtitle: string;
    description: string;
    image_path: string | null;
    teacher: Teacher;
    category: Category | null;
    level: Level | null;
    sections: Section[];
}

interface Props {
    course: Course;
    isEnrolled: boolean;
    progress: number;
    totalLessons: number;
    completedLessons: number;
    completedLessonIds: number[];
    hasExam: boolean;
    examPassed: boolean;
    certificate: { id: number; file_path: string } | null;
}

const props = defineProps<Props>();

const totalSections = computed(() => props.course.sections.length);

const totalDuration = computed(() => {
    const seconds = props.course.sections.reduce(
        (sum, section) =>
            sum +
            section.lessons.reduce(
                (lessonSum, lesson) => lessonSum + (lesson.duration || 0),
                0,
            ),
        0,
    );
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    if (hours > 0) return `${hours}h ${minutes}m`;
    return `${minutes}m`;
});

const firstLesson = computed(() => {
    const firstSection = props.course.sections[0];
    if (!firstSection || !firstSection.lessons[0]) return null;
    return firstSection.lessons[0];
});

// Encontrar la siguiente lección no completada para "Continuar"
const nextUncompletedLesson = computed(() => {
    for (const section of props.course.sections) {
        for (const lesson of section.lessons) {
            if (!props.completedLessonIds.includes(lesson.id)) {
                return lesson;
            }
        }
    }
    return firstLesson.value;
});

const isLessonCompleted = (lessonId: number) => {
    return props.completedLessonIds.includes(lessonId);
};

const formatDuration = (seconds: number | null) => {
    if (!seconds) return '';
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
};

const goToLesson = (lessonId: number) => {
    router.visit(`/student/courses/${props.course.id}/lessons/${lessonId}`);
};

const goToExam = () => {
    router.visit(`/student/courses/${props.course.id}/exam`);
};
</script>

<template>
    <Head :title="course.title" />

    <StudentLayout>
        <!-- Course Header -->
        <div class="mb-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <!-- Course Image -->
                <div
                    class="w-full overflow-hidden rounded-xl bg-gray-100 lg:w-80 lg:shrink-0"
                >
                    <img
                        v-if="course.image_path"
                        :src="'/storage/' + course.image_path"
                        :alt="course.title"
                        class="h-48 w-full object-cover lg:h-44"
                    />
                    <div
                        v-else
                        class="flex h-48 w-full items-center justify-center lg:h-44"
                    >
                        <GraduationCap class="h-16 w-16 text-gray-300" />
                    </div>
                </div>

                <!-- Course Info -->
                <div class="flex-1">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <Badge v-if="course.category" variant="outline">{{
                            course.category.name
                        }}</Badge>
                        <Badge v-if="course.level" variant="secondary">{{
                            course.level.name
                        }}</Badge>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ course.title }}
                    </h1>
                    <p
                        v-if="course.subtitle"
                        class="mt-2 text-lg text-gray-600"
                    >
                        {{ course.subtitle }}
                    </p>
                    <div
                        class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-600"
                    >
                        <div class="flex items-center gap-1.5">
                            <User class="h-4 w-4" />
                            <span>{{
                                course.teacher?.name ?? 'Sin instructor'
                            }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Layers class="h-4 w-4" />
                            <span>{{ totalSections }} secciones</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <PlayCircle class="h-4 w-4" />
                            <span>{{ totalLessons }} lecciones</span>
                        </div>
                        <div
                            v-if="totalDuration !== '0m'"
                            class="flex items-center gap-1.5"
                        >
                            <Clock class="h-4 w-4" />
                            <span>{{ totalDuration }}</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-5">
                        <div
                            class="mb-2 flex items-center justify-between text-sm"
                        >
                            <span class="text-gray-600"
                                >{{ completedLessons }}/{{
                                    totalLessons
                                }}
                                lecciones completadas</span
                            >
                            <span class="font-semibold text-blue-600"
                                >{{ progress }}%</span
                            >
                        </div>
                        <Progress :model-value="progress" />
                    </div>

                    <!-- Action Button -->
                    <div class="mt-5">
                        <Button
                            v-if="nextUncompletedLesson"
                            size="lg"
                            @click="goToLesson(nextUncompletedLesson.id)"
                        >
                            <PlayCircle class="mr-2 h-5 w-5" />
                            {{
                                completedLessons > 0
                                    ? 'Continuar aprendiendo'
                                    : 'Comenzar curso'
                            }}
                        </Button>
                        <Button
                            v-else-if="hasExam && !examPassed"
                            size="lg"
                            @click="goToExam"
                        >
                            <ClipboardList class="mr-2 h-5 w-5" />
                            Realizar examen final
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Description -->
                <Card v-if="course.description">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Descripción del curso
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="whitespace-pre-line text-gray-700">
                            {{ course.description }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Curriculum -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <BookOpen class="h-5 w-5" />
                            Contenido del curso
                        </CardTitle>
                        <CardDescription>
                            {{ totalSections }} secciones &middot;
                            {{ totalLessons }} lecciones
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div
                            v-for="(section, sIndex) in course.sections"
                            :key="section.id"
                            class="border-b last:border-b-0"
                        >
                            <!-- Section Header -->
                            <div
                                class="flex items-center justify-between bg-gray-50 px-6 py-4"
                            >
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        Sección {{ sIndex + 1 }}:
                                        {{ section.name }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        {{ section.lessons.length }}
                                        {{
                                            section.lessons.length === 1
                                                ? 'lección'
                                                : 'lecciones'
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Lessons -->
                            <div class="divide-y">
                                <button
                                    v-for="lesson in section.lessons"
                                    :key="lesson.id"
                                    class="flex w-full items-center gap-3 px-6 py-3 text-left transition-colors hover:bg-blue-50"
                                    @click="goToLesson(lesson.id)"
                                >
                                    <!-- Completed/Pending icon -->
                                    <CheckCircle
                                        v-if="isLessonCompleted(lesson.id)"
                                        class="h-5 w-5 shrink-0 text-green-500"
                                    />
                                    <PlayCircle
                                        v-else
                                        class="h-5 w-5 shrink-0 text-gray-400"
                                    />

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="truncate text-sm font-medium"
                                            :class="
                                                isLessonCompleted(lesson.id)
                                                    ? 'text-green-700'
                                                    : 'text-gray-900'
                                            "
                                        >
                                            {{ lesson.name }}
                                        </p>
                                    </div>
                                    <span
                                        v-if="lesson.duration"
                                        class="shrink-0 text-xs text-gray-500"
                                    >
                                        {{ formatDuration(lesson.duration) }}
                                    </span>
                                    <ChevronRight
                                        class="h-4 w-4 shrink-0 text-gray-400"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Exam Section -->
                        <div v-if="hasExam" class="border-b last:border-b-0">
                            <div
                                class="flex items-center justify-between bg-gray-50 px-6 py-4"
                            >
                                <h3 class="font-semibold text-gray-900">
                                    Evaluación Final
                                </h3>
                            </div>
                            <button
                                class="flex w-full items-center gap-3 px-6 py-3 text-left transition-colors hover:bg-blue-50"
                                @click="goToExam"
                            >
                                <CheckCircle
                                    v-if="examPassed"
                                    class="h-5 w-5 shrink-0 text-green-500"
                                />
                                <ClipboardList
                                    v-else
                                    class="h-5 w-5 shrink-0 text-orange-500"
                                />
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-sm font-medium"
                                        :class="
                                            examPassed
                                                ? 'text-green-700'
                                                : 'text-gray-900'
                                        "
                                    >
                                        Examen del curso
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            examPassed
                                                ? 'Aprobado'
                                                : 'Pendiente de aprobación'
                                        }}
                                    </p>
                                </div>
                                <Badge
                                    :variant="
                                        examPassed ? 'default' : 'secondary'
                                    "
                                >
                                    {{ examPassed ? 'Aprobado' : 'Pendiente' }}
                                </Badge>
                                <ChevronRight
                                    class="h-4 w-4 shrink-0 text-gray-400"
                                />
                            </button>
                        </div>

                        <div
                            v-if="course.sections.length === 0"
                            class="py-12 text-center text-gray-500"
                        >
                            Este curso aún no tiene contenido disponible.
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Progress Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Tu progreso</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4 text-center">
                            <div class="text-4xl font-bold text-blue-600">
                                {{ progress }}%
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ completedLessons }} de
                                {{ totalLessons }} lecciones
                            </p>
                        </div>
                        <Progress :model-value="progress" class="mb-4" />

                        <!-- Status checklist -->
                        <div class="space-y-3 border-t pt-4">
                            <div class="flex items-center gap-2 text-sm">
                                <CheckCircle
                                    class="h-4 w-4 shrink-0"
                                    :class="
                                        progress === 100
                                            ? 'text-green-500'
                                            : 'text-gray-300'
                                    "
                                />
                                <span
                                    :class="
                                        progress === 100
                                            ? 'text-green-700'
                                            : 'text-gray-600'
                                    "
                                >
                                    Completar todas las lecciones
                                </span>
                            </div>
                            <div
                                v-if="hasExam"
                                class="flex items-center gap-2 text-sm"
                            >
                                <CheckCircle
                                    class="h-4 w-4 shrink-0"
                                    :class="
                                        examPassed
                                            ? 'text-green-500'
                                            : 'text-gray-300'
                                    "
                                />
                                <span
                                    :class="
                                        examPassed
                                            ? 'text-green-700'
                                            : 'text-gray-600'
                                    "
                                >
                                    Aprobar el examen final
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <CheckCircle
                                    class="h-4 w-4 shrink-0"
                                    :class="
                                        certificate
                                            ? 'text-green-500'
                                            : 'text-gray-300'
                                    "
                                />
                                <span
                                    :class="
                                        certificate
                                            ? 'text-green-700'
                                            : 'text-gray-600'
                                    "
                                >
                                    Obtener certificado
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Certificate Download -->
                <Card v-if="certificate" class="border-green-200 bg-green-50">
                    <CardContent class="pt-6 text-center">
                        <Award class="mx-auto h-10 w-10 text-green-600" />
                        <p class="mt-3 text-sm font-semibold text-green-900">
                            Certificado disponible
                        </p>
                        <Button class="mt-4 w-full" variant="default" as-child>
                            <a
                                :href="`/student/certificates/${certificate.id}/download`"
                            >
                                <Download class="mr-2 h-4 w-4" />
                                Descargar certificado
                            </a>
                        </Button>
                    </CardContent>
                </Card>

                <!-- Quick Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Información</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600"
                                >Instructor</span
                            >
                            <span class="text-sm font-medium">{{
                                course.teacher?.name ?? 'Sin instructor'
                            }}</span>
                        </div>
                        <div
                            v-if="course.category"
                            class="flex items-center justify-between"
                        >
                            <span class="text-sm text-gray-600">Categoría</span>
                            <Badge variant="outline">{{
                                course.category.name
                            }}</Badge>
                        </div>
                        <div
                            v-if="course.level"
                            class="flex items-center justify-between"
                        >
                            <span class="text-sm text-gray-600">Nivel</span>
                            <Badge variant="secondary">{{
                                course.level.name
                            }}</Badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Secciones</span>
                            <span class="text-sm font-medium">{{
                                totalSections
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Lecciones</span>
                            <span class="text-sm font-medium">{{
                                totalLessons
                            }}</span>
                        </div>
                        <div
                            v-if="totalDuration !== '0m'"
                            class="flex items-center justify-between"
                        >
                            <span class="text-sm text-gray-600">Duración</span>
                            <span class="text-sm font-medium">{{
                                totalDuration
                            }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Continue CTA -->
                <Card
                    v-if="nextUncompletedLesson && progress < 100"
                    class="border-blue-200 bg-blue-50"
                >
                    <CardContent class="pt-6 text-center">
                        <GraduationCap
                            class="mx-auto h-10 w-10 text-blue-600"
                        />
                        <p class="mt-3 text-sm font-medium text-blue-900">
                            {{
                                completedLessons > 0
                                    ? 'Continúa donde lo dejaste'
                                    : 'Comienza tu aprendizaje'
                            }}
                        </p>
                        <Button
                            class="mt-4 w-full"
                            @click="goToLesson(nextUncompletedLesson.id)"
                        >
                            <PlayCircle class="mr-2 h-4 w-4" />
                            {{
                                completedLessons > 0 ? 'Continuar' : 'Comenzar'
                            }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Exam CTA -->
                <Card
                    v-else-if="progress === 100 && hasExam && !examPassed"
                    class="border-orange-200 bg-orange-50"
                >
                    <CardContent class="pt-6 text-center">
                        <ClipboardList
                            class="mx-auto h-10 w-10 text-orange-600"
                        />
                        <p class="mt-3 text-sm font-medium text-orange-900">
                            Has completado todas las lecciones
                        </p>
                        <p class="mt-1 text-xs text-orange-700">
                            Presenta el examen final para obtener tu certificado
                        </p>
                        <Button class="mt-4 w-full" @click="goToExam">
                            <ClipboardList class="mr-2 h-4 w-4" />
                            Ir al examen
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </StudentLayout>
</template>
