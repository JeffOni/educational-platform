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
import { Head, Link, router } from '@inertiajs/vue3';
import { Copy, Plus, Trash2, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Course {
    id: number;
    title: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface EnrollmentCode {
    id: number;
    code: string;
    course: Course;
    creator: User;
    used_by: User | null;
    used_at: string | null;
    expires_at: string | null;
    is_active: boolean;
    created_at: string;
}

interface PaginatedData {
    data: EnrollmentCode[];
    links: any[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    codes: PaginatedData;
    courses: Course[];
    filters: {
        course_id?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const filterCourse = ref(props.filters.course_id || '');
const filterStatus = ref(props.filters.status || '');

const applyFilters = () => {
    router.get('/admin/enrollment-codes', {
        course_id: filterCourse.value || undefined,
        status: filterStatus.value || undefined,
    }, { preserveState: true });
};

const clearFilters = () => {
    filterCourse.value = '';
    filterStatus.value = '';
    router.get('/admin/enrollment-codes');
};

const copyCode = (code: string) => {
    navigator.clipboard.writeText(code);
};

const deactivateCode = (codeId: number) => {
    if (confirm('¿Desactivar este código?')) {
        router.post(`/admin/enrollment-codes/${codeId}/deactivate`);
    }
};

const deleteCode = (codeId: number) => {
    if (confirm('¿Eliminar este código permanentemente?')) {
        router.delete(`/admin/enrollment-codes/${codeId}`);
    }
};

const getStatusBadge = (code: EnrollmentCode) => {
    if (code.used_at) return { label: 'Usado', variant: 'default' as const };
    if (!code.is_active) return { label: 'Desactivado', variant: 'secondary' as const };
    if (code.expires_at && new Date(code.expires_at) < new Date()) return { label: 'Expirado', variant: 'destructive' as const };
    return { label: 'Disponible', variant: 'outline' as const };
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Códigos de Inscripción', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Códigos de Inscripción" />

        <div class="w-full p-4 sm:p-6 lg:p-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Códigos de Inscripción</CardTitle>
                        <div class="flex gap-2">
                            <Button as-child>
                                <Link href="/admin/enrollment-codes/create">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Generar Códigos
                                </Link>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Filtros -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <select
                            v-model="filterCourse"
                            @change="applyFilters"
                            class="rounded-md border px-3 py-2 text-sm"
                        >
                            <option value="">Todos los cursos</option>
                            <option
                                v-for="course in courses"
                                :key="course.id"
                                :value="course.id"
                            >
                                {{ course.title }}
                            </option>
                        </select>

                        <select
                            v-model="filterStatus"
                            @change="applyFilters"
                            class="rounded-md border px-3 py-2 text-sm"
                        >
                            <option value="">Todos los estados</option>
                            <option value="available">Disponibles</option>
                            <option value="used">Usados</option>
                            <option value="expired">Expirados</option>
                        </select>

                        <Button
                            v-if="filterCourse || filterStatus"
                            variant="ghost"
                            size="sm"
                            @click="clearFilters"
                        >
                            Limpiar filtros
                        </Button>

                        <div class="ml-auto text-sm text-muted-foreground">
                            Total: {{ codes.total }} códigos
                        </div>
                    </div>

                    <div
                        v-if="codes.data.length === 0"
                        class="py-12 text-center text-muted-foreground"
                    >
                        <p class="mb-2 text-lg font-medium">No hay códigos generados</p>
                        <p class="text-sm">Genera códigos para que los estudiantes puedan inscribirse</p>
                    </div>

                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Código</TableHead>
                                <TableHead>Curso</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead>Usado por</TableHead>
                                <TableHead>Creado</TableHead>
                                <TableHead>Expira</TableHead>
                                <TableHead class="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="code in codes.data" :key="code.id">
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <code class="rounded bg-muted px-2 py-1 font-mono text-sm font-bold">
                                            {{ code.code }}
                                        </code>
                                        <button
                                            @click="copyCode(code.code)"
                                            class="text-muted-foreground hover:text-foreground"
                                            title="Copiar código"
                                        >
                                            <Copy class="h-4 w-4" />
                                        </button>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ code.course?.title }}</span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadge(code).variant">
                                        {{ getStatusBadge(code).label }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div v-if="code.used_by" class="text-sm">
                                        <div class="font-medium">{{ code.used_by.name }}</div>
                                        <div class="text-muted-foreground">{{ formatDate(code.used_at) }}</div>
                                    </div>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDate(code.created_at) }}
                                </TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDate(code.expires_at) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <div v-if="!code.used_at" class="flex justify-end gap-2">
                                        <Button
                                            v-if="code.is_active"
                                            variant="outline"
                                            size="sm"
                                            @click="deactivateCode(code.id)"
                                            title="Desactivar"
                                        >
                                            <XCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="deleteCode(code.id)"
                                            title="Eliminar"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Paginación -->
                    <div v-if="codes.last_page > 1" class="mt-4 flex justify-center gap-2">
                        <template v-for="link in codes.links" :key="link.label">
                            <Link
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
