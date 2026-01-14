<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    enrolledCourses: Array<any>;
}>();
</script>

<template>
    <Head title="Mis Cursos" />

    <StudentLayout>
        <div>
            <div>
                <div
                    class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <h1
                        class="mb-8 text-3xl font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mis Cursos
                    </h1>

                    <div
                        v-if="enrolledCourses.length === 0"
                        class="py-20 text-center"
                    >
                        <div class="mb-4 text-6xl">📚</div>
                        <p
                            class="mb-4 text-xl text-gray-500 dark:text-gray-400"
                        >
                            Aún no tienes cursos comprados.
                        </p>
                        <Link
                            href="/"
                            class="font-semibold text-indigo-600 hover:text-indigo-800"
                        >
                            Explorar cursos disponibles →
                        </Link>
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <Link
                            :href="'/student/courses/' + enrollment.course.id"
                            v-for="enrollment in enrolledCourses"
                            :key="enrollment.id"
                            class="group overflow-hidden rounded-xl border border-gray-200 bg-gray-50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900"
                        >
                            <div
                                class="relative h-40 overflow-hidden bg-gray-200 dark:bg-gray-800"
                            >
                                <img
                                    v-if="enrollment.course.image_path"
                                    :src="
                                        '/storage/' +
                                        enrollment.course.image_path
                                    "
                                    :alt="enrollment.course.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-gray-400"
                                >
                                    <span class="text-5xl">📚</span>
                                </div>
                            </div>

                            <div class="p-5">
                                <p
                                    class="mb-2 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ enrollment.course.teacher.name }}
                                </p>
                                <h3
                                    class="mb-2 line-clamp-2 text-lg font-bold text-gray-900 transition-colors group-hover:text-indigo-600 dark:text-gray-100 dark:group-hover:text-indigo-400"
                                >
                                    {{ enrollment.course.title }}
                                </h3>

                                <!-- Barra de progreso (placeholder por ahora) -->
                                <div class="mt-4">
                                    <div
                                        class="mb-1 flex justify-between text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        <span>Progreso</span>
                                        <span>0%</span>
                                    </div>
                                    <div
                                        class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-2 rounded-full bg-indigo-600"
                                            style="width: 0%"
                                        ></div>
                                    </div>
                                </div>

                                <div
                                    class="mt-4 flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400"
                                >
                                    Continuar curso
                                    <span>→</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
