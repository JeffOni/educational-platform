<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Head, useForm } from '@inertiajs/vue3';

interface Family {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    family_id: number | null;
    family: Family | null;
}

interface Subcategory {
    id: number;
    name: string;
    category_id: number;
    is_active: boolean;
    category: Category;
}

interface Props {
    subcategory: Subcategory;
    categories: Category[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.subcategory.name,
    category_id: props.subcategory.category_id,
    is_active: props.subcategory.is_active,
});

const submit = () => {
    form.put(
        admin.subcategories.update({ subcategory: props.subcategory.id }).url,
    );
};
</script>

<template>
    <AppLayout>
        <Head :title="`Editar: ${subcategory.name}`" />

        <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">
            <div>
                <h1 class="text-3xl font-bold">Editar Subcategoría</h1>
                <p class="mt-1 text-muted-foreground">
                    Actualiza los detalles de la subcategoría
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Nombre *
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-md border px-3 py-2"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Categoría *
                        </label>
                        <select
                            v-model="form.category_id"
                            class="w-full rounded-md border px-3 py-2"
                            required
                        >
                            <option value="">Selecciona una categoría</option>
                            <optgroup
                                v-for="(
                                    familyCategories, familyName
                                ) in categories.reduce(
                                    (acc, cat) => {
                                        const family =
                                            cat.family?.name || 'Sin familia';
                                        if (!acc[family]) acc[family] = [];
                                        acc[family].push(cat);
                                        return acc;
                                    },
                                    {} as Record<string, Category[]>,
                                )"
                                :key="familyName"
                                :label="familyName"
                            >
                                <option
                                    v-for="cat in familyCategories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name }}
                                </option>
                            </optgroup>
                        </select>
                        <p
                            v-if="form.errors.category_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.category_id }}
                        </p>
                    </div>

                    <!-- Estado -->
                    <div class="flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <label class="ml-2 text-sm font-medium"> Activa </label>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            Actualizar Subcategoría
                        </button>
                        <a
                            :href="admin.subcategories.index().url"
                            class="rounded-md border px-4 py-2 hover:bg-gray-50"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
