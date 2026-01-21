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
    Globe,
    Play,
    Shield,
    Sparkles,
    Star,
    TrendingUp,
    Users,
    Zap,
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

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

onMounted(async () => {
    // Cargar contador del carrito
    try {
        const response = await axios.get('/cart/count');
        cartCount.value = response.data.count;
    } catch (error) {
        console.error('Error loading cart count:', error);
    }

    const tl = gsap.timeline();

    tl.from(heroTitle.value, {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
    })
        .from(
            heroSubtitle.value,
            {
                y: 30,
                opacity: 0,
                duration: 0.8,
                ease: 'power3.out',
            },
            '-=0.5',
        )
        .from(
            heroButtons.value,
            {
                y: 20,
                opacity: 0,
                duration: 0.8,
                ease: 'power3.out',
            },
            '-=0.5',
        );

    // Animación de las tarjetas de cursos
    gsap.from('.course-card', {
        y: 50,
        opacity: 0,
        duration: 0.8,
        stagger: 0.15,
        scrollTrigger: {
            trigger: '.course-grid',
            start: 'top 80%',
        },
        ease: 'power2.out',
    });

    // Animación de features
    gsap.from('.feature-card', {
        y: 30,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.features-section',
            start: 'top 70%',
        },
    });

    // Animación de stats
    gsap.from('.stat-item', {
        scale: 0.8,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        scrollTrigger: {
            trigger: '.stats-section',
            start: 'top 75%',
        },
    });
});

const features = [
    {
        icon: BookOpen,
        title: 'Contenido de Calidad',
        description:
            'Cursos creados por expertos de la industria con contenido actualizado constantemente.',
    },
    {
        icon: Users,
        title: 'Comunidad Activa',
        description:
            'Únete a miles de estudiantes que aprenden y crecen juntos cada día.',
    },
    {
        icon: Award,
        title: 'Certificados Verificables',
        description:
            'Obtén certificados reconocidos al completar tus cursos exitosamente.',
    },
    {
        icon: Zap,
        title: 'Aprende a tu Ritmo',
        description:
            'Accede a todos los cursos 24/7 desde cualquier dispositivo.',
    },
    {
        icon: Shield,
        title: 'Garantía de Satisfacción',
        description:
            'Estamos comprometidos con tu aprendizaje y satisfacción total.',
    },
    {
        icon: Globe,
        title: 'Acceso Global',
        description:
            'Aprende desde cualquier parte del mundo con nuestros cursos en línea.',
    },
];

const stats = [
    { value: '10,000+', label: 'Estudiantes' },
    { value: '500+', label: 'Cursos' },
    { value: '50+', label: 'Instructores' },
    { value: '4.9/5', label: 'Calificación' },
];
</script>

