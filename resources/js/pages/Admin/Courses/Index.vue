<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Plus, Pencil, Trash2, Eye } from 'lucide-vue-next';

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

defineProps<Props>();

const getStatusBadge = (status: number) => {
    return status === 1 ? 'Borrador' : status === 2 ? 'Publicado' : 'Borrador';
};

const getStatusVariant = (status: number): 'default' | 'secondary' | 'destructive' => {
    return status === 2 ? 'default' : 'secondary';
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Mis Cursos" />

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex justify-between items-center">
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
                    <div v-if="courses.length === 0" class="text-center py-12 text-muted-foreground">
                        <p class="text-lg font-medium mb-2">No tienes cursos creados</p>
                        <p class="text-sm">Crea tu primer curso para comenzar</p>
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
                                <TableHead class="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="course in courses" :key="course.id">
                                <TableCell>
                                    <div class="flex items-center gap-3">
                                        <div
                                            v-if="course.image_path"
                                            class="w-12 h-12 rounded overflow-hidden bg-muted flex-shrink-0"
                                        >
                                            <img
                                                :src="`/storage/${course.image_path}`"
                                                :alt="course.title"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="w-12 h-12 rounded bg-muted flex items-center justify-center flex-shrink-0"
                                        >
                                            <Eye class="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ course.title }}</div>
                                            <div class="text-sm text-muted-foreground line-clamp-1">
                                                {{ course.subtitle }}
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ course.category.name }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ course.level.name }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <span class="font-semibold">${{ course.price }}</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">{{ course.sections_count }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusVariant(course.status)">
                                        {{ getStatusBadge(course.status) }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="`/admin/courses/${course.id}/edit`">
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="destructive" size="sm" as-child>
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
