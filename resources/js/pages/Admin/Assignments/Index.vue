<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    BookOpen,
    Calendar,
    CheckCircle2,
    Clock,
    FileText,
    Search,
    Users,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Assignment {
    id: number;
    title: string;
    description: string;
    submission_type: string;
    due_date: string | null;
    max_points: number;
    course: {
        id: number;
        title: string;
    };
    lesson: {
        id: number;
        name: string;
    };
    section: {
        id: number;
        name: string;
    };
    total_submissions: number;
    graded_submissions: number;
    pending_submissions: number;
    is_overdue: boolean;
}

interface Props {
    assignments: Assignment[];
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');

const applyFilters = () => {
    router.get(
        '/admin/assignments',
        {
            search: search.value || undefined,
            status:
                statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    router.get('/admin/assignments');
};

const viewSubmissions = (assignmentId: number) => {
    router.visit(`/admin/assignments/${assignmentId}/submissions`);
};

const formatDate = (date: string | null) => {
    if (!date) return 'Sin fecha límite';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getSubmissionTypeLabel = (type: string) => {
    const types: Record<string, string> = {
        file: 'Archivo',
        text: 'Texto',
        link: 'Enlace',
        file_and_text: 'Archivo y Texto',
        forum: 'Foro',
    };
    return types[type] || type;
};

const getStatusBadge = (assignment: Assignment) => {
    if (assignment.total_submissions === 0) {
        return { text: 'Sin entregas', variant: 'secondary' as const };
    }
    if (assignment.pending_submissions === 0) {
        return { text: 'Todas calificadas', variant: 'default' as const };
    }
    if (assignment.is_overdue && assignment.pending_submissions > 0) {
        return { text: 'Vencida', variant: 'destructive' as const };
    }
    return {
        text: `${assignment.pending_submissions} pendientes`,
        variant: 'outline' as const,
    };
};

const totalAssignments = computed(() => props.assignments.length);
const totalPending = computed(() =>
    props.assignments.reduce((sum, a) => sum + a.pending_submissions, 0),
);
const totalGraded = computed(() =>
    props.assignments.reduce((sum, a) => sum + a.graded_submissions, 0),
);
const overdueAssignments = computed(
    () =>
        props.assignments.filter(
            (a) => a.is_overdue && a.pending_submissions > 0,
        ).length,
);
</script>

<template>
    <Head title="Gestión de Tareas" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="w-full sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Gestión de Tareas
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Administra y califica todas las tareas de tus cursos
                    </p>
                </div>

                <!-- Estadísticas -->
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-blue-100 p-3">
                                    <FileText class="h-6 w-6 text-blue-600" />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Total Tareas
                                    </p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ totalAssignments }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-yellow-100 p-3">
                                    <Clock class="h-6 w-6 text-yellow-600" />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Por Calificar
                                    </p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ totalPending }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-green-100 p-3">
                                    <CheckCircle2
                                        class="h-6 w-6 text-green-600"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Calificadas
                                    </p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ totalGraded }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-red-100 p-3">
                                    <AlertCircle class="h-6 w-6 text-red-600" />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600"
                                    >
                                        Vencidas
                                    </p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ overdueAssignments }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Filtros -->
                <Card class="mb-6">
                    <CardContent class="pt-6">
                        <div class="flex flex-col gap-4 md:flex-row">
                            <div class="relative flex-1">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                />
                                <Input
                                    v-model="search"
                                    placeholder="Buscar tareas..."
                                    class="pl-10"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            <Select
                                v-model="statusFilter"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger class="w-full md:w-[200px]">
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="pending"
                                        >Con pendientes</SelectItem
                                    >
                                    <SelectItem value="graded"
                                        >Todas calificadas</SelectItem
                                    >
                                    <SelectItem value="overdue"
                                        >Vencidas</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <Button variant="outline" @click="clearFilters">
                                Limpiar
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Lista de Tareas -->
                <Card>
                    <CardHeader>
                        <CardTitle>Tareas Asignadas</CardTitle>
                        <CardDescription>
                            Lista completa de todas las tareas de tus cursos
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <!-- Empty State -->
                        <div
                            v-if="assignments.length === 0"
                            class="py-12 text-center"
                        >
                            <FileText
                                class="mx-auto mb-4 h-16 w-16 text-gray-300"
                            />
                            <h3
                                class="mb-2 text-lg font-semibold text-gray-900"
                            >
                                No hay tareas
                            </h3>
                            <p class="text-gray-600">
                                {{
                                    filters.search || filters.status
                                        ? 'No se encontraron tareas con los filtros aplicados'
                                        : 'Crea tareas desde el plan de estudios de tus cursos'
                                }}
                            </p>
                        </div>

                        <!-- Tabla -->
                        <div v-else class="space-y-4">
                            <div
                                v-for="assignment in assignments"
                                :key="assignment.id"
                                class="overflow-hidden rounded-lg border transition-all hover:shadow-md"
                            >
                                <div class="p-4">
                                    <div
                                        class="mb-3 flex items-start justify-between gap-4"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="mb-1 text-lg font-semibold text-gray-900"
                                            >
                                                {{ assignment.title }}
                                            </h3>
                                            <p
                                                class="mb-2 line-clamp-2 text-sm text-gray-600"
                                            >
                                                {{ assignment.description }}
                                            </p>
                                            <div
                                                class="flex flex-wrap items-center gap-2"
                                            >
                                                <div
                                                    class="flex items-center gap-1 text-xs text-gray-500"
                                                >
                                                    <BookOpen class="h-3 w-3" />
                                                    {{
                                                        assignment.course.title
                                                    }}
                                                </div>
                                                <span class="text-gray-300"
                                                    >•</span
                                                >
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        assignment.section.name
                                                    }}
                                                </div>
                                                <span class="text-gray-300"
                                                    >•</span
                                                >
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ assignment.lesson.name }}
                                                </div>
                                            </div>
                                        </div>
                                        <Badge
                                            :variant="
                                                getStatusBadge(assignment)
                                                    .variant
                                            "
                                        >
                                            {{
                                                getStatusBadge(assignment).text
                                            }}
                                        </Badge>
                                    </div>

                                    <div
                                        class="mb-3 flex flex-wrap items-center gap-4 text-sm"
                                    >
                                        <div
                                            class="flex items-center gap-1 text-gray-600"
                                        >
                                            <Calendar class="h-4 w-4" />
                                            <span>{{
                                                formatDate(assignment.due_date)
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1 text-gray-600"
                                        >
                                            <Users class="h-4 w-4" />
                                            <span
                                                >{{
                                                    assignment.total_submissions
                                                }}
                                                entregas</span
                                            >
                                        </div>
                                        <Badge variant="outline">
                                            {{
                                                getSubmissionTypeLabel(
                                                    assignment.submission_type,
                                                )
                                            }}
                                        </Badge>
                                        <Badge variant="secondary">
                                            {{ assignment.max_points }} pts
                                        </Badge>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Button
                                            @click="
                                                viewSubmissions(assignment.id)
                                            "
                                            class="flex-1"
                                        >
                                            Ver Entregas
                                            <span
                                                v-if="
                                                    assignment.pending_submissions >
                                                    0
                                                "
                                                class="ml-2 rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-primary"
                                            >
                                                {{
                                                    assignment.pending_submissions
                                                }}
                                            </span>
                                        </Button>
                                        <div
                                            v-if="
                                                assignment.graded_submissions >
                                                0
                                            "
                                            class="text-sm text-gray-600"
                                        >
                                            {{
                                                assignment.graded_submissions
                                            }}/{{
                                                assignment.total_submissions
                                            }}
                                            calificadas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
