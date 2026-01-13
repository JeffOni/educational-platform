<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { login, register, dashboard } from '@/routes';

gsap.registerPlugin(ScrollTrigger);

defineProps<{
    canRegister: boolean;
    featuredCourses: Array<any>;
}>();

const heroTitle = ref(null);
const heroSubtitle = ref(null);
const heroButtons = ref(null);
const courseCards = ref([]);

onMounted(() => {
    const tl = gsap.timeline();

    tl.from(heroTitle.value, {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: "power3.out"
    })
    .from(heroSubtitle.value, {
        y: 30,
        opacity: 0,
        duration: 0.8,
        ease: "power3.out"
    }, "-=0.5")
    .from(heroButtons.value, {
        y: 20,
        opacity: 0,
        duration: 0.8,
        ease: "power3.out"
    }, "-=0.5");

    // Animación de las tarjetas de cursos (Stagger)
    gsap.from(".course-card", {
        y: 50,
        opacity: 0,
        duration: 0.8,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".course-grid",
            start: "top 80%"
        },
        ease: "power2.out",
        delay: 0.5 // Pequeño delay inicial
    });
});
</script>

<template>
    <Head title="Bienvenido a tu Futuro" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white font-sans selection:bg-indigo-500 selection:text-white">
        
        <!-- Navbar Transparente -->
        <nav class="absolute top-0 w-full z-50 px-6 py-4 flex justify-between items-center max-w-7xl mx-auto left-0 right-0">
            <div class="text-2xl font-bold tracking-tighter flex items-center gap-2">
                <span class="bg-indigo-600 text-white px-2 py-1 rounded-lg">E</span> EduPlatform
            </div>
            <div class="flex gap-4">
                <Link v-if="$page.props.auth.user" :href="dashboard()" class="font-semibold hover:text-indigo-500 transition">Dashboard</Link>
                <template v-else>
                    <Link :href="login()" class="font-semibold hover:text-indigo-500 transition">Iniciar Sesión</Link>
                    <Link v-if="canRegister" :href="register()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full font-medium transition shadow-lg hover:shadow-indigo-500/30">Registrarse</Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
                <h1 ref="heroTitle" class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-500 dark:from-indigo-400 dark:to-purple-400">
                    Aprende sin límites.<br>Crea tu futuro.
                </h1>
                <p ref="heroSubtitle" class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Únete a la comunidad de aprendizaje más grande. Cursos de programación, diseño, marketing y más, impartidos por expertos.
                </p>
                <div ref="heroButtons" class="flex justify-center gap-4">
                    <Link :href="register()" class="bg-indigo-600 text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-indigo-700 transition transform hover:scale-105 shadow-xl shadow-indigo-600/20">
                        Comenzar Ahora
                    </Link>
                    <a href="#courses" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 px-8 py-4 rounded-full text-lg font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Explorar Cursos
                    </a>
                </div>
            </div>

            <!-- Background Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
                <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-purple-200/30 dark:bg-purple-900/20 blur-3xl"></div>
                <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-indigo-200/30 dark:bg-indigo-900/20 blur-3xl"></div>
            </div>
        </header>

        <!-- Featured Courses Section -->
        <section id="courses" class="py-20 bg-white dark:bg-gray-950">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">Cursos Destacados</h2>
                        <p class="text-gray-500 dark:text-gray-400">Los favoritos de la comunidad esta semana.</p>
                    </div>
                    <Link href="#" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Ver todos los cursos &rarr;</Link>
                </div>

                <div v-if="featuredCourses && featuredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 course-grid">
                    <Link :href="'/courses/' + course.id" v-for="course in featuredCourses" :key="course.id" class="course-card group bg-gray-50 dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative h-48 bg-gray-200 dark:bg-gray-800 overflow-hidden">
                            <img v-if="course.image_path" :src="'/storage/' + course.image_path" :alt="course.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📚</span>
                            </div>
                            <div class="absolute top-4 right-4 bg-white/90 dark:bg-black/80 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                                ${{ course.price }}
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                    {{ course.teacher.name.charAt(0) }}
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ course.teacher.name }}</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ course.title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4">
                                {{ course.description }}
                            </p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-800">
                                <div class="flex items-center gap-1 text-yellow-400 text-sm">
                                    <span>★</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">4.8</span>
                                    <span class="text-gray-400">(120)</span>
                                </div>
                                <button class="text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:underline">
                                    Ver detalles
                                </button>
                            </div>
                        </div>
                    </Link>
                </div>
                
                <div v-else class="text-center py-20 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-xl text-gray-500">Aún no hay cursos publicados.</p>
                    <p class="text-sm text-gray-400 mt-2">¡Sé el primero en enseñar!</p>
                </div>

            </div>
        </section>

        <!-- Footer Simple -->
        <footer class="bg-white dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-6 text-center text-gray-500 text-sm">
                &copy; {{ new Date().getFullYear() }} EduPlatform. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>