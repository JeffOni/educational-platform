<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Head, Link, router } from '@inertiajs/vue3';

interface Family {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
    categories_count: number;
}

interface Props {
    families: Family[];
}

defineProps<Props>();

const deleteFamily = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta familia?')) {
        router.delete(admin.families.destroy({ family: id }).url);
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Familias" />

        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Familias</h1>
                    <p class="mt-1 text-muted-foreground">
                        Gestiona las familias de contenido
                    </p>
                </div>
                <Link
                    :href="admin.families.create().url"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Nueva Familia
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
                                Categorías
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="family in families" :key="family.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ family.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ family.slug }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ family.categories_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                        family.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        family.is_active ? 'Activa' : 'Inactiva'
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="
                                        admin.families.edit({
                                            family: family.id,
                                        }).url
                                    "
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteFamily(family.id)"
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
