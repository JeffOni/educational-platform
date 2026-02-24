<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, GraduationCap } from 'lucide-vue-next';

interface Enrollment {
    id: number;
    course: {
        id: number;
        title: string;
        image_path: string | null;
        teacher: {
            name: string;
        };
    };
    progress: number;
    total_lessons: number;
    completed_lessons: number;
}

defineProps<{
    enrolledCourses: Enrollment[];
}>();
</script>

<template>
    <Head title="Mis Cursos" />

    <StudentLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mis Cursos</h1>
            <p class="mt-2 text-gray-600">
                Todos los cursos en los que estás inscrito
            </p>
        </div>

        <div v-if="enrolledCourses.length === 0">
            <Card>
                <CardContent class="py-16 text-center">
                    <BookOpen class="mx-auto h-16 w-16 text-gray-300" />
                    <p class="mt-4 text-lg font-medium text-gray-600">
                        Aún no estás inscrito en ningún curso
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Utiliza un código de inscripción para acceder a un curso
                    </p>
                </CardContent>
            </Card>
        </div>

        <div
            v-else
            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="enrollment in enrolledCourses"
                :key="enrollment.id"
                :href="'/student/courses/' + enrollment.course.id"
                class="group block"
            >
                <Card
                    class="overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="relative h-40 overflow-hidden bg-gray-100">
                        <img
                            v-if="enrollment.course.image_path"
                            :src="'/storage/' + enrollment.course.image_path"
                            :alt="enrollment.course.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <GraduationCap class="h-12 w-12 text-gray-300" />
                        </div>
                    </div>
                    <CardHeader class="pb-3">
                        <CardDescription>
                            {{ enrollment.course.teacher.name }}
                        </CardDescription>
                        <CardTitle
                            class="line-clamp-2 text-lg transition-colors group-hover:text-blue-600"
                        >
                            {{ enrollment.course.title }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div>
                            <div
                                class="mb-2 flex justify-between text-xs text-gray-600"
                            >
                                <span
                                    >{{ enrollment.completed_lessons }}/{{
                                        enrollment.total_lessons
                                    }}
                                    lecciones</span
                                >
                                <span class="font-medium text-blue-600"
                                    >{{ enrollment.progress }}%</span
                                >
                            </div>
                            <Progress :model-value="enrollment.progress" />
                        </div>
                        <div
                            class="mt-4 flex items-center gap-1 text-sm font-semibold text-blue-600"
                        >
                            {{
                                enrollment.progress > 0
                                    ? 'Continuar curso'
                                    : 'Comenzar curso'
                            }}
                            <span>&rarr;</span>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </StudentLayout>
</template>
