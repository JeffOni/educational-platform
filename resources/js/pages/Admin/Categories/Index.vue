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
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
    courses_count: number;
}

interface Props {
    categories: Category[];
}

defineProps<Props>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Categorías', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Categorías" />

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex justify-between items-center">
                        <CardTitle>Gestión de Categorías</CardTitle>
                        <Button as-child>
                            <Link href="/admin/categories/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nueva Categoría
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="categories.length === 0" class="text-center py-8 text-muted-foreground">
                        No hay categorías registradas
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nombre</TableHead>
                                <TableHead class="text-center">Cursos</TableHead>
                                <TableHead class="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="category in categories" :key="category.id">
                                <TableCell class="font-medium">
                                    {{ category.name }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">
                                        {{ category.courses_count }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="`/admin/categories/${category.id}/edit`">
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            as-child
                                            :disabled="category.courses_count > 0"
                                        >
                                            <Link
                                                :href="`/admin/categories/${category.id}`"
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
