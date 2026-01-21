<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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

interface Props {
    families: Family[];
    categories: Category[];
    subcategories: Subcategory[];
    levels: Level[];
}

const props = defineProps<Props>();

const form = useForm({
    title: '',
    subtitle: '',
    description: '',
    price: 0,
    family_id: '',
    category_id: '',
    subcategory_id: '',
    level_id: '',
    image: null as File | null,
});

const imagePreview = ref<string | null>(null);

// Filtrado en cascada
const filteredCategories = computed(() => {
    if (!form.family_id) return [];
    return props.categories.filter(cat => cat.family_id === Number(form.family_id));
});

const filteredSubcategories = computed(() => {
    if (!form.category_id) return [];
    return props.subcategories.filter(sub => sub.category_id === Number(form.category_id));
});

// Limpiar selecciones dependientes
const onFamilyChange = () => {
    form.category_id = '';
    form.subcategory_id = '';
};

const onCategoryChange = () => {
    form.subcategory_id = '';
};

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.image = file;

        // Crear vista previa
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

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
            <div class="mx-auto max-w-7xl">
                <h1 class="mb-6 text-3xl font-bold text-center text-blue-600">Crear Nuevo Curso</h1>
                <p class="mb-8 text-center text-gray-600">Completa la información para agregar un nuevo curso</p>

                <div class="rounded-lg bg-white p-6 shadow">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Columna Izquierda: Imagen -->
                            <div>
                                <div class="relative flex h-full min-h-[400px] items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50">
                                    <!-- Vista previa de la imagen -->
                                    <div v-if="imagePreview" class="absolute inset-0">
                                        <img
                                            :src="imagePreview"
                                            alt="Vista previa"
                                            class="h-full w-full rounded-lg object-cover"
                                        />
                                    </div>

                                    <!-- Placeholder cuando no hay imagen -->
                                    <div v-else class="text-center">
                                        <div class="mx-auto mb-4 flex h-32 w-32 items-center justify-center rounded-lg bg-gray-200">
                                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-4xl font-bold text-gray-300 mb-2">IMAGE NOT</p>
                                        <p class="text-4xl font-bold text-gray-300">AVAILABLE</p>
                                    </div>

                                    <!-- Botón de subir imagen -->
                                    <label class="absolute top-4 left-1/2 -translate-x-1/2 cursor-pointer rounded-md bg-blue-500 px-4 py-2 text-sm text-white hover:bg-blue-600">
                                        <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Imagen 1
                                        <input
                                            type="file"
                                            @change="handleImageUpload"
                                            accept="image/*"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                <p class="mt-2 text-center text-xs text-gray-500">Imagen Principal *</p>
                                <p class="text-center text-xs text-gray-400">Formatos: JPG, PNG, WebP (máx. 2MB)</p>
                            </div>

                            <!-- Columna Derecha: Campos -->
                            <div class="space-y-4">
                                <!-- Familia -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Familia</label>
                                    <select
                                        v-model="form.family_id"
                                        @change="onFamilyChange"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    >
                                        <option value="">Seleccione una familia</option>
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
                                    <label class="mb-2 block text-sm font-semibold">Categoría</label>
                                    <select
                                        v-model="form.category_id"
                                        @change="onCategoryChange"
                                        :disabled="!form.family_id"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    >
                                        <option value="">Seleccione una categoría</option>
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
                                    <label class="mb-2 block text-sm font-semibold">Subcategoría</label>
                                    <select
                                        v-model="form.subcategory_id"
                                        :disabled="!form.category_id"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    >
                                        <option value="">Seleccione una subcategoría</option>
                                        <option
                                            v-for="sub in filteredSubcategories"
                                            :key="sub.id"
                                            :value="sub.id"
                                        >
                                            {{ sub.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Precio -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Precio *</label>
                                    <input
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        placeholder="Precio del Curso"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                <!-- Título -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Título *</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        placeholder="Título del Curso"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                <!-- Subtítulo -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Subtítulo *</label>
                                    <input
                                        v-model="form.subtitle"
                                        type="text"
                                        placeholder="Subtítulo del Curso"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Descripción *</label>
                                    <textarea
                                        v-model="form.description"
                                        placeholder="Descripción del Curso"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        rows="6"
                                        required
                                    ></textarea>
                                </div>

                                <!-- Nivel -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold">Nivel *</label>
                                    <select
                                        v-model="form.level_id"
                                        class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Nivel del Curso</option>
                                        <option
                                            v-for="level in props.levels"
                                            :key="level.id"
                                            :value="level.id"
                                        >
                                            {{ level.name }}
                                        </option>
                                    </select>
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
                                Crear Curso
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
            </div>
        </div>
    </AppLayout>
</template>