<template>
    <Head title="Aprende Sin Límites - EduPlatform" />

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-white font-sans text-gray-900 selection:bg-indigo-500 selection:text-white dark:from-gray-900 dark:to-gray-950 dark:text-white"
    >
        <!-- Navbar usando el nuevo componente -->
        <PublicNavbar :cart-count="cartCount" :show-register="canRegister" />

        <!-- Hero Section Mejorado -->
        <header class="relative overflow-hidden pt-32 pb-24 lg:pt-48 lg:pb-40">
            <!-- Animated Background -->
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div
                    class="animate-blob absolute top-1/4 -left-1/4 h-96 w-96 rounded-full bg-purple-300 opacity-20 mix-blend-multiply blur-3xl filter dark:bg-purple-900/30 dark:mix-blend-normal"
                ></div>
                <div
                    class="animate-blob animation-delay-2000 absolute top-1/3 -right-1/4 h-96 w-96 rounded-full bg-indigo-300 opacity-20 mix-blend-multiply blur-3xl filter dark:bg-indigo-900/30 dark:mix-blend-normal"
                ></div>
                <div
                    class="animate-blob animation-delay-4000 absolute -bottom-8 left-1/2 h-96 w-96 rounded-full bg-pink-300 opacity-20 mix-blend-multiply blur-3xl filter dark:bg-pink-900/30 dark:mix-blend-normal"
                ></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-6">
                <div class="mx-auto max-w-4xl text-center">
                    <!-- Badge -->
                    <div
                        class="mb-8 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-2 dark:border-indigo-800 dark:bg-indigo-950/50"
                    >
                        <Sparkles
                            :size="16"
                            class="text-indigo-600 dark:text-indigo-400"
                        />
                        <span
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400"
                        >
                            ¡Únete a más de 10,000 estudiantes!
                        </span>
                    </div>

                    <h1
                        ref="heroTitle"
                        class="mb-6 text-5xl leading-tight font-extrabold tracking-tight md:text-7xl lg:text-8xl"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent"
                        >
                            Aprende Habilidades
                        </span>
                        <br />
                        <span class="text-gray-900 dark:text-white">
                            que Transforman Vidas
                        </span>
                    </h1>

                    <p
                        ref="heroSubtitle"
                        class="mx-auto mb-10 max-w-3xl text-xl leading-relaxed text-gray-600 md:text-2xl dark:text-gray-300"
                    >
                        Descubre cursos de programación, diseño, negocios y más.
                        Aprende de los mejores instructores y alcanza tus metas
                        profesionales.
                    </p>

                    <div
                        ref="heroButtons"
                        class="flex flex-wrap justify-center gap-4"
                    >
                        <Link
                            :href="canRegister ? register() : login()"
                            class="group flex transform items-center gap-2 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-4 text-lg font-bold text-white shadow-2xl shadow-indigo-600/30 transition-all hover:scale-105 hover:from-indigo-700 hover:to-purple-700"
                        >
                            <Play
                                :size="20"
                                class="transition-transform group-hover:scale-110"
                            />
                            Comenzar Ahora
                        </Link>
                        <a
                            href="#courses"
                            class="flex items-center gap-2 rounded-full border-2 border-gray-200 bg-white px-8 py-4 text-lg font-bold text-gray-900 transition-all hover:border-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:hover:border-indigo-400"
                        >
                            Explorar Cursos
                            <ArrowRight :size="20" />
                        </a>
                    </div>

                    <!-- Stats Pills -->
                    <div
                        class="stats-section mt-16 flex flex-wrap justify-center gap-6"
                    >
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="stat-item rounded-2xl border border-gray-200 bg-white px-6 py-4 shadow-lg backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/50"
                        >
                            <div
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-3xl font-bold text-transparent"
                            >
                                {{ stat.value }}
                            </div>
                            <div
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                {{ stat.label }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Features Section -->
        <section class="features-section bg-white py-24 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold md:text-5xl">
                        ¿Por qué elegirnos?
                    </h2>
                    <p
                        class="mx-auto max-w-2xl text-xl text-gray-600 dark:text-gray-400"
                    >
                        Ofrecemos la mejor experiencia de aprendizaje en línea
                        con herramientas y recursos de primera clase.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="feature-card group rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-gray-100 p-8 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/10 dark:border-gray-700 dark:from-gray-900 dark:to-gray-800 dark:hover:border-indigo-500"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg transition-transform group-hover:scale-110"
                        >
                            <component
                                :is="feature.icon"
                                :size="28"
                                class="text-white"
                            />
                        </div>
                        <h3
                            class="mb-3 text-xl font-bold transition-colors group-hover:text-indigo-600 dark:group-hover:text-indigo-400"
                        >
                            {{ feature.title }}
                        </h3>
                        <p
                            class="leading-relaxed text-gray-600 dark:text-gray-400"
                        >
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Courses Section Mejorada -->
        <section
            id="courses"
            class="bg-gradient-to-b from-gray-50 to-white py-24 dark:from-gray-900 dark:to-gray-950"
        >
            <div class="mx-auto max-w-7xl px-6">
                <div
                    class="mb-12 flex flex-col items-start justify-between gap-4 md:flex-row md:items-end"
                >
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <TrendingUp
                                :size="24"
                                class="text-indigo-600 dark:text-indigo-400"
                            />
                            <span
                                class="text-sm font-semibold tracking-wider text-indigo-600 uppercase dark:text-indigo-400"
                            >
                                Populares Esta Semana
                            </span>
                        </div>
                        <h2 class="mb-3 text-4xl font-bold md:text-5xl">
                            Cursos Destacados
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            Los favoritos de nuestra comunidad seleccionados
                            especialmente para ti.
                        </p>
                    </div>
                    <Link
                        href="/courses"
                        class="group inline-flex items-center gap-2 font-bold text-indigo-600 transition-all hover:gap-3 dark:text-indigo-400"
                    >
                        Ver todos los cursos
                        <ArrowRight
                            :size="20"
                            class="transition-transform group-hover:translate-x-1"
                        />
                    </Link>
                </div>

                <div
                    v-if="featuredCourses && featuredCourses.length > 0"
                    class="course-grid grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        :href="`/courses/${course.id}`"
                        v-for="course in featuredCourses"
                        :key="course.id"
                        class="course-card group transform overflow-hidden rounded-3xl border border-gray-200 bg-white transition-all duration-300 hover:-translate-y-2 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/20 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500"
                    >
                        <!-- Image -->
                        <div
                            class="relative h-56 overflow-hidden bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-950 dark:to-purple-950"
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
                                <BookOpen
                                    :size="64"
                                    class="text-indigo-300 dark:text-indigo-700"
                                />
                            </div>

                            <!-- Price Badge -->
                            <div
                                class="absolute top-4 right-4 rounded-full border border-gray-200 bg-white px-4 py-2 shadow-lg backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900"
                            >
                                <span
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-lg font-bold text-transparent"
                                >
                                    ${{ course.price }}
                                </span>
                            </div>

                            <!-- Overlay Gradient -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                            ></div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Teacher -->
                            <div class="mb-4 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 font-bold text-white shadow-lg"
                                >
                                    {{
                                        course.teacher.name
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </div>
                                <div>
                                    <div
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ course.teacher.name }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Instructor
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3
                                class="mb-2 line-clamp-2 text-xl leading-tight font-bold transition-colors group-hover:text-indigo-600 dark:group-hover:text-indigo-400"
                            >
                                {{ course.title }}
                            </h3>

                            <!-- Subtitle -->
                            <p
                                class="mb-4 line-clamp-2 text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ course.subtitle }}
                            </p>

                            <!-- Footer -->
                            <div
                                class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-800"
                            >
                                <!-- Rating -->
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <Star
                                            :size="16"
                                            class="fill-yellow-400 text-yellow-400"
                                        />
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white"
                                            >4.8</span
                                        >
                                    </div>
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                        >(120)</span
                                    >
                                </div>

                                <!-- CTA -->
                                <div
                                    class="flex items-center gap-2 text-sm font-bold text-indigo-600 transition-all group-hover:gap-3 dark:text-indigo-400"
                                >
                                    Ver más
                                    <ArrowRight :size="16" />
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="rounded-3xl border-2 border-dashed border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100 py-24 text-center dark:border-gray-700 dark:from-gray-900 dark:to-gray-800"
                >
                    <BookOpen :size="64" class="mx-auto mb-4 text-gray-400" />
                    <p
                        class="mb-2 text-2xl font-bold text-gray-600 dark:text-gray-400"
                    >
                        Aún no hay cursos publicados
                    </p>
                    <p class="text-gray-500 dark:text-gray-500">
                        ¡Sé el primero en crear un curso y compartir tu
                        conocimiento!
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section
            class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-24"
        >
            <div class="bg-grid-white/10 absolute inset-0"></div>
            <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
                <h2 class="mb-6 text-4xl font-bold text-white md:text-5xl">
                    ¿Listo para comenzar tu viaje de aprendizaje?
                </h2>
                <p class="mb-10 text-xl leading-relaxed text-white/90">
                    Únete a miles de estudiantes que ya están transformando sus
                    carreras con nosotros.
                </p>
                <Link
                    :href="canRegister ? register() : login()"
                    class="inline-flex transform items-center gap-2 rounded-full bg-white px-10 py-5 text-lg font-bold text-indigo-600 shadow-2xl transition-all hover:scale-105 hover:bg-gray-100"
                >
                    <Sparkles :size="20" />
                    Crear Cuenta Gratis
                    <ArrowRight :size="20" />
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-gray-800 bg-gray-950 py-16 text-gray-400"
        >
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-12 grid grid-cols-1 gap-12 md:grid-cols-4">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-2">
                        <div
                            class="mb-4 flex items-center gap-2 text-2xl font-bold"
                        >
                            <div
                                class="rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 p-2 text-white"
                            >
                                <BookOpen :size="24" />
                            </div>
                            <span class="text-white">EduPlatform</span>
                        </div>
                        <p class="max-w-md leading-relaxed text-gray-500">
                            Tu plataforma de aprendizaje en línea donde puedes
                            adquirir nuevas habilidades y transformar tu carrera
                            profesional.
                        </p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h3 class="mb-4 font-bold text-white">Plataforma</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Explorar Cursos</a
                                >
                            </li>
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Conviértete en Instructor</a
                                >
                            </li>
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Precios</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-4 font-bold text-white">Soporte</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Centro de Ayuda</a
                                >
                            </li>
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Términos de Servicio</a
                                >
                            </li>
                            <li>
                                <a href="#" class="transition hover:text-white"
                                    >Privacidad</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 text-center">
                    <p class="text-sm">
                        &copy; {{ new Date().getFullYear() }} EduPlatform. Todos
                        los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.bg-grid-white\/10 {
    background-image:
        linear-gradient(
            to right,
            rgba(255, 255, 255, 0.1) 1px,
            transparent 1px
        ),
        linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.1) 1px,
            transparent 1px
        );
    background-size: 40px 40px;
}
</style>
