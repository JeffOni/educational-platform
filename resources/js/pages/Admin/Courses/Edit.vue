<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Curriculum from './Partials/Curriculum.vue';
import Delegations from './Partials/Delegations.vue';

interface Family {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
    family_id: number;
}

interface Subcategory {
    id: number;
    name: string;
    category_id: number;
}

interface Level {
    id: number;
    name: string;
}

interface Teacher {
    id: number;
    name: string;
    email: string;
}

interface Course {
    id: number;
    title: string;
    subtitle: string;
    description: string;
    family_id: number | null;
    category_id: number | null;
    subcategory_id: number | null;
    level_id: number;
    user_id: number;
    image_path: string | null;
    status: number;
    sections: any[];
}

interface Props {
    course: Course;
    families: Family[];
    categories: Category[];
    subcategories: Subcategory[];
    levels: Level[];
    teachers: Teacher[];
    auth: {
        user: { id: number };
        roles: string[];
    };
}

const props = defineProps<Props>();

const form = useForm({
    title: props.course.title,
    subtitle: props.course.subtitle,
    description: props.course.description,
    family_id: props.course.family_id || '',
    category_id: props.course.category_id || '',
    subcategory_id: props.course.subcategory_id || '',
    level_id: props.course.level_id,
    status: props.course.status,
    user_id: props.course.user_id,
    image: null as File | null,
});

const imagePreview = ref<string | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);

// Filtrado en cascada
const filteredCategories = computed(() => {
    if (!form.family_id) return [];
    return props.categories.filter(
        (cat) => cat.family_id === Number(form.family_id),
    );
});

const filteredSubcategories = computed(() => {
    if (!form.category_id) return [];
    return props.subcategories.filter(
        (sub) => sub.category_id === Number(form.category_id),
    );
});

// Limpiar selecciones dependientes
const onFamilyChange = () => {
    form.category_id = '';
    form.subcategory_id = '';
};

const onCategoryChange = () => {
    form.subcategory_id = '';
};

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
    imagePreview.value = null;
};

