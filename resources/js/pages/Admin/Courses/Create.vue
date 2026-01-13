<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Category {
    id: number;
    name: string;
}

interface Level {
    id: number;
    name: string;
}

interface Props {
    categories: Category[];
    levels: Level[];
}

const props = defineProps<Props>();

const form = useForm({
    title: '',
    subtitle: '',
    description: '',
    price: 0,
    category_id: '',
    level_id: '',
    image: null as File | null,
});

const submit = () => {
    form.post('/admin/courses');
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/admin/courses' },
    { title: 'Crear Curso', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Crear Curso" />

        <div class="p-8">
            <div class="mx-auto max-w-4xl">
                <div class="rounded-lg bg-white p-6 shadow">
                    <h1 class="mb-6 text-2xl font-bold">Crear Nuevo Curso</h1>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Título *</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-md border px-3 py-2"
                                required
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Subtítulo *</label
                            >
                            <input
                                v-model="form.subtitle"
                                type="text"
                                class="w-full rounded-md border px-3 py-2"
                                required
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Descripción *</label
                            >
                            <textarea
                                v-model="form.description"
                                class="w-full rounded-md border px-3 py-2"
                                rows="5"
                                required
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium"
                                    >Categoría *</label
                                >
                                <select
                                    v-model="form.category_id"
                                    class="w-full rounded-md border px-3 py-2"
                                    required
                                >
                                    <option value="">Selecciona</option>
                                    <option
                                        v-for="cat in props.categories"
                                        :key="cat.id"
                                        :value="cat.id"
                                    >
                                        {{ cat.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium"
                                    >Nivel *</label
                                >
                                <select
                                    v-model="form.level_id"
                                    class="w-full rounded-md border px-3 py-2"
                                    required
                                >
                                    <option value="">Selecciona</option>
                                    <option
                                        v-for="level in props.levels"
                                        :key="level.id"
                                        :value="level.id"
                                    >
                                        {{ level.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium"
                                    >Precio ($) *</label
                                >
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    class="w-full rounded-md border px-3 py-2"
                                    required
                                />
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button
                                type="submit"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                :disabled="form.processing"
                            >
                                Crear Curso
                            </button>
                            <a
                                href="/admin/courses"
                                class="rounded-md border px-4 py-2 hover:bg-gray-50"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
