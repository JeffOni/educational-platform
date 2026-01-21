<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Trash2, Edit, UserPlus } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    student_type: string | null;
    roles: Array<{ name: string }>;
}

interface Props {
    users: {
        data: User[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}

defineProps<Props>();

const deleteUser = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este usuario?')) {
        router.delete(`/admin/users/${id}`, {
            preserveScroll: true,
        });
    }
};

const getStudentTypeBadge = (studentType: string | null) => {
    if (!studentType) return '';
    return studentType === 'external' ? 'Externo' : 'Interno';
};

const getStudentTypeVariant = (studentType: string | null): 'default' | 'secondary' | 'outline' => {
    if (!studentType) return 'default';
    return studentType === 'external' ? 'outline' : 'default';
};
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Usuarios</h2>
                    <p class="text-muted-foreground">
                        Gestiona los usuarios del sistema
                    </p>
                </div>
                <Link href="/admin/users/create">
                    <Button>
                        <UserPlus class="mr-2 h-4 w-4" />
                        Crear Usuario
                    </Button>
                </Link>
            </div>

            <div class="rounded-md border bg-card">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nombre</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Rol</TableHead>
                            <TableHead>Tipo de Estudiante</TableHead>
                            <TableHead class="text-right">Acciones</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    {{ user.roles[0]?.name || 'Sin rol' }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge 
                                    v-if="user.student_type"
                                    :variant="getStudentTypeVariant(user.student_type)"
                                >
                                    {{ getStudentTypeBadge(user.student_type) }}
                                </Badge>
                                <span v-else class="text-muted-foreground text-sm">-</span>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/admin/users/${user.id}/edit`">
                                        <Button variant="ghost" size="icon">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="deleteUser(user.id)"
                                    >
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell colspan="5" class="text-center py-8">
                                <p class="text-muted-foreground">No hay usuarios registrados</p>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
