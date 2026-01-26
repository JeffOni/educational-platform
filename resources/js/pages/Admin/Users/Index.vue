<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

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
    return studentType === 'external'
        ? 'Estudiante Externo'
        : 'Estudiante de Academia';
};

const getStudentTypeVariant = (
    studentType: string | null,
): 'default' | 'secondary' | 'outline' => {
    if (!studentType) return 'default';
    return studentType === 'external' ? 'outline' : 'default';
};
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout>
        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Usuarios</h1>
                    <p class="mt-1 text-muted-foreground">
                        Gestiona los usuarios del sistema
                    </p>
                </div>
                <Link
                    href="/admin/users/create"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Crear Usuario
                </Link>
            </div>

            <!-- Tabla -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Nombre
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Email
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Rol
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Modalidad
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ user.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ user.roles[0]?.name || 'Sin rol' }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <span
                                    v-if="user.student_type"
                                    :class="[
                                        'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                        user.student_type === 'external'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-purple-100 text-purple-800',
                                    ]"
                                >
                                    {{ getStudentTypeBadge(user.student_type) }}
                                </span>
                                <span v-else>-</span>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="`/admin/users/${user.id}/edit`"
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteUser(user.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
