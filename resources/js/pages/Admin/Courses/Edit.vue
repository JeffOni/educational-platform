<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Curriculum from './Partials/Curriculum.vue';

interface Category {
    id: number;
    name: string;
}

interface Level {
    id: number;
    name: string;
}

interface Course {
    id: number;
    title: string;
    subtitle: string;
    description: string;
    price: number;
    category_id: number;
    level_id: number;
    image_path: string | null;
    status: number;
    sections: any[];
}

interface Props {
    course: Course;
    categories: Category[];
    levels: Level[];
}

const props = defineProps<Props>();

const form = useForm({
    title: props.course.title,
    subtitle: props.course.subtitle,
    description: props.course.description,
    price: props.course.price,
    category_id: props.course.category_id,
    level_id: props.course.level_id,
    status: props.course.status,
    image: null as File | null,
});

const imagePreview = ref<string | null>(
    props.course.image_path ? `/storage/${props.course.image_path}` : null,
);
const photoInput = ref<HTMLInputElement | null>(null);

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
    imagePreview.value = props.course.image_path
        ? `/storage/${props.course.image_path}`
        : null;
};

const submit = () => {
    // Asegurar que status sea un número
    form.status = parseInt(form.status as any);
    
    console.log('Datos del formulario:', {
        title: form.title,
        status: form.status,
        tipo: typeof form.status
    });
    
    form.put(`/admin/courses/${props.course.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Curso actualizado exitosamente');
        },
        onError: (errors) => {
            console.error('Error al actualizar:', errors);
        },
    });
};

const getStatusBadge = () => {
    return props.course.status === 1 ? 'Borrador' : 'Publicado';
};

const getStatusVariant = (): 'default' | 'secondary' => {
    return props.course.status === 2 ? 'default' : 'secondary';
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/admin/courses' },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Editar: ${course.title}`" />

        <div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ course.title }}</h1>
                    <p class="mt-1 text-muted-foreground">Gestiona tu curso</p>
                </div>
                <Badge :variant="getStatusVariant()">
                    {{ getStatusBadge() }}
                </Badge>
            </div>

            <!-- Tabs -->
            <Tabs default-value="info" class="w-full">
                <TabsList class="grid w-full grid-cols-2">
                    <TabsTrigger value="info"
                        >Información del Curso</TabsTrigger
                    >
                    <TabsTrigger value="curriculum"
                        >Contenido del Curso</TabsTrigger
                    >
                </TabsList>

                <!-- Tab: Información -->
                <TabsContent value="info">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-4 text-xl font-semibold">
                            Información Básica
                        </h2>
                        <p class="mb-6 text-gray-600">
                            Actualiza los detalles principales de tu curso
                        </p>

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
                                    <label
                                        class="mb-2 block text-sm font-medium"
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
                                    <label
                                        class="mb-2 block text-sm font-medium"
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
                                    <label
                                        class="mb-2 block text-sm font-medium"
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

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium"
                                        >Estado del Curso *</label
                                    >
                                    <select
                                        v-model.number="form.status"
                                        class="w-full rounded-md border px-3 py-2"
                                        required
                                    >
                                        <option :value="1">📝 Borrador</option>
                                        <option :value="2">🔍 En Revisión</option>
                                        <option :value="3">✅ Publicado</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Solo los cursos publicados serán visibles para los estudiantes
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button
                                    type="submit"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                    :disabled="form.processing"
                                >
                                    Guardar Cambios
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
                </TabsContent>

                <!-- Tab: Curriculum -->
                <TabsContent value="curriculum">
                    <Curriculum :course="course" />
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
