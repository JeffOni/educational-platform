<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Textarea } from '@/components/ui/textarea';
import { router, useForm } from '@inertiajs/vue3';
import {
    Award,
    Calendar,
    FilePlus,
    FileText,
    FileUp,
    Link as LinkIcon,
    MessageSquare,
    Pencil,
    Plus,
    Save,
    Trash2,
    Users,
    X,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Assignment {
    id: number;
    title: string;
    description: string;
    due_date: string | null;
    max_points: number;
    submission_type: string;
    allowed_file_types: string[] | null;
    max_file_size: number | null;
    max_files: number | null;
    requires_text: boolean;
    enable_comments: boolean;
}

interface Lesson {
    id: number;
    name: string;
    assignments?: Assignment[];
}

interface Props {
    open: boolean;
    lesson: Lesson | null;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:open', 'refresh']);

const showForm = ref(false);
const editingAssignment = ref<Assignment | null>(null);

const assignmentForm = useForm({
    title: '',
    description: '',
    due_date: '',
    max_points: 100,
    submission_type: 'file' as string,
    allowed_file_types: [] as string[],
    max_file_size: 10240,
    max_files: 5,
    requires_text: false,
    enable_comments: false,
});

// Configuración de tipos de archivo por categoría
const fileCategories = {
    documents: {
        label: 'Documentos',
        extensions: ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'],
    },
    spreadsheets: {
        label: 'Hojas de Cálculo',
        extensions: ['xls', 'xlsx', 'csv', 'ods'],
    },
    presentations: {
        label: 'Presentaciones',
        extensions: ['ppt', 'pptx', 'odp'],
    },
    images: {
        label: 'Imágenes',
        extensions: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
    },
    code: {
        label: 'Código',
        extensions: ['zip', 'rar', '7z', 'py', 'js', 'java', 'cpp', 'php'],
    },
    video: {
        label: 'Video',
        extensions: ['mp4', 'avi', 'mov', 'wmv', 'mkv'],
    },
    audio: {
        label: 'Audio',
        extensions: ['mp3', 'wav', 'ogg', 'flac'],
    },
};

const allowedDomains = [
    { value: 'github.com', label: 'GitHub' },
    { value: 'gitlab.com', label: 'GitLab' },
    { value: 'bitbucket.org', label: 'Bitbucket' },
    { value: 'youtube.com', label: 'YouTube' },
    { value: 'vimeo.com', label: 'Vimeo' },
    { value: 'drive.google.com', label: 'Google Drive' },
    { value: 'dropbox.com', label: 'Dropbox' },
    { value: 'figma.com', label: 'Figma' },
    { value: 'codepen.io', label: 'CodePen' },
];

const assignmentTypes = [
    {
        value: 'file',
        label: 'Archivo(s)',
        description: 'Los estudiantes subirán uno o más archivos',
        icon: FileUp,
    },
    {
        value: 'text',
        label: 'Texto',
        description: 'Respuesta escrita con editor de texto',
        icon: FileText,
    },
    {
        value: 'link',
        label: 'Enlace Externo',
        description: 'URL a repositorio, video, documento, etc.',
        icon: LinkIcon,
    },
    {
        value: 'file_and_text',
        label: 'Archivo y Texto',
        description: 'Combina subida de archivos con descripción',
        icon: FilePlus,
    },
    {
        value: 'forum',
        label: 'Foro/Discusión',
        description: 'Participación en foro con comentarios',
        icon: MessageSquare,
    },
];

const selectedCategories = ref<string[]>([]);

// Watch para actualizar allowed_file_types cuando cambian las categorías
watch(selectedCategories, (newCategories) => {
    const extensions: string[] = [];
    newCategories.forEach((cat) => {
        const category = fileCategories[cat as keyof typeof fileCategories];
        if (category) {
            extensions.push(...category.extensions);
        }
    });
    assignmentForm.allowed_file_types = extensions;
});

// Watch para resetear configuración al cambiar tipo
watch(
    () => assignmentForm.submission_type,
    (newType) => {
        // Resetear configuraciones específicas
        selectedCategories.value = [];
        assignmentForm.allowed_file_types = [];
        assignmentForm.max_file_size = 10240;
        assignmentForm.max_files = 5;
        assignmentForm.requires_text = false;
        assignmentForm.enable_comments = newType === 'forum';
    },
);

const closeModal = () => {
    emit('update:open', false);
    showForm.value = false;
    editingAssignment.value = null;
    assignmentForm.reset();
    selectedCategories.value = [];
};

const startCreating = () => {
    showForm.value = true;
    editingAssignment.value = null;
    assignmentForm.reset();
    selectedCategories.value = [];
};

const editAssignment = (assignment: Assignment) => {
    showForm.value = true;
    editingAssignment.value = assignment;

    assignmentForm.title = assignment.title;
    assignmentForm.description = assignment.description;
    assignmentForm.due_date = assignment.due_date || '';
    assignmentForm.max_points = assignment.max_points;
    assignmentForm.submission_type = assignment.submission_type;
    assignmentForm.allowed_file_types = assignment.allowed_file_types || [];
    assignmentForm.max_file_size = assignment.max_file_size || 10240;
    assignmentForm.max_files = assignment.max_files || 5;
    assignmentForm.requires_text = assignment.requires_text;
    assignmentForm.enable_comments = assignment.enable_comments;

    // Determinar categorías seleccionadas
    if (assignment.allowed_file_types) {
        const cats: string[] = [];
        Object.entries(fileCategories).forEach(([key, cat]) => {
            const hasAnyExtension = cat.extensions.some((ext) =>
                assignment.allowed_file_types?.includes(ext),
            );
            if (hasAnyExtension) cats.push(key);
        });
        selectedCategories.value = cats;
    }
};

const saveAssignment = () => {
    if (!props.lesson) return;

    if (editingAssignment.value) {
        assignmentForm.put(`/admin/assignments/${editingAssignment.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showForm.value = false;
                editingAssignment.value = null;
                assignmentForm.reset();
                selectedCategories.value = [];
                emit('refresh');
            },
        });
    } else {
        assignmentForm.post(`/admin/lessons/${props.lesson.id}/assignments`, {
            preserveScroll: true,
            onSuccess: () => {
                showForm.value = false;
                assignmentForm.reset();
                selectedCategories.value = [];
                emit('refresh');
            },
        });
    }
};

const deleteAssignment = (assignment: Assignment) => {
    router.delete(`/admin/assignments/${assignment.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('refresh');
        },
    });
};

const viewSubmissions = (assignmentId: number) => {
    router.visit(`/admin/assignments/${assignmentId}/submissions`);
};

const getTypeLabel = (type: string) => {
    const typeObj = assignmentTypes.find((t) => t.value === type);
    return typeObj?.label || type;
};

const getTypeIcon = (type: string) => {
    const typeObj = assignmentTypes.find((t) => t.value === type);
    return typeObj?.icon || FileText;
};
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="max-h-[90vh] max-w-4xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>
                    Tareas de la Lección: {{ lesson?.name }}
                </DialogTitle>
                <DialogDescription>
                    Gestiona las tareas y entregas de esta lección
                </DialogDescription>
            </DialogHeader>

            <!-- Vista de Lista -->
            <div v-if="!showForm" class="space-y-4">
                <!-- Lista de Tareas -->
                <div
                    v-if="lesson?.assignments && lesson.assignments.length > 0"
                    class="space-y-3"
                >
                    <div
                        v-for="assignment in lesson.assignments"
                        :key="assignment.id"
                        class="rounded-lg border bg-card p-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center gap-2">
                                    <component
                                        :is="
                                            getTypeIcon(
                                                assignment.submission_type,
                                            )
                                        "
                                        class="h-5 w-5 text-primary"
                                    />
                                    <h4 class="font-semibold">
                                        {{ assignment.title }}
                                    </h4>
                                    <Badge variant="secondary">
                                        {{
                                            getTypeLabel(
                                                assignment.submission_type,
                                            )
                                        }}
                                    </Badge>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ assignment.description }}
                                </p>
                                <div
                                    class="flex flex-wrap gap-3 text-xs text-muted-foreground"
                                >
                                    <span
                                        v-if="assignment.due_date"
                                        class="flex items-center gap-1"
                                    >
                                        <Calendar class="h-3 w-3" />
                                        Vence:
                                        {{
                                            new Date(
                                                assignment.due_date,
                                            ).toLocaleString('es-ES')
                                        }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Award class="h-3 w-3" />
                                        {{ assignment.max_points }} puntos
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="viewSubmissions(assignment.id)"
                                >
                                    <Users class="mr-1 h-4 w-4" />
                                    Ver Entregas
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="editAssignment(assignment)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="ghost">
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle
                                                >¿Eliminar
                                                tarea?</AlertDialogTitle
                                            >
                                            <AlertDialogDescription>
                                                Esta acción eliminará la tarea y
                                                todas sus entregas. No se puede
                                                deshacer.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel
                                                >Cancelar</AlertDialogCancel
                                            >
                                            <AlertDialogAction
                                                @click="
                                                    deleteAssignment(assignment)
                                                "
                                            >
                                                Eliminar
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="rounded-lg border-2 border-dashed p-8 text-center"
                >
                    <MessageSquare
                        class="mx-auto h-12 w-12 text-muted-foreground"
                    />
                    <h3 class="mt-4 font-semibold">Sin tareas aún</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Crea la primera tarea para esta lección
                    </p>
                </div>

                <!-- Botón Nueva Tarea -->
                <Button @click="startCreating" class="w-full">
                    <Plus class="mr-2 h-4 w-4" />
                    Nueva Tarea
                </Button>
            </div>

            <!-- Vista de Formulario -->
            <div v-else class="space-y-6">
                <!-- Información Básica -->
                <div class="space-y-4">
                    <div>
                        <h3 class="mb-3 flex items-center gap-2 font-semibold">
                            📌 Información Básica
                        </h3>
                        <div class="space-y-3">
                            <div class="space-y-2">
                                <Label>Título de la Tarea *</Label>
                                <Input
                                    v-model="assignmentForm.title"
                                    placeholder="Ej: Implementar API REST con Laravel"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Descripción *</Label>
                                <Textarea
                                    v-model="assignmentForm.description"
                                    placeholder="Instrucciones detalladas para completar la tarea..."
                                    rows="4"
                                />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <Label>Fecha Límite</Label>
                                    <Input
                                        type="datetime-local"
                                        v-model="assignmentForm.due_date"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Puntos Máximos *</Label>
                                    <Input
                                        type="number"
                                        v-model="assignmentForm.max_points"
                                        placeholder="100"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Entrega -->
                    <div>
                        <h3 class="mb-3 flex items-center gap-2 font-semibold">
                            📋 Tipo de Entrega *
                        </h3>
                        <RadioGroup
                            v-model="assignmentForm.submission_type"
                            class="space-y-2"
                        >
                            <div
                                v-for="type in assignmentTypes"
                                :key="type.value"
                                class="flex items-start space-x-3 rounded-lg border p-3 hover:bg-accent"
                            >
                                <RadioGroupItem
                                    :value="type.value"
                                    :id="type.value"
                                />
                                <Label
                                    :for="type.value"
                                    class="flex-1 cursor-pointer"
                                >
                                    <div class="flex items-center gap-2">
                                        <component
                                            :is="type.icon"
                                            class="h-4 w-4"
                                        />
                                        <span class="font-medium">{{
                                            type.label
                                        }}</span>
                                    </div>
                                    <p
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        {{ type.description }}
                                    </p>
                                </Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <!-- Configuración Dinámica -->
                    <div>
                        <h3 class="mb-3 flex items-center gap-2 font-semibold">
                            ⚙️ Configuración
                        </h3>

                        <!-- Config: FILE -->
                        <div
                            v-if="
                                assignmentForm.submission_type === 'file' ||
                                assignmentForm.submission_type ===
                                    'file_and_text'
                            "
                            class="space-y-4 rounded-lg border bg-muted/50 p-4"
                        >
                            <div class="space-y-2">
                                <Label>Tipos de Archivo Permitidos</Label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div
                                        v-for="(cat, key) in fileCategories"
                                        :key="key"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :id="`cat-${key}`"
                                            :checked="
                                                selectedCategories.includes(key)
                                            "
                                            @update:checked="
                                                (checked) => {
                                                    if (checked) {
                                                        selectedCategories.push(
                                                            key,
                                                        );
                                                    } else {
                                                        selectedCategories =
                                                            selectedCategories.filter(
                                                                (c) =>
                                                                    c !== key,
                                                            );
                                                    }
                                                }
                                            "
                                        />
                                        <Label
                                            :for="`cat-${key}`"
                                            class="cursor-pointer text-sm font-normal"
                                        >
                                            {{ cat.label }}
                                        </Label>
                                    </div>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Extensiones:
                                    {{
                                        assignmentForm.allowed_file_types
                                            .length > 0
                                            ? assignmentForm.allowed_file_types.join(
                                                  ', ',
                                              )
                                            : 'Ninguna seleccionada'
                                    }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <Label>Máximo de Archivos</Label>
                                    <Input
                                        type="number"
                                        v-model="assignmentForm.max_files"
                                        min="1"
                                        max="10"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Tamaño Máximo (MB)</Label>
                                    <Input
                                        type="number"
                                        v-model="assignmentForm.max_file_size"
                                        min="1"
                                        max="102400"
                                        placeholder="10240"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        En KB (1MB = 1024KB)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Config: TEXT -->
                        <div
                            v-if="
                                assignmentForm.submission_type === 'text' ||
                                assignmentForm.submission_type ===
                                    'file_and_text'
                            "
                            class="space-y-3 rounded-lg border bg-muted/50 p-4"
                        >
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="requires-text"
                                    v-model:checked="
                                        assignmentForm.requires_text
                                    "
                                />
                                <Label
                                    for="requires-text"
                                    class="cursor-pointer"
                                >
                                    Texto obligatorio
                                </Label>
                            </div>
                        </div>

                        <!-- Config: LINK -->
                        <div
                            v-if="assignmentForm.submission_type === 'link'"
                            class="space-y-3 rounded-lg border bg-muted/50 p-4"
                        >
                            <Label>Dominios Permitidos</Label>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div
                                    v-for="domain in allowedDomains.slice(0, 6)"
                                    :key="domain.value"
                                    class="flex items-center gap-2"
                                >
                                    <span class="text-green-600">✓</span>
                                    {{ domain.label }}
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Solo se aceptarán enlaces de estos dominios
                                verificados
                            </p>
                        </div>

                        <!-- Config: FORUM -->
                        <div
                            v-if="assignmentForm.submission_type === 'forum'"
                            class="space-y-3 rounded-lg border bg-muted/50 p-4"
                        >
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="enable-comments"
                                    v-model:checked="
                                        assignmentForm.enable_comments
                                    "
                                />
                                <Label
                                    for="enable-comments"
                                    class="cursor-pointer"
                                >
                                    Permitir comentarios y discusiones
                                </Label>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Los estudiantes podrán responder entre ellos y
                                dar likes
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-2 border-t pt-4">
                    <Button
                        variant="outline"
                        @click="showForm = false"
                        class="flex-1"
                    >
                        <X class="mr-2 h-4 w-4" />
                        Cancelar
                    </Button>
                    <Button
                        @click="saveAssignment"
                        :disabled="
                            !assignmentForm.title || !assignmentForm.description
                        "
                        class="flex-1"
                    >
                        <Save class="mr-2 h-4 w-4" />
                        {{ editingAssignment ? 'Actualizar' : 'Crear' }} Tarea
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
