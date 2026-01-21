<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    is_active: true,
});

const submit = () => {
    form.post(admin.families.store().url);
};
</script>

<template>
    <AppLayout>
        <Head title="Crear Familia" />

        <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">
            <div>
                <h1 class="text-3xl font-bold">Nueva Familia</h1>
                <p class="mt-1 text-muted-foreground">
                    Crea una nueva familia de contenido
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
                            Crear Familia
                        </button>
                        <a
                            :href="admin.families.index().url"
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
