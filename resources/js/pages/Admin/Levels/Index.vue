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

interface Level {
    id: number;
    name: string;
    courses_count: number;
}

interface Props {
    levels: Level[];
}

defineProps<Props>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Niveles', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Niveles" />

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex justify-between items-center">
                        <CardTitle>Gestión de Niveles</CardTitle>
                        <Button as-child>
                            <Link href="/admin/levels/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nuevo Nivel
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="levels.length === 0" class="text-center py-8 text-muted-foreground">
                        No hay niveles registrados
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
                            <TableRow v-for="level in levels" :key="level.id">
                                <TableCell class="font-medium">
                                    {{ level.name }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge variant="secondary">
                                        {{ level.courses_count }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="`/admin/levels/${level.id}/edit`">
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            as-child
                                            :disabled="level.courses_count > 0"
                                        >
                                            <Link
                                                :href="`/admin/levels/${level.id}`"
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
