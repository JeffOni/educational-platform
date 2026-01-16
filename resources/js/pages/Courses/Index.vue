<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    price: number;
    image_path: string | null;
    teacher: Teacher;
    category: Category;
    level: Level;
}

interface Props {
    courses: {
        data: Course[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    categories: Category[];
    levels: Level[];
    filters: {
        category?: string;
        level?: string;
        search?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedLevel = ref(props.filters.level || '');
const selectedSort = ref(props.filters.sort || 'latest');

const applyFilters = () => {
    router.get(
        '/courses',
        {
            search: searchQuery.value || undefined,
            category: selectedCategory.value || undefined,
            level: selectedLevel.value || undefined,
            sort: selectedSort.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

watch([selectedCategory, selectedLevel, selectedSort], () => {
    applyFilters();
});

const handleSearch = () => {
    applyFilters();
};
</script>

<template>
    <Head title="Todos los Cursos" />

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
        <!-- Navbar -->
        <nav
            class="fixed top-0 z-50 w-full border-b border-gray-200/50 bg-white/80 backdrop-blur-lg"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2 text-2xl font-bold"
                >
                    <div
                        class="rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 p-2 text-white"
                    >
                        <BookOpen :size="24" />
                    </div>
                    <span
                        class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"
                    >
                        EduPlatform
                    </span>
                </Link>

                <div class="flex items-center gap-4">
                    <Link
                        href="/"
                        class="font-medium text-gray-700 transition-colors hover:text-indigo-600"
                    >
                        Inicio
                    </Link>
                    <Link
                        v-if="$page.props.auth?.user"
                        href="/dashboard"
                        class="rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 font-bold text-white transition-all hover:scale-105"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else
                        href="/login"
                        class="rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 font-bold text-white transition-all hover:scale-105"
                    >
                        Iniciar Sesión
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-6 pt-28 pb-16">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="mb-4 text-4xl font-bold md:text-5xl">
                    Explora Nuestros Cursos
                </h1>
                <p class="text-xl text-gray-600">
                    {{ courses.data.length }} cursos disponibles para ti
                </p>
            </div>

            <!-- Filters -->
            <div
                class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-lg"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-gray-400"
                            />
                            <input
                                v-model="searchQuery"
                                @keyup.enter="handleSearch"
                                type="text"
                                placeholder="Buscar cursos..."
                                class="w-full rounded-xl border border-gray-300 py-3 pr-4 pl-10 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select
                            v-model="selectedCategory"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                        >
                            <option value="">Todas las categorías</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <select
                            v-model="selectedSort"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                        >
                            <option value="latest">Más recientes</option>
                            <option value="popular">Más populares</option>
                            <option value="price_low">
                                Precio: Menor a Mayor
                            </option>
                            <option value="price_high">
                                Precio: Mayor a Menor
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Courses Grid -->
            <div
                v-if="courses.data.length > 0"
                class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    :href="`/courses/${course.id}`"
                    v-for="course in courses.data"
                    :key="course.id"
                    class="group transform overflow-hidden rounded-3xl border border-gray-200 bg-white transition-all duration-300 hover:-translate-y-2 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/20"
                >
                    <!-- Image -->
                    <div
                        class="relative h-56 overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100"
                    >
                        <img
                            v-if="course.image_path"
                            :src="`/storage/${course.image_path}`"
                            :alt="course.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <BookOpen :size="64" class="text-indigo-300" />
                        </div>

                        <!-- Price Badge -->
                        <div
                            class="absolute top-4 right-4 rounded-full border border-gray-200 bg-white px-4 py-2 shadow-lg backdrop-blur-sm"
                        >
                            <span
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-lg font-bold text-transparent"
                            >
                                ${{ course.price }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Teacher -->
                        <div class="mb-4 flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 font-bold text-white shadow-lg"
                            >
                                {{
                                    course.teacher.name.charAt(0).toUpperCase()
                                }}
                            </div>
                            <div>
                                <div
                                    class="text-sm font-semibold text-gray-900"
                                >
                                    {{ course.teacher.name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Instructor
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3
                            class="mb-2 text-xl font-bold text-gray-900 transition-colors group-hover:text-indigo-600"
                        >
                            {{ course.title }}
                        </h3>

                        <!-- Category & Level -->
                        <div class="mb-4 flex gap-2">
                            <span
                                class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700"
                            >
                                {{ course.category.name }}
                            </span>
                            <span
                                class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700"
                            >
                                {{ course.level.name }}
                            </span>
                        </div>

                        <!-- Subtitle -->
                        <p class="line-clamp-2 text-gray-600">
                            {{ course.subtitle }}
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="rounded-2xl border border-gray-200 bg-white p-12 text-center"
            >
                <BookOpen class="mx-auto h-24 w-24 text-gray-300" />
                <h3 class="mt-4 text-xl font-semibold text-gray-900">
                    No se encontraron cursos
                </h3>
                <p class="mt-2 text-gray-600">
                    Intenta ajustar tus filtros de búsqueda
                </p>
            </div>

            <!-- Pagination -->
            <div
                v-if="courses.last_page > 1"
                class="mt-12 flex justify-center gap-2"
            >
                <Link
                    v-for="link in courses.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    :class="[
                        'rounded-lg px-4 py-2 font-medium transition-colors',
                        link.active
                            ? 'bg-indigo-600 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'cursor-not-allowed opacity-50',
                    ]"
                    v-html="link.label"
                />
            </div>
        </main>
    </div>
</template>
