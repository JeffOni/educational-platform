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
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { router, useForm } from '@inertiajs/vue3';
import {
    ClipboardList,
    Clock,
    Download,
    FileText,
    GripVertical,
    Pencil,
    PlayCircle,
    Plus,
    Save,
    Trash2,
    Upload,
    Users,
    X,
} from 'lucide-vue-next';
import { nextTick, ref } from 'vue';

interface Resource {
    id: number;
    name: string;
    file_path: string;
    file_type: string;
    file_size: number;
}

interface Assignment {
    id: number;
    title: string;
    description: string;
    due_date: string | null;
    max_points: number;
}

interface Lesson {
    id: number;
    name: string;
    video_type: string;
    video_url: string;
    duration: number | null;
    is_preview: boolean;
    resources?: Resource[];
    assignments?: Assignment[];
}

interface Section {
    id: number;
    name: string;
    lessons: Lesson[];
}

interface Props {
    course: {
        id: number;
        sections: Section[];
    };
}

const props = defineProps<Props>();

const addingSection = ref(false);
const editingSection = ref<number | null>(null);
const addingLesson = ref<number | null>(null);
const editingLesson = ref<number | null>(null);
const managingResources = ref<number | null>(null);
const managingAssignments = ref<number | null>(null);
const editingAssignment = ref<number | null>(null);

const sectionForm = useForm({
    name: '',
});

const lessonForm = useForm({
    name: '',
    video_type: 'youtube',
    video_url: '',
    duration: '' as string | number | null,
    is_preview: false,
});

// Función para convertir MM:SS a segundos
const durationToSeconds = (duration: string | number | null): number | null => {
    if (!duration) return null;
    if (typeof duration === 'number') return duration;

    const parts = duration.toString().split(':');
    if (parts.length !== 2) return null;

    const minutes = parseInt(parts[0]) || 0;
    const seconds = parseInt(parts[1]) || 0;
    return minutes * 60 + seconds;
};

// Función para convertir segundos a MM:SS
const secondsToDuration = (seconds: number | null): string => {
    if (!seconds) return '';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

const resourceForm = useForm({
    name: '',
    file: null as File | null,
});

const assignmentForm = useForm({
    title: '',
    description: '',
    due_date: '',
    max_points: 100,
});

// Section Methods
const startAddingSection = () => {
    addingSection.value = true;
    sectionForm.reset();
};

const saveSection = () => {
    sectionForm.post(`/admin/courses/${props.course.id}/sections`, {
        preserveScroll: true,
        onSuccess: () => {
            addingSection.value = false;
            sectionForm.reset();
            router.reload({ only: ['course'] });
        },
    });
};

const editSection = (section: Section) => {
    editingSection.value = section.id;
    sectionForm.name = section.name;
};

const updateSection = (section: Section) => {
    sectionForm.put(`/admin/sections/${section.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingSection.value = null;
            sectionForm.reset();
            router.reload({ only: ['course'] });
        },
    });
};

const deleteSection = (section: Section) => {
    router.delete(`/admin/sections/${section.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['course'] });
        },
    });
};

// Lesson Methods
const startAddingLesson = (sectionId: number) => {
    addingLesson.value = sectionId;
    lessonForm.reset();
};

const saveLesson = (sectionId: number) => {
    // Convertir duración a segundos
    const durationInSeconds = durationToSeconds(lessonForm.duration);

    console.log('Guardando lección:', {
        sectionId,
        name: lessonForm.name,
        video_type: lessonForm.video_type,
        video_url: lessonForm.video_url,
        duration: durationInSeconds,
        is_preview: lessonForm.is_preview ? 1 : 0,
    });

    router.post(
        `/admin/sections/${sectionId}/lessons`,
        {
            name: lessonForm.name,
            video_type: lessonForm.video_type,
            video_url: lessonForm.video_url,
            duration: durationInSeconds,
            is_preview: lessonForm.is_preview ? 1 : 0,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log('✓ Lección guardada exitosamente');
                addingLesson.value = null;
                lessonForm.reset();
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                console.error('✗ Errores:', errors);
            },
        },
    );
};

