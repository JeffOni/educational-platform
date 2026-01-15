<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import { ShoppingCart } from 'lucide-vue-next';

const props = defineProps<{
    course: any;
    hasPurchased: boolean;
}>();

const totalLessons = props.course.sections.reduce((acc: number, section: any) => acc + section.lessons.length, 0);

const addToCart = () => {
    router.post(`/cart/add/${props.course.id}`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="course.title" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
        
        <!-- Navbar Simple -->
        <nav class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <Link href="/" class="text-xl font-bold">EduPlatform</Link>
                <div class="flex gap-4">
                    <Link v-if="$page.props.auth.user" href="/dashboard" class="font-semibold hover:text-indigo-500 transition">Dashboard</Link>
                    <template v-else>
                        <Link :href="login()" class="font-semibold hover:text-indigo-500 transition">Iniciar Sesión</Link>
                        <Link :href="register()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full font-medium transition">Registrarse</Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero del Curso -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <p class="text-sm mb-2 opacity-90">{{ course.category?.name || 'Sin categoría' }}</p>
                    <h1 class="text-4xl lg:text-5xl font-extrabold mb-4">{{ course.title }}</h1>
                    <p class="text-xl mb-6 opacity-90">{{ course.subtitle }}</p>
                    
                    <div class="flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-400">★★★★★</span>
                            <span>4.8 (120 valoraciones)</span>
                        </div>
                        <div>{{ totalLessons }} lecciones</div>
                        <div>{{ course.level?.name || 'Todos los niveles' }}</div>
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center font-bold text-lg">
                            {{ course.teacher.name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm opacity-75">Creado por</p>
                            <p class="font-semibold">{{ course.teacher.name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card de Compra -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 h-fit">
                    <img v-if="course.image_path" :src="'/storage/' + course.image_path" class="w-full h-48 object-cover rounded-lg mb-4" />
                    <div v-else class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center">
                        <span class="text-6xl">📚</span>
                    </div>

                    <div class="text-center mb-6">
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">${{ course.price }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pago único • Acceso de por vida</p>
                    </div>

                    <template v-if="hasPurchased">
                        <Link :href="'/student/courses/' + course.id" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-4 rounded-xl font-bold text-lg transition shadow-lg">
                            Ir al Curso →
                        </Link>
                    </template>
                    <template v-else>
                        <div class="space-y-3">
                            <button
                                @click="addToCart"
                                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-4 rounded-xl font-bold text-lg transition shadow-lg flex items-center justify-center gap-2"
                            >
                                <ShoppingCart :size="20" />
                                Agregar al Carrito
                            </button>
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                                o
                            </p>
                            <form v-if="$page.props.auth.user" method="POST" :action="'/student/courses/' + course.id + '/purchase'">
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="w-full bg-white dark:bg-gray-800 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 py-4 rounded-xl font-bold text-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700">
                                    Comprar Ahora
                                </button>
                            </form>
                            <Link v-else :href="login()" class="block w-full bg-white dark:bg-gray-800 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 text-center py-4 rounded-xl font-bold text-lg transition hover:bg-indigo-50 dark:hover:bg-gray-700">
                                Inicia Sesión para Comprar
                            </Link>
                        </div>
                    </template>

                    <div class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <span>✓</span> Acceso de por vida
                        </div>
                        <div class="flex items-center gap-2">
                            <span>✓</span> {{ totalLessons }} lecciones bajo demanda
                        </div>
                        <div class="flex items-center gap-2">
                            <span>✓</span> Certificado de finalización
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido del Curso -->
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contenido Principal -->
                <div class="lg:col-span-2 space-y-12">
                    
                    <!-- Lo que aprenderás -->
                    <section>
                        <h2 class="text-3xl font-bold mb-6">Lo que aprenderás</h2>
                        <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-2xl">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ course.description }}</p>
                        </div>
                    </section>

                    <!-- Contenido del curso (Curriculum) -->
                    <section>
                        <h2 class="text-3xl font-bold mb-6">Contenido del curso</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ course.sections.length }} secciones • {{ totalLessons }} lecciones</p>
                        
                        <div class="space-y-4">
                            <div v-for="section in course.sections" :key="section.id" class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                <div class="bg-gray-100 dark:bg-gray-800 px-6 py-4 font-semibold flex justify-between items-center">
                                    <span>{{ section.name }}</span>
                                    <span class="text-sm text-gray-500">{{ section.lessons.length }} lecciones</span>
                                </div>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li v-for="lesson in section.lessons" :key="lesson.id" class="px-6 py-3 flex items-center gap-3 text-gray-700 dark:text-gray-300">
                                        <span class="text-indigo-500">▶</span>
                                        {{ lesson.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>

                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="font-bold mb-4">Sobre el instructor</h3>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center font-bold text-2xl text-indigo-600 dark:text-indigo-300">
                                {{ course.teacher.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-semibold text-lg">{{ course.teacher.name }}</p>
                                <p class="text-sm text-gray-500">Instructor experto</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
