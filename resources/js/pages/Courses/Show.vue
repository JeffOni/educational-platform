<script setup lang="ts">
import { login, register } from '@/routes';
import { Head, Link, router } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';

const props = defineProps<{
    course: any;
    hasPurchased: boolean;
}>();

const totalLessons = props.course.sections.reduce(
    (acc: number, section: any) => acc + section.lessons.length,
    0,
);

const addToCart = () => {
    router.post(
        `/cart/add/${props.course.id}`,
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <Head :title="course.title" />

    <div
        class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white"
    >
        <!-- Navbar Simple -->
        <nav
            class="border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-950"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link href="/" class="text-xl font-bold">EduPlatform</Link>
                <div class="flex gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
                        class="font-semibold transition hover:text-indigo-500"
                        >Dashboard</Link
                    >
                    <template v-else>
                        <Link
                            :href="login()"
                            class="font-semibold transition hover:text-indigo-500"
                            >Iniciar Sesión</Link
                        >
                        <Link
                            :href="register()"
                            class="rounded-full bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-700"
                            >Registrarse</Link
                        >
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero del Curso -->
        <div
            class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16 text-white"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 lg:grid-cols-3"
            >
                <div class="lg:col-span-2">
                    <p class="mb-2 text-sm opacity-90">
                        {{ course.category?.name || 'Sin categoría' }}
                    </p>
                    <h1 class="mb-4 text-4xl font-extrabold lg:text-5xl">
                        {{ course.title }}
                    </h1>
                    <p class="mb-6 text-xl opacity-90">{{ course.subtitle }}</p>

                    <div class="flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-400">★★★★★</span>
                            <span>4.8 (120 valoraciones)</span>
                        </div>
                        <div>{{ totalLessons }} lecciones</div>
                        <div>
                            {{ course.level?.name || 'Todos los niveles' }}
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-lg font-bold"
                        >
                            {{ course.teacher.name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm opacity-75">Creado por</p>
                            <p class="font-semibold">
                                {{ course.teacher.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card de Compra -->
                <div
                    class="h-fit rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800"
                >
                    <img
                        v-if="course.image_path"
                        :src="'/storage/' + course.image_path"
                        class="mb-4 h-48 w-full rounded-lg object-cover"
                    />
                    <div
                        v-else
                        class="mb-4 flex h-48 w-full items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700"
                    >
                        <span class="text-6xl">📚</span>
                    </div>

                    <div class="mb-6 text-center">
                        <p
                            class="text-4xl font-bold text-gray-900 dark:text-white"
                        >
                            ${{ course.price }}
                        </p>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Pago único • Acceso de por vida
                        </p>
                    </div>

                    <template v-if="hasPurchased">
                        <Link
                            :href="'/student/courses/' + course.id"
                            class="block w-full rounded-xl bg-green-600 py-4 text-center text-lg font-bold text-white shadow-lg transition hover:bg-green-700"
                        >
                            Ir al Curso →
                        </Link>
                    </template>
                    <template v-else>
                        <div class="space-y-3">
                            <button
                                @click="addToCart"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-4 text-lg font-bold text-white shadow-lg transition hover:from-indigo-700 hover:to-purple-700"
                            >
                                <ShoppingCart :size="20" />
                                Agregar al Carrito
                            </button>
                            <p
                                class="text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                o
                            </p>
                            <form
                                v-if="$page.props.auth.user"
                                method="POST"
                                :action="
                                    '/student/courses/' +
                                    course.id +
                                    '/purchase'
                                "
                            >
                                <input
                                    type="hidden"
                                    name="_token"
                                    :value="$page.props.csrf_token"
                                />
                                <button
                                    type="submit"
                                    class="w-full rounded-xl border-2 border-indigo-600 bg-white py-4 text-lg font-bold text-indigo-600 transition hover:bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400 dark:hover:bg-gray-700"
                                >
                                    Comprar Ahora
                                </button>
                            </form>
                            <Link
                                v-else
                                :href="login()"
                                class="block w-full rounded-xl border-2 border-indigo-600 bg-white py-4 text-center text-lg font-bold text-indigo-600 transition hover:bg-indigo-50 dark:bg-gray-800 dark:text-indigo-400 dark:hover:bg-gray-700"
                            >
                                Inicia Sesión para Comprar
                            </Link>
                        </div>
                    </template>

                    <div
                        class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <div class="flex items-center gap-2">
                            <span>✓</span> Acceso de por vida
                        </div>
                        <div class="flex items-center gap-2">
                            <span>✓</span> {{ totalLessons }} lecciones bajo
                            demanda
                        </div>
                        <div class="flex items-center gap-2">
                            <span>✓</span> Certificado de finalización
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido del Curso -->
        <div class="mx-auto max-w-7xl px-6 py-16">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <!-- Contenido Principal -->
                <div class="space-y-12 lg:col-span-2">
                    <!-- Lo que aprenderás -->
                    <section>
                        <h2 class="mb-6 text-3xl font-bold">
                            Lo que aprenderás
                        </h2>
                        <div
                            class="rounded-2xl bg-gray-50 p-8 dark:bg-gray-800"
                        >
                            <p
                                class="leading-relaxed whitespace-pre-line text-gray-700 dark:text-gray-300"
                            >
                                {{ course.description }}
                            </p>
                        </div>
                    </section>

                    <!-- Contenido del curso (Curriculum) -->
                    <section>
                        <h2 class="mb-6 text-3xl font-bold">
                            Contenido del curso
                        </h2>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ course.sections.length }} secciones •
                            {{ totalLessons }} lecciones
                        </p>

                        <div class="space-y-4">
                            <div
                                v-for="section in course.sections"
                                :key="section.id"
                                class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
                            >
                                <div
                                    class="flex items-center justify-between bg-gray-100 px-6 py-4 font-semibold dark:bg-gray-800"
                                >
                                    <span>{{ section.name }}</span>
                                    <span class="text-sm text-gray-500"
                                        >{{
                                            section.lessons.length
                                        }}
                                        lecciones</span
                                    >
                                </div>
                                <ul
                                    class="divide-y divide-gray-200 dark:divide-gray-700"
                                >
                                    <li
                                        v-for="lesson in section.lessons"
                                        :key="lesson.id"
                                        class="flex items-center gap-3 px-6 py-3 text-gray-700 dark:text-gray-300"
                                    >
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
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        <h3 class="mb-4 font-bold">Sobre el instructor</h3>
                        <div class="mb-4 flex items-center gap-3">
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-2xl font-bold text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300"
                            >
                                {{ course.teacher.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-lg font-semibold">
                                    {{ course.teacher.name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Instructor experto
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
