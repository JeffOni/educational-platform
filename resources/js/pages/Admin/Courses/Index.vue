<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle, Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next';

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
    status: number;
    image_path: string | null;
    category: Category;
    level: Level;
    sections_count: number;
}

interface Props {
    courses: Course[];
}

const props = defineProps<Props>();

// Debug: ver qué datos llegan
console.log(
    'Cursos cargados:',
    props.courses.map((c) => ({
        id: c.id,
        title: c.title,
        status: c.status,
        tipo: typeof c.status,
    })),
);

const getStatusBadge = (status: number) => {
    if (status === 1) return 'Borrador';
    if (status === 2) return 'En Revisión';
    if (status === 3) return 'Publicado';
    return 'Borrador';
};

const getStatusVariant = (
    status: number,
): 'default' | 'secondary' | 'destructive' => {
    if (status === 3) return 'default'; // Publicado - verde
    if (status === 2) return 'secondary'; // En revisión - gris
    return 'secondary'; // Borrador - gris
};

const publishCourse = (courseId: number) => {
    if (confirm('¿Estás seguro de publicar este curso?')) {
        router.put(
            `/admin/courses/${courseId}/publish`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Curso publicado
                },
            },
        );
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Mis Cursos" />

        <div class="w-full p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Gestión de Cursos</CardTitle>
                        <Button as-child>
                            <Link href="/admin/courses/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nuevo Curso
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="courses.length === 0"
                        class="py-12 text-center text-muted-foreground"
                    >
                        <p class="mb-2 text-lg font-medium">
                            No tienes cursos creados
                        </p>
                        <p class="text-sm">
                            Crea tu primer curso para comenzar
                        </p>
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Curso</TableHead>
                                <TableHead>Categoría</TableHead>
                                <TableHead>Nivel</TableHead>
                                <TableHead>Precio</TableHead>
                                <TableHead>Secciones</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right"
                                    >Acciones</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="course in courses"
                                :key="course.id"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div
                                            v-if="course.image_path"
                                            class="h-12 w-12 flex-shrink-0 overflow-hidden rounded bg-muted"
                                        >
                                            <img
                                                :src="`/storage/${course.image_path}`"
                                                :alt="course.title"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded bg-muted"
                                        >
                                            <Eye
                                                class="h-5 w-5 text-muted-foreground"
                                            />
                                        </div>
                                        <div>
                                            <div class="font-medium">
                                                {{ course.title }}
                                            </div>
                                            <div
                                                class="line-clamp-1 text-sm text-muted-foreground"
                                            >
                                                {{ course.subtitle }}
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{
                                        course.category.name
                                    }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{
                                        course.level.name
                                    }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <span class="font-semibold"
                                        >${{ course.price }}</span
                                    >
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">{{
                                        course.sections_count
                                    }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            getStatusVariant(course.status)
                                        "
                                    >
                                        {{ getStatusBadge(course.status) }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            v-if="course.status !== 3"
                                            variant="default"
                                            size="sm"
                                            @click="publishCourse(course.id)"
                                            title="Publicar curso"
                                        >
                                            <CheckCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            as-child
                                        >
                                            <Link
                                                :href="`/admin/courses/${course.id}/edit`"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            as-child
                                        >
                                            <Link
                                                :href="`/admin/courses/${course.id}`"
                                                method="delete"
                                                as="button"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
