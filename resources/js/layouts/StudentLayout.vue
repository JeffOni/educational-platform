<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Award,
    Bell,
    BookOpen,
    GraduationCap,
    LayoutDashboard,
    LogOut,
    Menu,
    Search,
    Settings,
    User,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const mobileMenuOpen = ref(false);

const navigation = [
    {
        name: 'Inicio',
        href: '/dashboard',
        icon: LayoutDashboard,
        current: page.url === '/dashboard',
    },
    {
        name: 'Mis Cursos',
        href: '/student/my-courses',
        icon: BookOpen,
        current:
            page.url.startsWith('/student/my-courses') ||
            page.url.startsWith('/student/courses'),
    },
    {
        name: 'Certificados',
        href: '/student/certificates',
        icon: Award,
        current: page.url === '/student/certificates',
    },
];

const logout = () => {
    router.post('/logout');
};

const userName = computed(() => page.props.auth?.user?.name || 'Usuario');
const userEmail = computed(() => page.props.auth?.user?.email || '');
const userInitials = computed(() => {
    const name = userName.value.split(' ');
    if (name.length >= 2) {
        return name[0][0] + name[1][0];
    }
    return name[0][0];
});
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50"
    >
        <Head title="Plataforma de Aprendizaje" />

        <!-- Modern Floating Header -->
        <header class="fixed top-0 right-0 left-0 z-50">
            <div class="mx-auto max-w-7xl px-4 pt-6">
                <div
                    class="rounded-2xl border border-white/20 bg-white/80 shadow-xl shadow-black/5 backdrop-blur-xl"
                >
                    <div class="flex h-20 items-center justify-between px-6">
                        <!-- Logo -->
                        <Link
                            href="/dashboard"
                            class="group flex items-center gap-3"
                        >
                            <div class="relative">
                                <div
                                    class="absolute -inset-1 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 opacity-75 blur transition group-hover:opacity-100"
                                ></div>
                                <div
                                    class="relative flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-cyan-600"
                                >
                                    <GraduationCap class="h-7 w-7 text-white" />
                                </div>
                            </div>
                            <div class="hidden sm:block">
                                <h1
                                    class="bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-xl font-bold text-transparent"
                                >
                                    EduPlatform
                                </h1>
                                <p class="text-xs text-gray-500">
                                    Tu espacio de aprendizaje
                                </p>
                            </div>
                        </Link>

                        <!-- Desktop Navigation Tabs -->
                        <nav class="hidden items-center gap-2 lg:flex">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    'group relative flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium transition-all duration-200',
                                    item.current
                                        ? 'text-blue-600'
                                        : 'text-gray-600 hover:text-gray-900',
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                        'h-4 w-4 transition-transform',
                                        item.current
                                            ? 'scale-110'
                                            : 'group-hover:scale-110',
                                    ]"
                                />
                                <span>{{ item.name }}</span>
                                <div
                                    v-if="item.current"
                                    class="absolute -bottom-2 left-1/2 h-1 w-1 -translate-x-1/2 rounded-full bg-blue-600"
                                />
                            </Link>
                        </nav>

                        <!-- Right Actions -->
                        <div class="flex items-center gap-3">
                            <!-- Search (Desktop) -->
                            <div class="hidden xl:block">
                                <div class="relative">
                                    <Search
                                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <input
                                        type="text"
                                        placeholder="Buscar..."
                                        class="w-64 rounded-xl border border-gray-200 bg-gray-50/50 py-2 pr-4 pl-10 text-sm transition-all focus:w-80 focus:border-blue-300 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <!-- Notifications -->
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative rounded-xl hover:bg-gray-100"
                            >
                                <Bell class="h-5 w-5 text-gray-600" />
                                <span
                                    class="absolute top-2 right-2 flex h-2 w-2"
                                >
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
                                    ></span>
                                </span>
                            </Button>

                            <!-- User Menu -->
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button
                                        class="group flex items-center gap-3 rounded-xl p-1.5 pr-4 transition-all hover:bg-gray-100"
                                    >
                                        <div class="relative">
                                            <div
                                                class="absolute -inset-0.5 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 opacity-75 blur transition group-hover:opacity-100"
                                            ></div>
                                            <div
                                                class="relative flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white"
                                            >
                                                <span
                                                    class="text-sm font-bold"
                                                    >{{ userInitials }}</span
                                                >
                                            </div>
                                        </div>
                                        <div class="hidden text-left lg:block">
                                            <p
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                {{ userName }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Estudiante
                                            </p>
                                        </div>
                                    </button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-64">
                                    <DropdownMenuLabel>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white"
                                            >
                                                <span class="font-bold">{{
                                                    userInitials
                                                }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold">
                                                    {{ userName }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ userEmail }}
                                                </p>
                                            </div>
                                        </div>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem as-child>
                                        <Link
                                            href="/settings/profile"
                                            class="flex items-center"
                                        >
                                            <User class="mr-3 h-4 w-4" />
                                            Mi Perfil
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            href="/settings"
                                            class="flex items-center"
                                        >
                                            <Settings class="mr-3 h-4 w-4" />
                                            Configuración
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        @click="logout"
                                        class="text-red-600"
                                    >
                                        <LogOut class="mr-3 h-4 w-4" />
                                        Cerrar Sesión
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            <!-- Mobile Menu Button -->
                            <Button
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                variant="ghost"
                                size="icon"
                                class="rounded-xl lg:hidden"
                            >
                                <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
                                <X v-else class="h-5 w-5" />
                            </Button>
                        </div>
                    </div>

                    <!-- Mobile Navigation -->
                    <div
                        v-if="mobileMenuOpen"
                        class="border-t border-gray-100 px-4 py-4 lg:hidden"
                    >
                        <nav class="space-y-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                @click="mobileMenuOpen = false"
                                :class="[
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all',
                                    item.current
                                        ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg'
                                        : 'text-gray-600 hover:bg-gray-100',
                                ]"
                            >
                                <component :is="item.icon" class="h-5 w-5" />
                                <span>{{ item.name }}</span>
                            </Link>
                        </nav>

                        <!-- Mobile Search -->
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <div class="relative">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <input
                                    type="text"
                                    placeholder="Buscar cursos..."
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pr-4 pl-10 text-sm focus:border-blue-300 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content with proper spacing for fixed header -->
        <main class="px-4 pt-32 pb-8">
            <div class="mx-auto max-w-7xl">
                <slot />
            </div>
        </main>

        <!-- Optional: Floating Action Button for quick actions -->
        <div class="fixed right-8 bottom-8 lg:hidden">
            <button
                class="group relative flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-cyan-600 shadow-2xl shadow-blue-500/50 transition-all hover:scale-110"
            >
                <div
                    class="absolute -inset-1 rounded-full bg-gradient-to-r from-blue-600 to-cyan-600 opacity-75 blur transition group-hover:opacity-100"
                ></div>
                <Plus class="relative h-6 w-6 text-white" />
            </button>
        </div>
    </div>
</template>
