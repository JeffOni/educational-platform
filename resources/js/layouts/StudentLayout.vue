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
    Check,
    GraduationCap,
    LayoutDashboard,
    LogOut,
    Menu,
    MessageCircle,
    Plus,
    Search,
    Settings,
    User,
    X,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface StudentNotification {
    id: number;
    teacher_name: string;
    lesson_name: string;
    answer_preview: string;
    created_at: string;
}

const page = usePage();
const mobileMenuOpen = ref(false);
const lastSeenAt = ref<string | null>(null);

onMounted(() => {
    lastSeenAt.value = localStorage.getItem('student_notifications_seen_at');
});

const notifications = computed<StudentNotification[]>(() => {
    const data = (page.props as any).notifications;
    return data?.studentNotifications || [];
});

const unreadCount = computed(() => {
    if (!lastSeenAt.value || notifications.value.length === 0) {
        return notifications.value.length;
    }
    const seenDate = new Date(lastSeenAt.value);
    return notifications.value.filter((n) => new Date(n.created_at) > seenDate)
        .length;
});

const markAllAsRead = () => {
    const now = new Date().toISOString();
    localStorage.setItem('student_notifications_seen_at', now);
    lastSeenAt.value = now;
};

const timeAgo = (dateStr: string): string => {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1) return 'Ahora';
    if (diffMin < 60) return `Hace ${diffMin}m`;
    const diffHours = Math.floor(diffMin / 60);
    if (diffHours < 24) return `Hace ${diffHours}h`;
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return `Hace ${diffDays}d`;
    return date.toLocaleDateString('es');
};

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
        <FlashMessage />
        <Head title="Plataforma de Aprendizaje" />

        <!-- Modern Floating Header -->
        <header class="fixed top-0 right-0 left-0 z-50">
            <div class="mx-auto max-w-7xl px-6 pt-6">
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
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="relative rounded-xl hover:bg-gray-100"
                                    >
                                        <Bell class="h-5 w-5 text-gray-600" />
                                        <span
                                            v-if="unreadCount > 0"
                                            class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
                                        >
                                            {{
                                                unreadCount > 9
                                                    ? '9+'
                                                    : unreadCount
                                            }}
                                        </span>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="w-80">
                                    <DropdownMenuLabel
                                        class="flex items-center justify-between"
                                    >
                                        <span>Notificaciones</span>
                                        <button
                                            v-if="unreadCount > 0"
                                            @click.stop="markAllAsRead"
                                            class="flex items-center gap-1 text-xs font-normal text-blue-600 hover:text-blue-800"
                                        >
                                            <Check class="h-3 w-3" />
                                            Marcar como leídas
                                        </button>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />

                                    <div
                                        v-if="notifications.length === 0"
                                        class="px-4 py-6 text-center"
                                    >
                                        <Bell
                                            class="mx-auto h-8 w-8 text-gray-300"
                                        />
                                        <p class="mt-2 text-sm text-gray-500">
                                            No hay notificaciones
                                        </p>
                                    </div>

                                    <div
                                        v-else
                                        class="max-h-72 overflow-y-auto"
                                    >
                                        <DropdownMenuItem
                                            v-for="notif in notifications"
                                            :key="notif.id"
                                            class="flex cursor-default flex-col items-start gap-1 px-4 py-3"
                                            :class="{
                                                'bg-blue-50/50':
                                                    !lastSeenAt ||
                                                    new Date(notif.created_at) >
                                                        new Date(lastSeenAt),
                                            }"
                                        >
                                            <div
                                                class="flex w-full items-start justify-between gap-2"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <MessageCircle
                                                        class="h-4 w-4 shrink-0 text-blue-500"
                                                    />
                                                    <span
                                                        class="text-sm font-medium text-gray-900"
                                                    >
                                                        {{ notif.teacher_name }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="shrink-0 text-[10px] text-gray-400"
                                                >
                                                    {{
                                                        timeAgo(
                                                            notif.created_at,
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <p
                                                class="pl-6 text-xs text-gray-500"
                                            >
                                                respondió en
                                                <span
                                                    class="font-medium text-gray-700"
                                                    >{{
                                                        notif.lesson_name
                                                    }}</span
                                                >
                                            </p>
                                            <p
                                                class="pl-6 text-xs text-gray-600"
                                            >
                                                "{{ notif.answer_preview }}"
                                            </p>
                                        </DropdownMenuItem>
                                    </div>
                                </DropdownMenuContent>
                            </DropdownMenu>

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
        <main class="pt-32 pb-8">
            <div class="mx-auto max-w-7xl px-6">
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
