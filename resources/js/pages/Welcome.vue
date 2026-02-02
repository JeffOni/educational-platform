<script setup lang="ts">
import PublicNavbar from '@/components/PublicNavbar.vue';
import { login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import {
    ArrowRight,
    Award,
    BookOpen,
    CheckCircle2,
    Clock,
    Code2,
    Globe,
    GraduationCap,
    Laptop,
    Lightbulb,
    Play,
    Rocket,
    Shield,
    Sparkles,
    Star,
    Target,
    TrendingUp,
    Trophy,
    Users,
    Video,
    Zap,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

interface Teacher {
    id: number;
    name: string;
    email: string;
}

interface Course {
    id: number;
    title: string;
    subtitle: string;
    description: string;
    price: number;
    image_path: string | null;
    teacher: Teacher;
}

interface Props {
    canRegister: boolean;
    featuredCourses: Course[];
}

const props = defineProps<Props>();

const heroTitle = ref<HTMLElement | null>(null);
const heroSubtitle = ref<HTMLElement | null>(null);
const heroButtons = ref<HTMLElement | null>(null);
const cartCount = ref(0);
const activeCourseIndex = ref(0);

// Verificar si hay cursos
const hasCourses = computed(() => props.featuredCourses && props.featuredCourses.length > 0);

onMounted(async () => {
    // Cargar contador del carrito
    try {
        const response = await axios.get('/cart/count');
        cartCount.value = response.data.count;
    } catch (error) {
        console.error('Error loading cart count:', error);
    }

    // Animaciones GSAP mejoradas
    const tl = gsap.timeline();

    tl.from(heroTitle.value, {
        y: 80,
        opacity: 0,
        duration: 1.2,
        ease: 'power4.out',
    })
        .from(
            heroSubtitle.value,
            {
                y: 50,
                opacity: 0,
                duration: 1,
                ease: 'power4.out',
            },
            '-=0.8',
        )
        .from(
            heroButtons.value,
            {
                y: 30,
                opacity: 0,
                duration: 0.8,
                ease: 'power4.out',
            },
            '-=0.6',
        );

    // Animación de las tarjetas de cursos
    if (hasCourses.value) {
        gsap.from('.course-card', {
            y: 60,
            opacity: 0,
            duration: 1,
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.course-grid',
                start: 'top 85%',
            },
            ease: 'power3.out',
        });
    }

    // Animación de features
    gsap.from('.feature-card', {
        y: 40,
        opacity: 0,
        duration: 0.8,
        stagger: 0.15,
        scrollTrigger: {
            trigger: '.features-section',
            start: 'top 75%',
        },
        ease: 'power3.out',
    });

    // Animación de stats
    gsap.from('.stat-item', {
        scale: 0.5,
        opacity: 0,
        duration: 0.8,
        stagger: 0.12,
        scrollTrigger: {
            trigger: '.stats-section',
            start: 'top 80%',
        },
        ease: 'back.out(1.7)',
    });

    // Animación de categorías
    gsap.from('.category-item', {
        x: -30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.categories-section',
            start: 'top 80%',
        },
        ease: 'power2.out',
    });
});

const features = [
    {
        icon: Video,
        title: 'Clases en HD',
        description:
            'Contenido en alta definición con subtítulos y recursos descargables.',
        color: 'from-blue-600 to-cyan-600',
    },
    {
        icon: Users,
        title: 'Comunidad Global',
        description:
            'Conecta con estudiantes y profesionales de todo el mundo.',
        color: 'from-purple-600 to-pink-600',
    },
    {
        icon: Trophy,
        title: 'Certificados Pro',
        description:
            'Certificaciones reconocidas por empresas líderes del mercado.',
        color: 'from-orange-600 to-red-600',
    },
    {
        icon: Clock,
        title: 'Acceso Ilimitado',
        description:
            'Aprende a tu ritmo con acceso de por vida a todo el contenido.',
        color: 'from-green-600 to-emerald-600',
    },
    {
        icon: Target,
        title: 'Aprendizaje Guiado',
        description:
            'Rutas de aprendizaje personalizadas según tus objetivos.',
        color: 'from-indigo-600 to-purple-600',
    },
    {
        icon: Rocket,
        title: 'Proyectos Reales',
        description:
            'Aplica lo aprendido en proyectos del mundo real.',
        color: 'from-pink-600 to-rose-600',
    },
];

