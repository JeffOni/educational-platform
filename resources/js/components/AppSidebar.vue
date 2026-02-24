<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import student from '@/routes/student';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Award,
    BarChart3,
    BookOpen,
    ClipboardList,
    Folder,
    GraduationCap,
    KeyRound,
    Layers,
    LayoutGrid,
    MessageCircle,
    MonitorPlay,
    Tags,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const roles = computed(() => page.props.auth.roles || []);
const user = computed(() => page.props.auth.user);
const notifications = computed(() => (page.props as any).notifications || { pendingQuestions: 0, studentNotifications: [] });
const pendingQuestions = computed(() => notifications.value.pendingQuestions || 0);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // ADMIN: Acceso completo a todos los módulos
    if (roles.value.includes('admin')) {
        items.push(
            {
                title: 'Dashboard',
                href: dashboard().url,
                icon: LayoutGrid,
            },
            {
                title: 'Mis Cursos',
                href: admin.courses.index().url,
                icon: MonitorPlay,
            },
            {
                title: 'Tareas',
                href: '/admin/assignments',
                icon: ClipboardList,
            },
            {
                title: 'Preguntas',
                href: admin.questions.index().url,
                icon: MessageCircle,
                badge: pendingQuestions.value,
            },            {
                title: 'Usuarios',
                href: '/admin/users',
                icon: Users,
            },
            {
                title: 'Códigos de Inscripción',
                href: '/admin/enrollment-codes',
                icon: KeyRound,
            },
            {
                title: 'Certificados',
                href: '/admin/certificates',
                icon: Award,
            },
            {
                title: 'Familias',
                href: '/admin/families',
                icon: Layers,
            },
            {
                title: 'Categorías',
                href: '/admin/categories',
                icon: Tags,
            },
            {
                title: 'Subcategorías',
                href: '/admin/subcategories',
                icon: Tags,
            },
            {
                title: 'Niveles',
                href: '/admin/levels',
                icon: Layers,
            },
            {
                title: 'Estadísticas',
                href: '#', // TODO: Ruta estadísticas
                icon: BarChart3,
            },
        );
    }
    // TEACHER: Solo gestión de sus cursos y preguntas
    else if (roles.value.includes('teacher')) {
        items.push(
            {
                title: 'Dashboard',
                href: dashboard().url,
                icon: LayoutGrid,
            },
            {
                title: 'Mis Cursos',
                href: admin.courses.index().url,
                icon: MonitorPlay,
            },
            {
                title: 'Tareas',
                href: '/admin/assignments',
                icon: ClipboardList,
            },
            {
                title: 'Preguntas',
                href: admin.questions.index().url,
                icon: MessageCircle,
                badge: pendingQuestions.value,
            },        );
    }
    // STUDENT: Panel de estudiante
    else if (roles.value.includes('student')) {
        items.push(
            {
                title: 'Dashboard',
                href: dashboard().url,
                icon: LayoutGrid,
            },
            {
                title: 'Mis Cursos',
                href: student.courses.index().url,
                icon: GraduationCap,
            },
        );

        items.push({
            title: 'Certificados',
            href: '#', // TODO: Ruta certificados
            icon: Award,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard().url">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
