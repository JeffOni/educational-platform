<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertCircle,
    Calendar,
    CheckCircle,
    Clock,
    Eye,
    FileText,
    Search,
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
    filters?: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || 'all');

const filteredAssignments = computed(() => {
    let filtered = props.assignments;

    // Filtro de búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (assignment) =>
                assignment.title.toLowerCase().includes(query) ||
                assignment.course.title.toLowerCase().includes(query) ||
                assignment.lesson.name.toLowerCase().includes(query),
        );
    }

    // Filtro de estado
    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter((assignment) => {
            if (selectedStatus.value === 'pending') {
                return assignment.pending_submissions > 0;
            }
            if (selectedStatus.value === 'graded') {
                return (
                    assignment.graded_submissions ===
                        assignment.total_submissions &&
                    assignment.total_submissions > 0
                );
            }
            if (selectedStatus.value === 'overdue') {
                return assignment.is_overdue;
            }
            return true;
        });
    }

    return filtered;
});

const totalAssignments = computed(() => props.assignments.length);
const pendingAssignments = computed(
    () => props.assignments.filter((a) => a.pending_submissions > 0).length,
);
const gradedAssignments = computed(
    () =>
        props.assignments.filter(
            (a) =>
                a.graded_submissions === a.total_submissions &&
                a.total_submissions > 0,
        ).length,
);
const overdueAssignments = computed(
    () => props.assignments.filter((a) => a.is_overdue).length,
);

const getStatusBadge = (assignment: Assignment) => {
    if (assignment.is_overdue && assignment.pending_submissions > 0) {
        return {
            text: 'Vencida',
            variant: 'destructive' as const,
        };
    }
    if (
        assignment.graded_submissions === assignment.total_submissions &&
        assignment.total_submissions > 0
    ) {
        return {
            text: 'Completa',
            variant: 'default' as const,
        };
    }
    if (assignment.pending_submissions > 0) {
        return {
            text: 'Pendiente',
            variant: 'secondary' as const,
        };
    }
    return {
        text: 'Sin entregas',
        variant: 'outline' as const,
    };
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Sin fecha';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tareas', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Tareas" />

        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Gestión de Tareas</h1>
                <p class="mt-1 text-muted-foreground">
                    Vista centralizada de todas las tareas y entregas
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total de Tareas</CardTitle
                        >
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ totalAssignments }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            En todos los cursos
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Pendientes</CardTitle
                        >
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ pendingAssignments }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Con entregas sin calificar
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Calificadas</CardTitle
                        >
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ gradedAssignments }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Todas las entregas evaluadas
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Vencidas</CardTitle
                        >
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ overdueAssignments }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Con fecha límite pasada
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Table -->
            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Buscar por título, curso o lección..."
                                class="pl-10"
                            />
                        </div>
                        <Select v-model="selectedStatus">
                            <SelectTrigger class="w-full sm:w-[200px]">
                                <SelectValue placeholder="Filtrar por estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todas</SelectItem>
                                <SelectItem value="pending"
                                    >Pendientes</SelectItem
                                >
                                <SelectItem value="graded"
                                    >Calificadas</SelectItem
                                >
                                <SelectItem value="overdue"
                                    >Vencidas</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="filteredAssignments.length === 0"
                        class="py-12 text-center text-muted-foreground"
                    >
                        <FileText class="mx-auto mb-4 h-12 w-12 opacity-50" />
                        <p class="mb-2 text-lg font-medium">
                            No se encontraron tareas
                        </p>
                        <p class="text-sm">
                            {{
                                searchQuery || selectedStatus !== 'all'
                                    ? 'Intenta con otros filtros'
                                    : 'Crea tareas desde la gestión de cursos'
                            }}
                        </p>
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tarea</TableHead>
                                <TableHead>Curso / Lección</TableHead>
                                <TableHead>Fecha Límite</TableHead>
                                <TableHead>Puntos</TableHead>
                                <TableHead>Entregas</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right"
                                    >Acciones</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="assignment in filteredAssignments"
                                :key="assignment.id"
                            >
                                <TableCell>
                                    <div>
                                        <div class="font-medium">
                                            {{ assignment.title }}
                                        </div>
                                        <div
                                            v-if="assignment.description"
                                            class="line-clamp-1 text-sm text-muted-foreground"
                                        >
                                            {{ assignment.description }}
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium">
                                            {{ assignment.course.title }}
                                        </div>
                                        <div
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ assignment.section.name }} ›
                                            {{ assignment.lesson.name }}
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <Calendar
                                            class="h-4 w-4 text-muted-foreground"
                                        />
                                        <span
                                            :class="[
                                                'text-sm',
                                                assignment.is_overdue
                                                    ? 'font-medium text-destructive'
                                                    : '',
                                            ]"
                                        >
                                            {{
                                                formatDate(assignment.due_date)
                                            }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">
                                        {{ assignment.max_points }} pts
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm">
                                            Total:
                                            {{ assignment.total_submissions }}
                                        </span>
                                        <div class="flex gap-1">
                                            <Badge
                                                variant="outline"
                                                class="text-xs"
                                            >
                                                ✓
                                                {{
                                                    assignment.graded_submissions
                                                }}
                                            </Badge>
                                            <Badge
                                                v-if="
                                                    assignment.pending_submissions >
                                                    0
                                                "
                                                variant="secondary"
                                                class="text-xs"
                                            >
                                                ⏳
                                                {{
                                                    assignment.pending_submissions
                                                }}
                                            </Badge>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            getStatusBadge(assignment).variant
                                        "
                                    >
                                        {{ getStatusBadge(assignment).text }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="`/admin/assignments/${assignment.id}/submissions`"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