const categories = [
    { icon: Code2, name: 'Desarrollo Web', count: 120, color: 'bg-blue-500' },
    { icon: Laptop, name: 'Diseño UX/UI', count: 85, color: 'bg-purple-500' },
    { icon: GraduationCap, name: 'Negocios', count: 95, color: 'bg-green-500' },
    { icon: Lightbulb, name: 'Marketing', count: 70, color: 'bg-orange-500' },
    { icon: Shield, name: 'Ciberseguridad', count: 45, color: 'bg-red-500' },
    { icon: Sparkles, name: 'IA & ML', count: 60, color: 'bg-pink-500' },
];

const stats = [
    { value: '50K+', label: 'Estudiantes Activos', icon: Users },
    { value: '800+', label: 'Cursos Premium', icon: BookOpen },
    { value: '150+', label: 'Expertos', icon: Award },
    { value: '4.9/5', label: 'Calificación', icon: Star },
];

const benefits = [
    'Acceso ilimitado a todos los cursos',
    'Nuevos cursos cada semana',
    'Certificados verificables',
    'Soporte prioritario 24/7',
    'Proyectos prácticos incluidos',
    'Comunidad exclusiva de estudiantes',
];
</script>

<template>
    <Head title="Transforma tu Futuro - EduPlatform" />

    <div
        class="min-h-screen bg-white font-sans text-gray-900 selection:bg-indigo-500 selection:text-white dark:bg-gray-950 dark:text-white"
    >
        <!-- Navbar -->
        <PublicNavbar :cart-count="cartCount" :show-register="canRegister" />

        <!-- Hero Section Ultra Moderno -->
        <header class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-950 dark:via-gray-900 dark:to-indigo-950">
            <!-- Grid Pattern Background -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent dark:from-gray-950"></div>
            </div>

            <!-- Floating Orbs -->
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div
                    class="animate-blob absolute -top-20 -left-20 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-purple-400/30 to-pink-400/30 blur-3xl filter dark:from-purple-600/20 dark:to-pink-600/20"
                ></div>
                <div
                    class="animate-blob animation-delay-2000 absolute top-1/4 -right-20 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-indigo-400/30 to-blue-400/30 blur-3xl filter dark:from-indigo-600/20 dark:to-blue-600/20"
                ></div>
                <div
                    class="animate-blob animation-delay-4000 absolute -bottom-20 left-1/3 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-cyan-400/30 to-teal-400/30 blur-3xl filter dark:from-cyan-600/20 dark:to-teal-600/20"
                ></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-6 pt-28 pb-20 lg:pt-36 lg:pb-28">
                <div class="mx-auto max-w-5xl text-center">
                    <!-- Floating Badge -->
                    <div
                        class="mb-8 inline-flex animate-bounce items-center gap-2 rounded-full border border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-3 shadow-lg backdrop-blur-sm dark:border-indigo-800/50 dark:from-indigo-950/50 dark:to-purple-950/50"
                    >
                        <Sparkles
                            :size="18"
                            class="animate-pulse text-indigo-600 dark:text-indigo-400"
                        />
                        <span
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-sm font-bold text-transparent dark:from-indigo-400 dark:to-purple-400"
                        >
                            🎓 +50,000 estudiantes ya están aprendiendo
                        </span>
                    </div>

                    <!-- Main Headline -->
                    <h1
                        ref="heroTitle"
                        class="mb-8 text-6xl leading-tight font-black tracking-tight md:text-7xl lg:text-8xl"
                    >
                        <span class="block">
                            <span
                                class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400"
                            >
                                Domina Nuevas
                            </span>
                        </span>
                        <span class="block">
                            <span class="relative inline-block">
                                <span class="relative z-10 text-gray-900 dark:text-white">Habilidades</span>
                                <span class="absolute inset-x-0 bottom-3 h-4 bg-gradient-to-r from-yellow-400 to-orange-400 opacity-30 dark:opacity-20"></span>
                            </span>
                        </span>
                        <span class="block text-gray-900 dark:text-white">Hoy Mismo</span>
                    </h1>

                    <!-- Subtitle -->
                    <p
                        ref="heroSubtitle"
                        class="mx-auto mb-12 max-w-3xl text-xl leading-relaxed text-gray-600 md:text-2xl lg:text-3xl dark:text-gray-300"
                    >
                        Accede a <span class="font-bold text-indigo-600 dark:text-indigo-400">+800 cursos</span> premium de tecnología, diseño y negocios.
                        <span class="block mt-2">Aprende de expertos de la industria 🚀</span>
                    </p>

                    <!-- CTA Buttons -->
                    <div
                        ref="heroButtons"
                        class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center"
                    >
                        <Link
                            :href="canRegister ? register() : login()"
                            class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-10 py-5 text-lg font-bold text-white shadow-2xl shadow-indigo-600/40 transition-all hover:scale-105 hover:shadow-indigo-600/50 dark:shadow-indigo-600/30"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-700 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <Rocket
                                :size="24"
                                class="relative z-10 transition-transform group-hover:rotate-12"
                            />
                            <span class="relative z-10">Comenzar Gratis</span>
                            <ArrowRight
                                :size="20"
                                class="relative z-10 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                        <a
                            href="#courses"
                            class="group inline-flex items-center gap-3 rounded-2xl border-2 border-gray-200 bg-white/80 px-10 py-5 text-lg font-bold text-gray-900 backdrop-blur-sm transition-all hover:border-indigo-600 hover:bg-white dark:border-gray-700 dark:bg-gray-800/80 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-gray-800"
                        >
                            <Play :size="20" />
                            Ver Catálogo
                            <span class="transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    </div>

                    <!-- Stats Cards Modernos -->
                    <div class="stats-section mt-20 grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="stat-item group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-xl backdrop-blur-sm transition-all hover:scale-105 hover:border-indigo-500 hover:shadow-2xl dark:border-gray-800 dark:bg-gray-900/80"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <div class="relative">
                                <div class="mb-3 inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 p-2.5 shadow-lg">
                                    <component :is="stat.icon" :size="20" class="text-white" />
                                </div>
                                <div class="mb-1 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-3xl font-black text-transparent lg:text-4xl">
                                    {{ stat.value }}
                                </div>
                                <div class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    {{ stat.label }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>


        <!-- Categories Section Moderna -->
        <section class="categories-section relative overflow-hidden bg-gradient-to-b from-white via-purple-50/30 to-indigo-50/40 py-24 dark:from-gray-950 dark:via-purple-950/20 dark:to-indigo-950/30">
            <!-- Decorative Background -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 right-1/4 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-purple-400/20 to-pink-400/20 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/4 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-indigo-400/20 to-blue-400/20 blur-3xl"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 px-5 py-2 dark:from-purple-950 dark:to-pink-950">
                        <Sparkles :size="18" class="text-purple-600 dark:text-purple-400" />
                        <span class="text-sm font-bold text-purple-600 uppercase tracking-wide dark:text-purple-400">
                            Descubre tu Pasión
                        </span>
                    </div>
                    <h2 class="mb-4 text-4xl font-black md:text-5xl lg:text-6xl">
                        <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent dark:from-purple-400 dark:to-pink-400">
                            Explora por
                        </span>
                        <span class="block">Categoría</span>
                    </h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600 dark:text-gray-400">
                        Encuentra el camino perfecto para tu carrera profesional
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-6">
                    <div
                        v-for="category in categories"
                        :key="category.name"
                        class="category-item group cursor-pointer rounded-3xl border-2 border-white/50 bg-white/80 p-6 text-center backdrop-blur-sm transition-all hover:-translate-y-3 hover:border-white hover:bg-white hover:shadow-2xl hover:shadow-purple-500/20 dark:border-gray-700/50 dark:bg-gray-800/80 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                    >
                        <div class="mb-4 flex justify-center">
                            <div :class="[category.color, 'inline-flex items-center justify-center rounded-2xl p-4 shadow-xl transition-all duration-500 group-hover:scale-125 group-hover:rotate-12']">
                                <component :is="category.icon" :size="32" class="text-white" />
                            </div>
                        </div>
                        <h3 class="mb-2 text-sm font-bold text-gray-900 transition-colors group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400">
                            {{ category.name }}
                        </h3>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400">
                            {{ category.count }} cursos
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section Ultra Moderna -->
        <section class="features-section relative overflow-hidden bg-gradient-to-b from-indigo-50/40 via-pink-50/30 to-orange-50/40 py-24 dark:from-indigo-950/30 dark:via-pink-950/20 dark:to-orange-950/30">
            <!-- Background Pattern -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] bg-[size:20px_20px] opacity-40 dark:bg-[radial-gradient(#374151_1px,transparent_1px)]"></div>
                <div class="absolute top-1/3 left-0 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-indigo-400/20 to-purple-400/20 blur-3xl"></div>
                <div class="absolute bottom-1/3 right-0 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-pink-400/20 to-orange-400/20 blur-3xl"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-100 to-purple-100 px-6 py-3 shadow-lg dark:from-indigo-950 dark:to-purple-950">
                        <Zap :size="20" class="animate-pulse text-indigo-600 dark:text-indigo-400" />
                        <span class="text-sm font-bold text-indigo-600 uppercase tracking-wide dark:text-indigo-400">
                            ¿Por qué elegirnos?
                        </span>
                    </div>
                    <h2 class="mb-6 text-4xl font-black md:text-5xl lg:text-6xl">
                        <span class="block">Experiencia de</span>
                        <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400">
                            Aprendizaje Superior
                        </span>
                    </h2>
                    <p class="mx-auto max-w-3xl text-xl text-gray-600 dark:text-gray-400">
                        Herramientas y recursos diseñados para maximizar tu éxito
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(feature, index) in features"
                        :key="feature.title"
                        class="feature-card group relative overflow-hidden rounded-3xl border-2 border-white/60 bg-white/90 p-8 shadow-xl backdrop-blur-sm transition-all duration-500 hover:-translate-y-3 hover:border-white hover:bg-white hover:shadow-2xl dark:border-gray-700/50 dark:bg-gray-800/90 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                    >
                        <!-- Gradient Overlay on Hover -->
                        <div :class="[
                            'absolute inset-0 bg-gradient-to-br opacity-0 transition-opacity duration-500 group-hover:opacity-5',
                            feature.color
                        ]"></div>

                        <div class="relative z-10">
                            <!-- Icon -->
                            <div class="mb-6 inline-flex items-center justify-center rounded-2xl bg-gradient-to-br p-4 shadow-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3"
                                :class="feature.color"
                            >
                                <component :is="feature.icon" :size="32" class="text-white" />
                            </div>

                            <!-- Content -->
                            <h3 class="mb-3 text-xl font-bold transition-colors group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                {{ feature.title }}
                            </h3>
                            <p class="leading-relaxed text-gray-600 dark:text-gray-400">
                                {{ feature.description }}
                            </p>
                        </div>

                        <!-- Decorative Element -->
                        <div class="absolute -bottom-2 -right-2 h-24 w-24 rounded-full opacity-0 blur-2xl transition-opacity duration-500 group-hover:opacity-20"
                            :class="[feature.color.replace('from-', 'bg-').split(' ')[0]]"
                        ></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Courses Section -->
        <section
            id="courses"
            class="relative overflow-hidden bg-gradient-to-b from-orange-50/40 via-pink-50/40 to-purple-50/50 py-28 dark:from-orange-950/30 dark:via-pink-950/30 dark:to-purple-950/40"
        >
            <!-- Decorative Elements -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 left-0 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-orange-400/20 to-pink-400/20 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-purple-400/20 to-indigo-400/20 blur-3xl"></div>
            </div>
            <div class="relative z-10 mx-auto max-w-7xl px-6">
                <!-- Header -->
                <div class="mb-16 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                    <div class="flex-1">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-orange-100 to-pink-100 px-6 py-3 shadow-lg dark:from-orange-950 dark:to-pink-950">
                            <TrendingUp :size="20" class="animate-pulse text-orange-600 dark:text-orange-400" />
                            <span class="text-sm font-bold text-orange-600 uppercase tracking-wide dark:text-orange-400">
                                🔥 Trending Now
                            </span>
                        </div>
                        <h2 class="mb-6 text-4xl font-black md:text-5xl lg:text-6xl">
                            <span class="block">Cursos</span>
                            <span class="bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 bg-clip-text text-transparent dark:from-orange-400 dark:via-pink-400 dark:to-purple-400">
                                Más Populares
                            </span>
                        </h2>
                        <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                            Seleccionados por nuestra comunidad de +50,000 estudiantes activos
                        </p>
                    </div>
                    <Link
                        href="/courses"
                        class="group inline-flex items-center gap-3 rounded-2xl border-2 border-transparent bg-gradient-to-r from-orange-600 via-pink-600 to-purple-600 px-10 py-5 text-lg font-bold text-white shadow-xl transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        Ver Todo el Catálogo
                        <ArrowRight
                            :size="22"
                            class="transition-transform group-hover:translate-x-1"
                        />
                    </Link>
                </div>

                <!-- Courses Grid -->
                <div v-if="hasCourses" class="course-grid grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Link
                        :href="`/courses/${course.id}`"
                        v-for="course in featuredCourses"
                        :key="course.id"
                        class="course-card group relative overflow-hidden rounded-3xl border-2 border-gray-200 bg-white transition-all duration-500 hover:-translate-y-3 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/20 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <!-- Image Container -->
                        <div class="relative h-64 overflow-hidden bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-950 dark:via-purple-950 dark:to-pink-950">
                            <img
                                v-if="course.image_path"
                                :src="`/storage/${course.image_path}`"
                                :alt="course.title"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110 group-hover:rotate-1"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <BookOpen :size="80" class="text-indigo-300 dark:text-indigo-700" />
                            </div>

                            <!-- Price Badge Mejorado -->
                            <div class="absolute top-4 right-4 rounded-2xl border-2 border-white bg-white/95 px-5 py-2.5 shadow-2xl backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900/95">
                                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-2xl font-black text-transparent">
                                    ${{ course.price }}
                                </span>
                            </div>

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>

                            <!-- Play Button on Hover -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-500 group-hover:opacity-100">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-2xl">
                                    <Play :size="28" class="ml-1 text-indigo-600" />
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-7">
                            <!-- Teacher Info -->
                            <div class="mb-5 flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 font-bold text-white shadow-lg ring-4 ring-indigo-100 dark:ring-indigo-950">
                                    {{ course.teacher.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ course.teacher.name }}
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Expert Instructor
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="mb-3 line-clamp-2 min-h-[3.5rem] text-2xl leading-tight font-bold text-gray-900 transition-colors group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                                {{ course.title }}
                            </h3>

                            <!-- Subtitle -->
                            <p class="mb-5 line-clamp-2 min-h-[3rem] text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                                {{ course.subtitle }}
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between border-t-2 border-gray-100 pt-5 dark:border-gray-800">
                                <!-- Rating -->
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1">
                                        <Star :size="18" class="fill-yellow-400 text-yellow-400" />
                                        <span class="text-base font-bold text-gray-900 dark:text-white">4.9</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">(1.2K)</span>
                                </div>

                                <!-- CTA -->
                                <div class="flex items-center gap-2 font-bold text-indigo-600 transition-all group-hover:gap-3 dark:text-indigo-400">
                                    <span class="text-sm">Ver Curso</span>
                                    <ArrowRight :size="18" class="transition-transform group-hover:translate-x-1" />
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State Mejorado -->
                <div
                    v-else
                    class="rounded-3xl border-2 border-dashed border-gray-300 bg-gradient-to-br from-gray-50 via-white to-gray-50 py-32 text-center dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
                >
                    <div class="mx-auto max-w-md">
                        <div class="mb-6 inline-flex items-center justify-center rounded-full bg-gray-100 p-6 dark:bg-gray-800">
                            <BookOpen :size="64" class="text-gray-400" />
                        </div>
                        <h3 class="mb-3 text-3xl font-bold text-gray-700 dark:text-gray-300">
                            Próximamente Cursos Increíbles
                        </h3>
                        <p class="mb-6 text-lg text-gray-500 dark:text-gray-400">
                            Estamos preparando contenido de primer nivel para ti
                        </p>
                        <div class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-6 py-3 text-sm font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                            <Sparkles :size="16" />
                            ¡Mantente atento!
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Benefits Section Nueva -->
        <section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <!-- Left Side -->
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-5 py-2 backdrop-blur-sm">
                            <Trophy :size="18" class="text-white" />
                            <span class="text-sm font-bold text-white">
                                Beneficios Premium
                            </span>
                        </div>
                        <h2 class="mb-6 text-4xl font-black text-white md:text-5xl lg:text-6xl">
                            Todo lo que Necesitas para
                            <span class="block">Triunfar</span>
                        </h2>
                        <p class="mb-8 text-xl leading-relaxed text-white/90">
                            Únete hoy y obtén acceso completo a nuestra plataforma de aprendizaje premium
                        </p>
                        <Link
                            :href="canRegister ? register() : login()"
                            class="inline-flex items-center gap-3 rounded-2xl bg-white px-10 py-5 text-lg font-bold text-indigo-600 shadow-2xl transition-all hover:scale-105 hover:bg-gray-50"
                        >
                            <Rocket :size="22" />
                            Empezar Ahora Gratis
                            <ArrowRight :size="20" />
                        </Link>
                    </div>

                    <!-- Right Side - Benefits List -->
                    <div class="space-y-4">
                        <div
                            v-for="(benefit, index) in benefits"
                            :key="index"
                            class="flex items-start gap-4 rounded-2xl bg-white/10 p-5 backdrop-blur-sm transition-all hover:bg-white/20"
                        >
                            <div class="flex-shrink-0">
                                <CheckCircle2 :size="24" class="text-green-300" />
                            </div>
                            <span class="text-lg font-semibold text-white">
                                {{ benefit }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section Moderna -->
        <section class="relative overflow-hidden bg-gray-950 py-32">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

            <!-- Gradient Orbs -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-indigo-600/30 to-purple-600/30 blur-3xl"></div>
                <div class="absolute -bottom-40 -right-40 h-[600px] w-[600px] rounded-full bg-gradient-to-br from-pink-600/30 to-orange-600/30 blur-3xl"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-5xl px-6 text-center">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-6 py-3 backdrop-blur-sm">
                    <Sparkles :size="20" class="text-yellow-400" />
                    <span class="text-sm font-bold text-white">
                        Oferta Especial de Lanzamiento
                    </span>
                </div>

                <h2 class="mb-6 text-5xl leading-tight font-black text-white md:text-6xl lg:text-7xl">
                    ¿Listo para Cambiar
                    <span class="block">
                        <span class="bg-gradient-to-r from-yellow-400 via-orange-400 to-pink-400 bg-clip-text text-transparent">
                            Tu Futuro?
                        </span>
                    </span>
                </h2>

                <p class="mx-auto mb-12 max-w-2xl text-xl leading-relaxed text-gray-300">
                    Miles de profesionales ya han transformado sus carreras con nosotros.
                    <span class="block mt-2 font-bold text-white">¡Es tu turno!</span>
                </p>

                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link
                        :href="canRegister ? register() : login()"
                        class="group inline-flex items-center gap-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 px-12 py-6 text-xl font-bold text-white shadow-2xl transition-all hover:scale-105 hover:from-indigo-700 hover:to-purple-700"
                    >
                        <GraduationCap :size="26" />
                        Crear Cuenta Gratis
                        <ArrowRight :size="22" class="transition-transform group-hover:translate-x-1" />
                    </Link>
                    <a
                        href="#courses"
                        class="inline-flex items-center gap-3 rounded-2xl border-2 border-white bg-transparent px-12 py-6 text-xl font-bold text-white transition-all hover:bg-white hover:text-gray-900"
                    >
                        <Play :size="22" />
                        Ver Demo
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-16 flex flex-wrap items-center justify-center gap-8 opacity-60">
                    <div class="text-center">
                        <div class="mb-1 text-3xl font-bold text-white">98%</div>
                        <div class="text-sm text-gray-400">Satisfacción</div>
                    </div>
                    <div class="h-12 w-px bg-gray-700"></div>
                    <div class="text-center">
                        <div class="mb-1 text-3xl font-bold text-white">50K+</div>
                        <div class="text-sm text-gray-400">Graduados</div>
                    </div>
                    <div class="h-12 w-px bg-gray-700"></div>
                    <div class="text-center">
                        <div class="mb-1 text-3xl font-bold text-white">4.9★</div>
                        <div class="text-sm text-gray-400">Calificación</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Ultra Moderno -->
        <footer class="border-t border-gray-800 bg-gray-950 py-20 text-gray-400">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-5">
                    <!-- Brand Column -->
                    <div class="lg:col-span-2">
                        <div class="mb-6 flex items-center gap-3 text-2xl font-bold">
                            <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 p-3 shadow-xl">
                                <GraduationCap :size="28" class="text-white" />
                            </div>
                            <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                                EduPlatform
                            </span>
                        </div>
                        <p class="mb-6 max-w-sm text-base leading-relaxed text-gray-500">
                            La plataforma líder de educación online donde transformas tu carrera con cursos de nivel mundial.
                        </p>
                        <div class="flex gap-3">
                            <a href="#" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gray-800 bg-gray-900 transition-all hover:border-indigo-500 hover:bg-gray-800">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gray-800 bg-gray-900 transition-all hover:border-indigo-500 hover:bg-gray-800">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="flex h-12 w-12 items-center justify-center rounded-xl border border-gray-800 bg-gray-900 transition-all hover:border-indigo-500 hover:bg-gray-800">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Links Columns -->
                    <div>
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-white">Plataforma</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Explorar Cursos</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Categorías</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Instructores</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Precios</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-white">Empresa</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Sobre Nosotros</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Carreras</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Blog</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Contacto</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-white">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Privacidad</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Términos</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Cookies</a></li>
                            <li><a href="#" class="text-sm transition hover:text-indigo-400">Licencias</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-10">
                    <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                        <p class="text-sm text-gray-500">
                            &copy; {{ new Date().getFullYear() }} <span class="font-bold text-gray-400">EduPlatform</span>. Todos los derechos reservados.
                        </p>
                        <div class="flex items-center gap-6 text-sm">
                            <a href="#" class="text-gray-500 transition hover:text-indigo-400">Centro de Ayuda</a>
                            <a href="#" class="text-gray-500 transition hover:text-indigo-400">Status</a>
                            <a href="#" class="text-gray-500 transition hover:text-indigo-400">Soporte</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
<style scoped>
@keyframes blob {
    0%,
    100% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(40px, -60px) scale(1.15);
    }
    66% {
        transform: translate(-30px, 30px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 10s infinite cubic-bezier(0.4, 0, 0.2, 1);
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Smooth gradient animations */
@keyframes gradient {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 8s ease infinite;
}

/* Improved hover effects */
.course-card:hover {
    transform: translateY(-12px) scale(1.02);
}

.feature-card:hover {
    transform: translateY(-8px);
}

/* Smooth transitions */
* {
    transition-property: transform, opacity, background-color, border-color, box-shadow;
}
</style>

