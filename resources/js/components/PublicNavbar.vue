<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { dashboard, login } from '@/routes';
import { cn } from '@/lib/utils';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Award,
    BookOpen,
    GraduationCap,
    LayoutDashboard,
    LogOut,
    Menu,
    Settings,
    ShoppingCart,
    Sparkles,
    User,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    cartCount?: number;
    showRegister?: boolean;
    transparent?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    cartCount: 0,
    showRegister: true,
    transparent: false,
});

const page = usePage();
const mobileMenuOpen = ref(false);

const user = computed(() => page.props.auth?.user);
const userName = computed(() => user.value?.name || 'Usuario');
const userInitials = computed(() => {
    const name = userName.value.split(' ');
    if (name.length >= 2) {
        return name[0][0] + name[1][0];
    }
    return name[0][0];
});

const navLinks = [
    { href: '/', label: 'Inicio', icon: LayoutDashboard },
    { href: '/courses', label: 'Cursos', icon: BookOpen },
];

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <nav
        :class="
            cn(
                'fixed top-0 z-50 w-full transition-all duration-300',
                transparent
                    ? 'bg-transparent'
                    : 'border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60',
            )
        "
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex items-center gap-2 transition-transform hover:scale-105"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 shadow-lg"
                    >
                        <GraduationCap class="h-5 w-5 text-primary-foreground" />
                    </div>
                    <span
                        class="hidden text-xl font-bold tracking-tight sm:block"
                    >
                        <span
                            class="bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent"
                        >
                            EduPlatform
                        </span>
                    </span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden items-center gap-6 md:flex">
                    <NavigationMenu>
                        <NavigationMenuList>
                            <NavigationMenuItem>
                                <NavigationMenuLink as-child>
                                    <Link
                                        href="/"
                                        :class="
                                            cn(
                                                'group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:outline-none disabled:pointer-events-none disabled:opacity-50',
                                            )
                                        "
                                    >
                                        Inicio
                                    </Link>
                                </NavigationMenuLink>
                            </NavigationMenuItem>

                            <NavigationMenuItem>
                                <NavigationMenuLink as-child>
                                    <Link
                                        href="/courses"
                                        :class="
                                            cn(
                                                'group inline-flex h-9 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:outline-none disabled:pointer-events-none disabled:opacity-50',
                                            )
                                        "
                                    >
                                        Cursos
                                    </Link>
                                </NavigationMenuLink>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <!-- Cart -->
                    <Link
                        href="/cart"
                        class="relative rounded-md p-2 text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                    >
                        <ShoppingCart class="h-5 w-5" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground shadow-lg"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>

                    <!-- Auth Buttons / User Menu -->
                    <template v-if="user">
                        <!-- User Dropdown Menu -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="hidden gap-2 md:flex"
                                >
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary/80 text-xs font-bold text-primary-foreground"
                                    >
                                        {{ userInitials }}
                                    </div>
                                    <span class="max-w-[150px] truncate">
                                        {{ userName }}
                                    </span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <DropdownMenuLabel>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-sm font-medium">
                                            {{ userName }}
                                        </p>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="dashboard()"
                                        class="flex w-full cursor-pointer items-center gap-2"
                                    >
                                        <LayoutDashboard class="h-4 w-4" />
                                        Dashboard
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/student/my-courses"
                                        class="flex w-full cursor-pointer items-center gap-2"
                                    >
                                        <BookOpen class="h-4 w-4" />
                                        Mis Cursos
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/student/certificates"
                                        class="flex w-full cursor-pointer items-center gap-2"
                                    >
                                        <Award class="h-4 w-4" />
                                        Certificados
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link
                                        href="/user/profile"
                                        class="flex w-full cursor-pointer items-center gap-2"
                                    >
                                        <Settings class="h-4 w-4" />
                                        Configuración
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    @click="logout"
                                    class="cursor-pointer text-destructive focus:text-destructive"
                                >
                                    <LogOut class="mr-2 h-4 w-4" />
                                    Cerrar Sesión
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                    <template v-else>
                        <Link :href="login()" class="hidden md:block">
                            <Button variant="ghost" size="sm">
                                Iniciar Sesión
                            </Button>
                        </Link>
                        <Link
                            v-if="showRegister"
                            href="/register"
                            class="hidden md:block"
                        >
                            <Button size="sm" class="gap-2">
                                <Sparkles class="h-4 w-4" />
                                Empezar Gratis
                            </Button>
                        </Link>
                    </template>

                    <!-- Mobile Menu -->
                    <Sheet v-model:open="mobileMenuOpen">
                        <SheetTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="md:hidden"
                            >
                                <Menu class="h-5 w-5" />
                                <span class="sr-only">Toggle menu</span>
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-[300px] sm:w-[400px]">
                            <SheetHeader>
                                <SheetTitle class="flex items-center gap-2">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80"
                                    >
                                        <GraduationCap
                                            class="h-4 w-4 text-primary-foreground"
                                        />
                                    </div>
                                    EduPlatform
                                </SheetTitle>
                            </SheetHeader>

                            <div class="mt-8 flex flex-col gap-4">
                                <!-- User Info for Mobile (if authenticated) -->
                                <div
                                    v-if="user"
                                    class="mb-4 flex items-center gap-3 rounded-lg bg-accent/50 p-4"
                                >
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary/80 text-sm font-bold text-primary-foreground"
                                    >
                                        {{ userInitials }}
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p
                                            class="truncate text-sm font-medium"
                                        >
                                            {{ userName }}
                                        </p>
                                        <p
                                            class="truncate text-xs text-muted-foreground"
                                        >
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Mobile Nav Links -->
                                <Link
                                    v-for="link in navLinks"
                                    :key="link.href"
                                    :href="link.href"
                                    @click="mobileMenuOpen = false"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <component :is="link.icon" class="h-4 w-4" />
                                    {{ link.label }}
                                </Link>

                                <div class="my-4 border-t border-border"></div>

                                <!-- Mobile Auth/User Actions -->
                                <template v-if="user">
                                    <Link
                                        :href="dashboard()"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button
                                            variant="outline"
                                            class="w-full justify-start gap-2"
                                        >
                                            <LayoutDashboard class="h-4 w-4" />
                                            Dashboard
                                        </Button>
                                    </Link>
                                    <Link
                                        href="/student/my-courses"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button
                                            variant="outline"
                                            class="w-full justify-start gap-2"
                                        >
                                            <BookOpen class="h-4 w-4" />
                                            Mis Cursos
                                        </Button>
                                    </Link>
                                    <Link
                                        href="/student/certificates"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button
                                            variant="outline"
                                            class="w-full justify-start gap-2"
                                        >
                                            <Award class="h-4 w-4" />
                                            Certificados
                                        </Button>
                                    </Link>

                                    <div class="my-2 border-t border-border"></div>

                                    <Link
                                        href="/user/profile"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button
                                            variant="ghost"
                                            class="w-full justify-start gap-2"
                                        >
                                            <Settings class="h-4 w-4" />
                                            Configuración
                                        </Button>
                                    </Link>

                                    <Button
                                        @click="logout"
                                        variant="ghost"
                                        class="w-full justify-start gap-2 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                    >
                                        <LogOut class="h-4 w-4" />
                                        Cerrar Sesión
                                    </Button>
                                </template>
                                <template v-else>
                                    <Link
                                        :href="login()"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button variant="outline" class="w-full">
                                            Iniciar Sesión
                                        </Button>
                                    </Link>
                                    <Link
                                        v-if="showRegister"
                                        href="/register"
                                        @click="mobileMenuOpen = false"
                                    >
                                        <Button class="w-full gap-2">
                                            <Sparkles class="h-4 w-4" />
                                            Empezar Gratis
                                        </Button>
                                    </Link>
                                </template>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </div>
    </nav>
</template>
