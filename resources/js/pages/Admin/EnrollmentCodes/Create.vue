<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Course {
    id: number;
    title: string;
}

interface Props {
    courses: Course[];
}

defineProps<Props>();

const form = useForm({
    course_id: '',
    quantity: 10,
    expires_at: '',
});

const submit = () => {
    form.post('/admin/enrollment-codes/generate-batch', {
        onSuccess: () => form.reset('quantity', 'expires_at'),
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Códigos de Inscripción', href: '/admin/enrollment-codes' },
    { title: 'Generar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Generar Códigos de Inscripción" />

        <div class="w-full p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <CardTitle>Generar Códigos de Inscripción</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold"
                                    >Curso *</label
                                >
                                <select
                                    v-model="form.course_id"
                                    class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                    required
                                >
                                    <option value="">
                                        Seleccione un curso
                                    </option>
                                    <option
                                        v-for="course in courses"
                                        :key="course.id"
                                        :value="course.id"
                                    >
                                        {{ course.title }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.course_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.course_id }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold"
                                    >Cantidad de códigos *</label
                                >
                                <input
                                    v-model.number="form.quantity"
                                    type="number"
                                    min="1"
                                    max="100"
                                    class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                    required
                                />
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Máximo 100 códigos por lote
                                </p>
                                <p
                                    v-if="form.errors.quantity"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.quantity }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold"
                                >Fecha de expiración (opcional)</label
                            >
                            <input
                                v-model="form.expires_at"
                                type="datetime-local"
                                class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Si no se establece, los códigos no expirarán
                            </p>
                            <p
                                v-if="form.errors.expires_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.expires_at }}
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Generar {{ form.quantity }} Códigos
                            </Button>
                            <Button variant="outline" as-child>
                                <a href="/admin/enrollment-codes">Cancelar</a>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
