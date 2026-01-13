<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    enrolledCourses: Array<any>;
}>();
</script>

<template>
    <Head title="Mis Cursos" />

    <AppLayout :breadcrumbs="[{ title: 'Mis Cursos', href: '#' }]">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-gray-100">Mis Cursos</h1>

                    <div v-if="enrolledCourses.length === 0" class="text-center py-20">
                        <div class="text-6xl mb-4">📚</div>
                        <p class="text-xl text-gray-500 dark:text-gray-400 mb-4">Aún no tienes cursos comprados.</p>
                        <Link href="/" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            Explorar cursos disponibles →
                        </Link>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Link 
                            :href="'/student/courses/' + enrollment.course.id" 
                            v-for="enrollment in enrolledCourses" 
                            :key="enrollment.id"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                        >
                            <div class="relative h-40 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                                <img 
                                    v-if="enrollment.course.image_path" 
                                    :src="'/storage/' + enrollment.course.image_path" 
                                    :alt="enrollment.course.title" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                >
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="text-5xl">📚</span>
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ enrollment.course.teacher.name }}</p>
                                <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ enrollment.course.title }}
                                </h3>
                                
                                <!-- Barra de progreso (placeholder por ahora) -->
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        <span>Progreso</span>
                                        <span>0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 text-indigo-600 dark:text-indigo-400 text-sm font-semibold flex items-center gap-1">
                                    Continuar curso
                                    <span>→</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
