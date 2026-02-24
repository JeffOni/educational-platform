<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Award, Trash2, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Course {
    id: number;
    title: string;
}

interface Enrollment {
    id: number;
    user: User;
    course: Course;
}

interface Certificate {
    id: number;
    user: User;
    course: Course;
    uploader: User;
    file_path: string;
    created_at: string;
}

interface PaginatedData {
    data: Certificate[];
    links: any[];
    total: number;
    last_page: number;
}

interface Props {
    certificates: PaginatedData;
    eligibleStudents: Enrollment[];
    courses: Course[];
    filters: {
        course_id?: string;
    };
}

const props = defineProps<Props>();

const showUploadForm = ref(false);

const uploadForm = useForm({
    enrollment_id: '',
    certificate_file: null as File | null,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        uploadForm.certificate_file = target.files[0];
    }
};

const submitUpload = () => {
    uploadForm.post('/admin/certificates', {
        onSuccess: () => {
            uploadForm.reset();
            showUploadForm.value = false;
        },
        forceFormData: true,
    });
};

const deleteCertificate = (id: number) => {
    if (confirm('¿Eliminar este certificado?')) {
        router.delete(`/admin/certificates/${id}`);
    }
};

const filterByCourse = (courseId: string) => {
    router.get('/admin/certificates', {
        course_id: courseId || undefined,
    }, { preserveState: true });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Certificados', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gestión de Certificados" />

        <div class="w-full p-4 sm:p-6 lg:p-8">
            <!-- Upload Section -->
            <Card class="mb-6">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="flex items-center gap-2">
                            <Award class="h-5 w-5" />
                            Subir Certificado
                        </CardTitle>
                        <Button @click="showUploadForm = !showUploadForm" variant="outline" size="sm">
                            {{ showUploadForm ? 'Cancelar' : 'Subir Certificado' }}
                        </Button>
                    </div>
                </CardHeader>
                <CardContent v-if="showUploadForm">
                    <div v-if="eligibleStudents.length === 0" class="py-4 text-center text-muted-foreground">
                        No hay estudiantes elegibles para certificación en este momento.
                        <p class="mt-1 text-sm">El estudiante debe haber completado el curso y aprobado el examen.</p>
                    </div>

                    <form v-else @submit.prevent="submitUpload" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Estudiante / Curso *</label>
                            <select
                                v-model="uploadForm.enrollment_id"
                                class="w-full rounded-md border px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                required
                            >
                                <option value="">Seleccione un estudiante</option>
                                <option
                                    v-for="enrollment in eligibleStudents"
                                    :key="enrollment.id"
                                    :value="enrollment.id"
                                >
                                    {{ enrollment.user.name }} - {{ enrollment.course.title }}
                                </option>
                            </select>
                            <p v-if="uploadForm.errors.enrollment_id" class="mt-1 text-sm text-red-600">
                                {{ uploadForm.errors.enrollment_id }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold">Archivo del Certificado (PDF/Imagen) *</label>
                            <input
                                type="file"
                                @change="handleFileChange"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full rounded-md border px-3 py-2"
                                required
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Formatos: PDF, JPG, PNG (máx. 10MB)
                            </p>
                            <p v-if="uploadForm.errors.certificate_file" class="mt-1 text-sm text-red-600">
                                {{ uploadForm.errors.certificate_file }}
                            </p>
                        </div>

                        <Button type="submit" :disabled="uploadForm.processing">
                            <Upload class="mr-2 h-4 w-4" />
                            Subir Certificado
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Certificates List -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Certificados Emitidos</CardTitle>
                        <div class="flex items-center gap-3">
                            <select
                                @change="(e: Event) => filterByCourse((e.target as HTMLSelectElement).value)"
                                class="rounded-md border px-3 py-2 text-sm"
                            >
                                <option value="">Todos los cursos</option>
                                <option
                                    v-for="course in courses"
                                    :key="course.id"
                                    :value="course.id"
                                    :selected="filters.course_id == String(course.id)"
                                >
                                    {{ course.title }}
                                </option>
                            </select>
                            <span class="text-sm text-muted-foreground">
                                Total: {{ certificates.total }}
                            </span>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="certificates.data.length === 0"
                        class="py-12 text-center text-muted-foreground"
                    >
                        <Award class="mx-auto mb-4 h-12 w-12 opacity-40" />
                        <p class="text-lg font-medium">No hay certificados emitidos</p>
                    </div>

                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Estudiante</TableHead>
                                <TableHead>Curso</TableHead>
                                <TableHead>Subido por</TableHead>
                                <TableHead>Fecha</TableHead>
                                <TableHead class="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="cert in certificates.data" :key="cert.id">
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ cert.user.name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ cert.user.email }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>{{ cert.course.title }}</TableCell>
                                <TableCell>{{ cert.uploader.name }}</TableCell>
                                <TableCell>{{ formatDate(cert.created_at) }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <a :href="`/storage/${cert.file_path}`" target="_blank">
                                                Ver
                                            </a>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="deleteCertificate(cert.id)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Paginación -->
                    <div v-if="certificates.last_page > 1" class="mt-4 flex justify-center gap-2">
                        <template v-for="link in certificates.links" :key="link.label">
                            <a
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-md border px-3 py-1 text-sm"
                                :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="rounded-md border px-3 py-1 text-sm text-muted-foreground"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
