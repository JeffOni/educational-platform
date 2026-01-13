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
    Folder,
    GraduationCap,
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

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
            icon: LayoutGrid,
        },
    ];

    if (roles.value.includes('teacher') || roles.value.includes('admin')) {
        items.push({
            title: 'Mis Cursos',
            href: admin.courses.index().url,
            icon: MonitorPlay,
        });
        items.push({
            title: 'Preguntas',
            href: admin.questions.index().url,
            icon: MessageCircle,
        });
    }

    if (roles.value.includes('admin')) {
        items.push({
            title: 'Usuarios',
            href: '/admin/users',
            icon: Users,
        });
        items.push({
            title: 'Categorías',
            href: '/admin/categories',
            icon: Tags,
        });
        items.push({
            title: 'Estadísticas',
            href: '#', // TODO: Ruta estadísticas
            icon: BarChart3,
        });
    }

    if (roles.value.includes('student')) {
        items.push({
            title: 'Mis Cursos',
            href: student.courses.index().url,
            icon: GraduationCap,
        });
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
