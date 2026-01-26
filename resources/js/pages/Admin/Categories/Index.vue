<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Head, Link, router } from '@inertiajs/vue3';

interface Family {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
    family: Family | null;
    subcategories_count: number;
    courses_count: number;
}

interface Props {
    categories: Category[];
}

defineProps<Props>();

const deleteCategory = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta categoría?')) {
        router.delete(admin.categories.destroy({ category: id }).url);
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Categorías" />

        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Categorías</h1>
                    <p class="mt-1 text-muted-foreground">
                        Gestiona las categorías de contenido
                    </p>
                </div>
                <Link
                    :href="admin.categories.create().url"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Nueva Categoría
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
                                Familia
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Subcategorías
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Cursos
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
                        <tr v-for="category in categories" :key="category.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ category.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ category.slug }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ category.family?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ category.subcategories_count }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ category.courses_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                        category.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        category.is_active
                                            ? 'Activa'
                                            : 'Inactiva'
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <Link
                                    :href="
                                        admin.categories.edit({
                                            category: category.id,
                                        }).url
                                    "
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteCategory(category.id)"
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