const editLesson = (lesson: Lesson) => {
    editingLesson.value = lesson.id;
    lessonForm.name = lesson.name;
    lessonForm.video_type = lesson.video_type;
    lessonForm.video_url = lesson.video_url;
    lessonForm.duration = secondsToDuration(lesson.duration);
    lessonForm.is_preview = lesson.is_preview;
};

const updateLesson = (lesson: Lesson) => {
    router.put(
        `/admin/lessons/${lesson.id}`,
        {
            name: lessonForm.name,
            video_type: lessonForm.video_type,
            video_url: lessonForm.video_url,
            duration: durationToSeconds(lessonForm.duration),
            is_preview: lessonForm.is_preview ? 1 : 0,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                editingLesson.value = null;
                lessonForm.reset();
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                console.error('✗ Errores al actualizar:', errors);
            },
        },
    );
};

const deleteLesson = (lesson: Lesson) => {
    router.delete(`/admin/lessons/${lesson.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['course'] });
        },
    });
};

// Resource Methods
const toggleResourcesPanel = (lessonId: number) => {
    managingResources.value =
        managingResources.value === lessonId ? null : lessonId;
    resourceForm.reset();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        resourceForm.file = target.files[0];
    }
};

const saveResource = (lessonId: number) => {
    if (!resourceForm.file) return;

    resourceForm.post(`/admin/lessons/${lessonId}/resources`, {
        onSuccess: () => {
            resourceForm.reset();
        },
    });
};

const deleteResource = (resource: Resource) => {
    useForm({}).delete(`/admin/resources/${resource.id}`);
};

// Assignment Methods
const toggleAssignmentsPanel = (lessonId: number) => {
    const isCurrentlyOpen = managingAssignments.value === lessonId;
    
    if (isCurrentlyOpen) {
        managingAssignments.value = null;
        editingAssignment.value = null;
    } else {
        managingAssignments.value = lessonId;
        managingResources.value = null; // Cerrar recursos
        editingAssignment.value = null;
    }

    assignmentForm.reset();
};

const saveAssignment = (lessonId: number) => {
    assignmentForm.post(`/admin/lessons/${lessonId}/assignments`, {
        onSuccess: () => {
            assignmentForm.reset();
        },
    });
};

const editAssignment = (assignment: Assignment) => {
    editingAssignment.value = assignment.id;
    assignmentForm.title = assignment.title;
    assignmentForm.description = assignment.description;
    assignmentForm.due_date = assignment.due_date || '';
    assignmentForm.max_points = assignment.max_points;
};

const updateAssignment = () => {
    if (!editingAssignment.value) return;

    assignmentForm.put(`/admin/assignments/${editingAssignment.value}`, {
        onSuccess: () => {
            editingAssignment.value = null;
            assignmentForm.reset();
        },
    });
};

const deleteAssignment = (assignment: Assignment) => {
    useForm({}).delete(`/admin/assignments/${assignment.id}`);
};

const cancelAssignmentEdit = () => {
    editingAssignment.value = null;
    assignmentForm.reset();
};

const viewSubmissions = (assignmentId: number) => {
    router.visit(`/admin/assignments/${assignmentId}/submissions`);
};

const cancelEdit = () => {
    addingSection.value = false;
    editingSection.value = null;
    addingLesson.value = null;
    editingLesson.value = null;
    managingResources.value = null;
    managingAssignments.value = null;
    editingAssignment.value = null;
    sectionForm.reset();
    lessonForm.reset();
    resourceForm.reset();
    assignmentForm.reset();
};

const formatDuration = (seconds: number | null) => {
    if (!seconds) return 'Sin duración';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / 1048576).toFixed(2) + ' MB';
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Plan de Estudios</CardTitle>
            <CardDescription>
                Organiza el contenido de tu curso en secciones y lecciones
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Sections List -->
            <div class="space-y-4">
                <Card v-for="section in course.sections" :key="section.id">
                    <CardHeader class="pb-3">
                        <!-- Section Header -->
                        <div
                            v-if="editingSection === section.id"
                            class="flex gap-2"
                        >
                            <Input
                                v-model="sectionForm.name"
                                placeholder="Nombre de la sección"
                                class="flex-1"
                            />
                            <Button size="sm" @click="updateSection(section)">
                                <Save class="mr-2 h-4 w-4" />
                                Guardar
                            </Button>
                            <Button
                                size="sm"
                                variant="outline"
                                @click="cancelEdit"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                        <div v-else class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <GripVertical
                                    class="h-5 w-5 cursor-move text-muted-foreground"
                                />
                                <div>
                                    <CardTitle class="text-lg">{{
                                        section.name
                                    }}</CardTitle>
                                    <CardDescription class="text-sm">
                                        {{ section.lessons?.length || 0 }}
                                        lecciones
                                    </CardDescription>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="editSection(section)"
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
                                                sección?</AlertDialogTitle
                                            >
                                            <AlertDialogDescription>
                                                Esta acción eliminará la sección
                                                y todas sus lecciones. Esta
                                                acción no se puede deshacer.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel
                                                >Cancelar</AlertDialogCancel
                                            >
                                            <AlertDialogAction
                                                @click="deleteSection(section)"
                                            >
                                                Eliminar
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <!-- Lessons List -->
                        <div
                            v-for="lesson in section.lessons"
                            :key="lesson.id"
                            class="rounded-lg border p-3"
                        >
                            <!-- Lesson Edit Form -->
                            <div
                                v-if="editingLesson === lesson.id"
                                class="space-y-4"
                            >
                                <div class="space-y-2">
                                    <Label>Nombre de la lección</Label>
                                    <Input v-model="lessonForm.name" />
                                    <div
                                        v-if="lessonForm.errors.name"
                                        class="text-sm text-destructive"
                                    >
                                        {{ lessonForm.errors.name }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Tipo de video</Label>
                                        <Select v-model="lessonForm.video_type">
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="youtube"
                                                    >YouTube</SelectItem
                                                >
                                                <SelectItem value="vimeo"
                                                    >Vimeo</SelectItem
                                                >
                                                <SelectItem value="file"
                                                    >Archivo Local</SelectItem
                                                >
                                                <SelectItem value="spaces"
                                                    >DigitalOcean
                                                    Spaces</SelectItem
                                                >
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Duración (MM:SS)</Label>
                                        <Input
                                            v-model="lessonForm.duration"
                                            type="text"
                                            placeholder="05:30"
                                        />
                                        <p class="text-xs text-gray-500">
                                            Ejemplo: 05:30 para 5 minutos y 30
                                            segundos
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label>URL del Video</Label>
                                    <Input
                                        v-model="lessonForm.video_url"
                                        placeholder="https://youtube.com/watch?v=..."
                                    />
                                    <div
                                        v-if="lessonForm.errors.video_url"
                                        class="text-sm text-destructive"
                                    >
                                        {{ lessonForm.errors.video_url }}
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        :checked="lessonForm.is_preview"
                                        @update:checked="
                                            lessonForm.is_preview = $event
                                        "
                                    />
                                    <Label
                                        class="cursor-pointer text-sm font-normal"
                                    >
                                        Vista previa gratuita (usuarios no
                                        matriculados pueden verla)
                                    </Label>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="cancelEdit"
                                    >
                                        Cancelar
                                    </Button>
                                    <Button
                                        size="sm"
                                        @click="updateLesson(lesson)"
                                    >
                                        <Save class="mr-2 h-4 w-4" />
                                        Actualizar
                                    </Button>
                                </div>
                            </div>

                            <!-- Lesson Display -->
                            <div v-else>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <PlayCircle
                                            class="h-5 w-5 text-muted-foreground"
                                        />
                                        <div>
                                            <div class="font-medium">
                                                {{ lesson.name }}
                                            </div>
                                            <div
                                                class="flex items-center gap-2 text-sm text-muted-foreground"
                                            >
                                                <Clock class="h-3 w-3" />
                                                {{
                                                    formatDuration(
                                                        lesson.duration,
                                                    )
                                                }}
                                                <Badge
                                                    v-if="lesson.is_preview"
                                                    variant="secondary"
                                                    class="ml-2"
                                                >
                                                    Gratis
                                                </Badge>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="
                                                toggleResourcesPanel(lesson.id)
                                            "
                                            :class="{
                                                'bg-accent':
                                                    managingResources ===
                                                    lesson.id,
                                            }"
                                        >
                                            <FileText class="mr-1 h-4 w-4" />
                                            Recursos ({{
                                                lesson.resources?.length || 0
                                            }})
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="
                                                toggleAssignmentsPanel(
                                                    lesson.id,
                                                )
                                            "
                                            :class="{
                                                'bg-accent':
                                                    managingAssignments ===
                                                    lesson.id,
                                            }"
                                        >
                                            <ClipboardList
                                                class="mr-1 h-4 w-4"
                                            />
                                            Tareas ({{
                                                lesson.assignments?.length || 0
                                            }})
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            @click="editLesson(lesson)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <AlertDialog>
                                            <AlertDialogTrigger as-child>
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                >
                                                    <Trash2
                                                        class="h-4 w-4 text-destructive"
                                                    />
                                                </Button>
                                            </AlertDialogTrigger>
                                            <AlertDialogContent>
                                                <AlertDialogHeader>
                                                    <AlertDialogTitle
                                                        >¿Eliminar
                                                        lección?</AlertDialogTitle
                                                    >
                                                    <AlertDialogDescription>
                                                        Esta acción no se puede
                                                        deshacer.
                                                    </AlertDialogDescription>
                                                </AlertDialogHeader>
                                                <AlertDialogFooter>
                                                    <AlertDialogCancel
                                                        >Cancelar</AlertDialogCancel
                                                    >
                                                    <AlertDialogAction
                                                        @click="
                                                            deleteLesson(lesson)
                                                        "
                                                    >
                                                        Eliminar
                                                    </AlertDialogAction>
                                                </AlertDialogFooter>
                                            </AlertDialogContent>
                                        </AlertDialog>
                                    </div>
                                </div>

                                <!-- Resources Panel -->
                                <div
                                    v-if="managingResources === lesson.id"
                                    class="mt-4 space-y-4 rounded-lg bg-muted/50 p-4"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <h4 class="font-medium">
                                            Recursos de la Lección
                                        </h4>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            @click="
                                                toggleResourcesPanel(lesson.id)
                                            "
                                        >
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <!-- Resources List -->
                                    <div
                                        v-if="
                                            lesson.resources &&
                                            lesson.resources.length > 0
                                        "
                                        class="space-y-2"
                                    >
                                        <div
                                            v-for="resource in lesson.resources"
                                            :key="resource.id"
                                            class="flex items-center justify-between rounded border bg-background p-3"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <Download
                                                    class="h-4 w-4 text-muted-foreground"
                                                />
                                                <div>
                                                    <div
                                                        class="text-sm font-medium"
                                                    >
                                                        {{ resource.name }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-muted-foreground"
                                                    >
                                                        {{ resource.file_type }}
                                                        •
                                                        {{
                                                            formatFileSize(
                                                                resource.file_size,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <AlertDialog>
                                                <AlertDialogTrigger as-child>
                                                    <Button
                                                        size="sm"
                                                        variant="ghost"
                                                    >
                                                        <Trash2
                                                            class="h-4 w-4 text-destructive"
                                                        />
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle
                                                            >¿Eliminar
                                                            recurso?</AlertDialogTitle
                                                        >
                                                        <AlertDialogDescription>
                                                            Se eliminará el
                                                            archivo del
                                                            servidor.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel
                                                            >Cancelar</AlertDialogCancel
                                                        >
                                                        <AlertDialogAction
                                                            @click="
                                                                deleteResource(
                                                                    resource,
                                                                )
                                                            "
                                                        >
                                                            Eliminar
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </div>

                                    <!-- Add Resource Form -->
                                    <div
                                        class="space-y-3 rounded border-2 border-dashed p-3"
                                    >
                                        <div class="space-y-2">
                                            <Label>Nombre del Recurso</Label>
                                            <Input
                                                v-model="resourceForm.name"
                                                placeholder="Ej: Material complementario"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <Label>Archivo (máx. 10MB)</Label>
                                            <Input
                                                type="file"
                                                @change="handleFileChange"
                                                accept=".pdf,.doc,.docx,.zip,.rar,.ppt,.pptx"
                                            />
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                Formatos: PDF, DOC, DOCX, ZIP,
                                                RAR, PPT, PPTX
                                            </p>
                                        </div>
                                        <Button
                                            size="sm"
                                            @click="saveResource(lesson.id)"
                                            :disabled="
                                                !resourceForm.file ||
                                                !resourceForm.name
                                            "
                                        >
                                            <Upload class="mr-2 h-4 w-4" />
                                            Subir Recurso
                                        </Button>
                                    </div>
                                </div>

                                <!-- Assignments Panel -->
                                <div
                                    v-if="managingAssignments === lesson.id"
                                    class="mt-4 space-y-4 rounded-lg bg-muted/50 p-4"
                                    :key="`assignments-${lesson.id}`"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <h4 class="font-medium">
                                            Tareas de la Lección
                                        </h4>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            @click="toggleAssignmentsPanel(lesson.id)"
                                        >
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <!-- Assignments List -->
                                    <div
                                        v-if="
                                            lesson.assignments &&
                                            lesson.assignments.length > 0
                                        "
                                        class="space-y-3"
                                    >
                                        <div
                                            v-for="assignment in lesson.assignments"
                                            :key="assignment.id"
                                            class="rounded border bg-background p-3"
                                        >
                                                <div
                                                    class="flex items-start justify-between"
                                                >
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium"
                                                        >
                                                            {{
                                                                assignment.title
                                                            }}
                                                        </div>
                                                        <p
                                                            class="mt-1 text-sm text-muted-foreground"
                                                        >
                                                            {{
                                                                assignment.description
                                                            }}
                                                        </p>
                                                        <div
                                                            class="mt-2 flex gap-4 text-xs text-muted-foreground"
                                                        >
                                                            <span
                                                                v-if="
                                                                    assignment.due_date
                                                                "
                                                            >
                                                                Vence:
                                                                {{
                                                                    new Date(
                                                                        assignment.due_date,
                                                                    ).toLocaleString(
                                                                        'es-ES',
                                                                    )
                                                                }}
                                                            </span>
                                                            <span
                                                                >Puntos:
                                                                {{
                                                                    assignment.max_points
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                            @click="
                                                                viewSubmissions(
                                                                    assignment.id,
                                                                )
                                                            "
                                                        >
                                                            <Users
                                                                class="mr-1 h-4 w-4"
                                                            />
                                                            Ver Entregas
                                                        </Button>
                                                        <Button
                                                            size="sm"
                                                            variant="ghost"
                                                            @click="
                                                                editAssignment(
                                                                    assignment,
                                                                )
                                                            "
                                                        >
                                                            <Pencil
                                                                class="h-4 w-4"
                                                            />
                                                        </Button>
                                                        <Button
                                                            size="sm"
                                                            variant="ghost"
                                                            @click="
                                                                deleteAssignment(
                                                                    assignment,
                                                                )
                                                            "
                                                        >
                                                            <Trash2
                                                                class="h-4 w-4 text-destructive"
                                                            />
                                                        </Button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>

                                    <!-- Add Assignment Form -->
                                    <div
                                        class="space-y-3 rounded border-2 border-dashed p-3"
                                    >
                                        <div class="space-y-2">
                                            <Label>Título de la Tarea</Label>
                                            <Input
                                                v-model="assignmentForm.title"
                                                placeholder="Ej: Cuestionario sobre el tema"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <Label>Descripción</Label>
                                            <Textarea
                                                v-model="
                                                    assignmentForm.description
                                                "
                                                placeholder="Instrucciones para completar la tarea..."
                                                rows="3"
                                            />
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="space-y-2">
                                                <Label
                                                    >Fecha límite
                                                    (opcional)</Label
                                                >
                                                <Input
                                                    type="datetime-local"
                                                    v-model="
                                                        assignmentForm.due_date
                                                    "
                                                />
                                            </div>
                                            <div class="space-y-2">
                                                <Label>Puntos máximos</Label>
                                                <Input
                                                    type="number"
                                                    v-model="
                                                        assignmentForm.max_points
                                                    "
                                                    placeholder="100"
                                                />
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <Button
                                                v-if="editingAssignment"
                                                size="sm"
                                                variant="outline"
                                                @click="cancelAssignmentEdit"
                                            >
                                                Cancelar
                                            </Button>
                                            <Button
                                                size="sm"
                                                @click="
                                                    editingAssignment
                                                        ? updateAssignment()
                                                        : saveAssignment(
                                                              lesson.id,
                                                          )
                                                "
                                                :disabled="
                                                    !assignmentForm.title ||
                                                    !assignmentForm.description
                                                "
                                            >
                                                <Save
                                                    v-if="editingAssignment"
                                                    class="mr-2 h-4 w-4"
                                                />
                                                <Plus
                                                    v-else
                                                    class="mr-2 h-4 w-4"
                                                />
                                                {{
                                                    editingAssignment
                                                        ? 'Actualizar Tarea'
                                                        : 'Crear Tarea'
                                                }}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Lesson Form -->
                        <div
                            v-if="addingLesson === section.id"
                            class="space-y-4 rounded-lg border border-dashed p-4"
                        >
                            <div class="space-y-2">
                                <Label>Nombre de la lección</Label>
                                <Input
                                    v-model="lessonForm.name"
                                    placeholder="Ej: Introducción al tema"
                                />
                                <div
                                    v-if="lessonForm.errors.name"
                                    class="text-sm text-destructive"
                                >
                                    {{ lessonForm.errors.name }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Tipo de video</Label>
                                    <Select v-model="lessonForm.video_type">
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="youtube"
                                                >YouTube</SelectItem
                                            >
                                            <SelectItem value="vimeo"
                                                >Vimeo</SelectItem
                                            >
                                            <SelectItem value="file"
                                                >Archivo Local</SelectItem
                                            >
                                            <SelectItem value="spaces"
                                                >DigitalOcean Spaces</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div class="space-y-2">
                                    <Label>Duración (MM:SS)</Label>
                                    <Input
                                        v-model="lessonForm.duration"
                                        type="text"
                                        placeholder="05:30"
                                    />
                                    <p class="text-xs text-gray-500">
                                        Ejemplo: 05:30 para 5 minutos y 30
                                        segundos
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>URL del Video</Label>
                                <Input
                                    v-model="lessonForm.video_url"
                                    placeholder="https://youtube.com/watch?v=..."
                                />
                                <p class="text-xs text-muted-foreground">
                                    Para YouTube/Vimeo, pega la URL completa
                                </p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    :checked="lessonForm.is_preview"
                                    @update:checked="
                                        lessonForm.is_preview = $event
                                    "
                                />
                                <Label
                                    class="cursor-pointer text-sm font-normal"
                                >
                                    Vista previa gratuita (usuarios no
                                    matriculados pueden verla)
                                </Label>
                            </div>

                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    type="button"
                                    @click="cancelEdit"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    size="sm"
                                    type="button"
                                    @click="saveLesson(section.id)"
                                >
                                    <Plus class="mr-2 h-4 w-4" />
                                    Agregar Lección
                                </Button>
                            </div>
                        </div>

                        <!-- Add Lesson Button -->
                        <Button
                            v-else
                            variant="ghost"
                            size="sm"
                            class="w-full"
                            @click="startAddingLesson(section.id)"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Agregar Lección
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Add Section Form -->
            <div
                v-if="addingSection"
                class="rounded-lg border border-dashed p-4"
            >
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>Nombre de la Nueva Sección</Label>
                        <Input
                            v-model="sectionForm.name"
                            placeholder="Ej: Introducción al curso"
                        />
                        <div
                            v-if="sectionForm.errors.name"
                            class="text-sm text-destructive"
                        >
                            {{ sectionForm.errors.name }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button @click="saveSection">
                            <Save class="mr-2 h-4 w-4" />
                            Guardar Sección
                        </Button>
                        <Button variant="outline" @click="cancelEdit">
                            Cancelar
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Add Section Button -->
            <Button
                v-else
                variant="outline"
                class="w-full border-dashed"
                @click="startAddingSection"
            >
                <Plus class="mr-2 h-4 w-4" />
                Agregar Nueva Sección
            </Button>
        </CardContent>
    </Card>
</template>
