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
    family: Family | null;
}

interface Subcategory {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
    category: Category;
    courses_count: number;
}

interface Props {
    subcategories: Subcategory[];
}

defineProps<Props>();

const deleteSubcategory = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta subcategoría?')) {
        router.delete(admin.subcategories.destroy({ subcategory: id }).url);
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Subcategorías" />

        <div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Subcategorías</h1>
                    <p class="mt-1 text-muted-foreground">
                        Gestiona las subcategorías de contenido
                    </p>
                </div>
                <Link
                    :href="admin.subcategories.create().url"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Nueva Subcategoría
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
                                Categoría
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Familia
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
                        <tr
                            v-for="subcategory in subcategories"
                            :key="subcategory.id"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ subcategory.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ subcategory.slug }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ subcategory.category.name }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ subcategory.category.family?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ subcategory.courses_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                        subcategory.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        subcategory.is_active
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
                                        admin.subcategories.edit({
                                            subcategory: subcategory.id,
                                        }).url
                                    "
                                    class="mr-3 text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteSubcategory(subcategory.id)"
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