const submit = () => {
    // Asegurar que status sea un número
    form.status = parseInt(form.status as any);

    console.log('Datos del formulario:', {
        title: form.title,
        status: form.status,
        tipo: typeof form.status,
    });

    form.put(`/admin/courses/${props.course.id}`, {
        onSuccess: () => {
            window.location.href = '/admin/courses';
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

        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
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
                <TabsList class="grid w-full grid-cols-4">
                    <TabsTrigger value="info"
                        >Información del Curso</TabsTrigger
                    >
                    <TabsTrigger value="curriculum"
                        >Contenido del Curso</TabsTrigger
                    >
                    <TabsTrigger value="delegations">Delegaciones</TabsTrigger>
                    <TabsTrigger value="exam">Examen</TabsTrigger>
                </TabsList>

                <!-- Tab: Información -->
                <TabsContent value="info">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2
                            class="mb-4 text-center text-xl font-semibold text-blue-600"
                        >
                            Información Básica
                        </h2>
                        <p class="mb-6 text-center text-gray-600">
                            Actualiza los detalles principales de tu curso
                        </p>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <!-- Columna Izquierda: Imagen -->
                                <div>
                                    <div
                                        class="relative flex h-full min-h-[400px] items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50"
                                    >
                                        <!-- Vista previa de la imagen -->
                                        <div
                                            v-if="
                                                imagePreview ||
                                                course.image_path
                                            "
                                            class="absolute inset-0"
                                        >
                                            <img
                                                :src="
                                                    imagePreview ||
                                                    `/storage/${course.image_path}`
                                                "
                                                alt="Vista previa"
                                                class="h-full w-full rounded-lg object-cover"
                                            />
                                        </div>

                                        <!-- Placeholder cuando no hay imagen -->
                                        <div v-else class="text-center">
                                            <div
                                                class="mx-auto mb-4 flex h-32 w-32 items-center justify-center rounded-lg bg-gray-200"
                                            >
                                                <svg
                                                    class="h-16 w-16 text-gray-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    ></path>
                                                </svg>
                                            </div>
                                            <p
                                                class="mb-2 text-4xl font-bold text-gray-300"
                                            >
                                                IMAGE NOT
                                            </p>
                                            <p
                                                class="text-4xl font-bold text-gray-300"
                                            >
                                                AVAILABLE
                                            </p>
                                        </div>

                                        <!-- Botón de subir imagen -->
                                        <label
                                            class="absolute top-4 left-1/2 -translate-x-1/2 cursor-pointer rounded-md bg-blue-500 px-4 py-2 text-sm text-white hover:bg-blue-600"
                                        >
                                            <svg
                                                class="mr-2 inline h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                            Cambiar Imagen
                                            <input
                                                ref="photoInput"
                                                type="file"
                                                @change="handleImageChange"
                                                accept="image/*"
                                                class="hidden"
                                            />
                                        </label>

                                        <!-- Botón de quitar imagen -->
                                        <button
                                            v-if="imagePreview"
                                            type="button"
                                            @click="removeImage"
                                            class="absolute top-4 right-4 rounded-md bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700"
                                        >
                                            Quitar
                                        </button>
                                    </div>
                                    <p
                                        class="mt-2 text-center text-xs text-gray-500"
                                    >
                                        Imagen Principal *
                                    </p>
                                    <p
                                        class="text-center text-xs text-gray-400"
                                    >
                                        Formatos: JPG, PNG, WebP (máx. 2MB)
                                    </p>
                                </div>

                                <!-- Columna Derecha: Campos -->
                                <div class="space-y-4">
                                    <!-- Familia -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Familia</label
                                        >
                                        <select
                                            v-model="form.family_id"
                                            @change="onFamilyChange"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                        >
                                            <option value="">
                                                Seleccione una familia
                                            </option>
                                            <option
                                                v-for="family in props.families"
                                                :key="family.id"
                                                :value="family.id"
                                            >
                                                {{ family.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Categoría -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Categoría</label
                                        >
                                        <select
                                            v-model="form.category_id"
                                            @change="onCategoryChange"
                                            :disabled="!form.family_id"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:bg-gray-100"
                                        >
                                            <option value="">
                                                Seleccione una categoría
                                            </option>
                                            <option
                                                v-for="cat in filteredCategories"
                                                :key="cat.id"
                                                :value="cat.id"
                                            >
                                                {{ cat.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Subcategoría -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Subcategoría</label
                                        >
                                        <select
                                            v-model="form.subcategory_id"
                                            :disabled="!form.category_id"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:bg-gray-100"
                                        >
                                            <option value="">
                                                Seleccione una subcategoría
                                            </option>
                                            <option
                                                v-for="sub in filteredSubcategories"
                                                :key="sub.id"
                                                :value="sub.id"
                                            >
                                                {{ sub.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Título -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Título *</label
                                        >
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            placeholder="Título del Curso"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            required
                                        />
                                    </div>

                                    <!-- Subtítulo -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Subtítulo *</label
                                        >
                                        <input
                                            v-model="form.subtitle"
                                            type="text"
                                            placeholder="Subtítulo del Curso"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            required
                                        />
                                    </div>

                                    <!-- Descripción -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Descripción *</label
                                        >
                                        <textarea
                                            v-model="form.description"
                                            placeholder="Descripción del Curso"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            rows="6"
                                            required
                                        ></textarea>
                                    </div>

                                    <!-- Nivel -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Nivel *</label
                                        >
                                        <select
                                            v-model="form.level_id"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            required
                                        >
                                            <option value="">
                                                Nivel del Curso
                                            </option>
                                            <option
                                                v-for="level in props.levels"
                                                :key="level.id"
                                                :value="level.id"
                                            >
                                                {{ level.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Profesor Titular (solo admin) -->
                                    <div v-if="auth.roles.includes('admin')">
                                        <label
                                            class="mb-2 block text-sm font-semibold text-blue-700"
                                            >Profesor Titular *</label
                                        >
                                        <select
                                            v-model="form.user_id"
                                            class="w-full rounded-md border border-blue-300 bg-blue-50 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            required
                                        >
                                            <option
                                                v-for="teacher in teachers"
                                                :key="teacher.id"
                                                :value="teacher.id"
                                            >
                                                {{ teacher.name }} ({{
                                                    teacher.email
                                                }})
                                            </option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Solo admin puede cambiar el profesor
                                            titular
                                        </p>
                                    </div>

                                    <!-- Estado -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-semibold"
                                            >Estado *</label
                                        >
                                        <select
                                            v-model.number="form.status"
                                            class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                            required
                                        >
                                            <option :value="1">
                                                📝 Borrador
                                            </option>
                                            <option :value="2">
                                                🔍 En Revisión
                                            </option>
                                            <option :value="3">
                                                ✅ Publicado
                                            </option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">
                                            * Solo los cursos publicados serán
                                            visibles para los estudiantes
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="mt-8 flex justify-center gap-4">
                                <button
                                    type="submit"
                                    class="rounded-md bg-blue-600 px-8 py-3 font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                                    :disabled="form.processing"
                                >
                                    Guardar Cambios
                                </button>
                                <a
                                    href="/admin/courses"
                                    class="rounded-md border border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </TabsContent>

                <!-- Tab: Curriculum -->

                <!-- Tab: Delegaciones -->
                <TabsContent value="delegations">
                    <Delegations
                        :course="course"
                        :teachers="teachers"
                        :is-owner="auth.user.id === course.user_id"
                        :is-admin="auth.roles.includes('admin')"
                    />
                </TabsContent>
                <TabsContent value="curriculum">
                    <Curriculum :course="course" />
                </TabsContent>

                <!-- Tab: Examen -->
                <TabsContent value="exam">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="text-center">
                            <h2
                                class="mb-2 text-xl font-semibold text-blue-600"
                            >
                                Examen Final del Curso
                            </h2>
                            <p class="mb-6 text-gray-600">
                                Configura el examen de evaluación final para tus
                                estudiantes
                            </p>
                            <Link
                                :href="`/admin/courses/${course.id}/exam`"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700"
                            >
                                Gestionar Examen
                            </Link>
                        </div>
                    </div>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
