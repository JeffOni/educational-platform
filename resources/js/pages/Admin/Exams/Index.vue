<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    BookOpen,
    CheckCircle,
    ClipboardList,
    Clock,
    FileText,
    Hash,
    Pencil,
    Plus,
    Save,
    Target,
    Trash2,
    X,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface ExamQuestion {
    id: number;
    question_text: string;
    question_type: 'multiple_choice' | 'true_false';
    options: string[] | null;
    correct_answer: string;
    points: number;
}

interface Exam {
    id: number;
    title: string;
    description: string | null;
    passing_score: number;
    time_limit: number | null;
    max_attempts: number;
    is_active: boolean;
    questions: ExamQuestion[];
}

interface Course {
    id: number;
    title: string;
}

interface Props {
    course: Course;
    exam: Exam | null;
}

const props = defineProps<Props>();

// Formulario para crear/editar examen
const examForm = useForm({
    title: props.exam?.title || '',
    description: props.exam?.description || '',
    passing_score: props.exam?.passing_score || 70,
    time_limit: props.exam?.time_limit || (null as number | null),
    max_attempts: props.exam?.max_attempts || 1,
    is_active: props.exam?.is_active ?? true,
});

const editingExam = ref(false);

// Formulario para nueva pregunta
const questionType = ref<'multiple_choice' | 'true_false'>('multiple_choice');
const questionText = ref('');
const questionPoints = ref(1);
const questionOptions = ref<string[]>(['', '']);
const correctAnswer = ref('');
const showQuestionForm = ref(false);
const questionProcessing = ref(false);

watch(questionType, (newType) => {
    if (newType === 'true_false') {
        questionOptions.value = ['Verdadero', 'Falso'];
        correctAnswer.value = '';
    } else {
        questionOptions.value = ['', ''];
        correctAnswer.value = '';
    }
});

const addOption = () => {
    questionOptions.value.push('');
};

const removeOption = (index: number) => {
    if (questionOptions.value.length > 2) {
        const removedOption = questionOptions.value[index];
        questionOptions.value.splice(index, 1);
        if (correctAnswer.value === removedOption) {
            correctAnswer.value = '';
        }
    }
};

const submitExam = () => {
    if (props.exam) {
        examForm.put(route('admin.exams.update', props.exam.id), {
            preserveScroll: true,
            onSuccess: () => {
                editingExam.value = false;
            },
        });
    } else {
        examForm.post(route('admin.courses.exam.store', props.course.id), {
            preserveScroll: true,
        });
    }
};

const submitQuestion = () => {
    if (!props.exam) return;

    questionProcessing.value = true;

    const data = {
        question_text: questionText.value,
        question_type: questionType.value,
        options: questionType.value === 'multiple_choice' ? questionOptions.value.filter((o) => o.trim() !== '') : null,
        correct_answer: correctAnswer.value,
        points: questionPoints.value,
    };

    router.post(route('admin.exams.questions.store', props.exam.id), data, {
        preserveScroll: true,
        onSuccess: () => {
            questionText.value = '';
            questionPoints.value = 1;
            questionType.value = 'multiple_choice';
            questionOptions.value = ['', ''];
            correctAnswer.value = '';
            showQuestionForm.value = false;
            questionProcessing.value = false;
        },
        onError: () => {
            questionProcessing.value = false;
        },
    });
};

const deleteQuestion = (questionId: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
        router.delete(route('admin.exam-questions.destroy', questionId), {
            preserveScroll: true,
        });
    }
};

const deleteExam = () => {
    if (!props.exam) return;
    if (confirm('¿Estás seguro de que deseas eliminar este examen y todas sus preguntas?')) {
        router.delete(route('admin.exams.destroy', props.exam.id), {
            preserveScroll: true,
        });
    }
};

const getQuestionTypeBadge = (type: string) => {
    return type === 'multiple_choice' ? 'Opción Múltiple' : 'Verdadero/Falso';
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos', href: '/admin/courses' },
    { title: props.course.title, href: `/admin/courses/${props.course.id}/edit` },
    { title: 'Examen', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Examen - ${course.title}`" />

        <div class="w-full space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Examen del Curso</h1>
                    <p class="mt-1 text-muted-foreground">{{ course.title }}</p>
                </div>
                <Badge v-if="exam" :variant="exam.is_active ? 'default' : 'secondary'">
                    {{ exam.is_active ? 'Activo' : 'Inactivo' }}
                </Badge>
            </div>

            <!-- Crear Examen (si no existe) -->
            <Card v-if="!exam">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <ClipboardList class="h-5 w-5" />
                        Crear Examen
                    </CardTitle>
                    <CardDescription>
                        Este curso aún no tiene un examen. Configura uno a continuación.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitExam" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Título -->
                            <div class="space-y-2 md:col-span-2">
                                <Label for="title">Título del Examen *</Label>
                                <Input
                                    id="title"
                                    v-model="examForm.title"
                                    placeholder="Ej: Examen Final del Curso"
                                    required
                                />
                                <p v-if="examForm.errors.title" class="text-sm text-destructive">
                                    {{ examForm.errors.title }}
                                </p>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-2 md:col-span-2">
                                <Label for="description">Descripción</Label>
                                <textarea
                                    id="description"
                                    v-model="examForm.description"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    placeholder="Descripción o instrucciones del examen..."
                                    rows="3"
                                ></textarea>
                                <p v-if="examForm.errors.description" class="text-sm text-destructive">
                                    {{ examForm.errors.description }}
                                </p>
                            </div>

                            <!-- Puntaje de aprobación -->
                            <div class="space-y-2">
                                <Label for="passing_score">Puntaje de Aprobación (%) *</Label>
                                <Input
                                    id="passing_score"
                                    v-model.number="examForm.passing_score"
                                    type="number"
                                    min="1"
                                    max="100"
                                    required
                                />
                                <p class="text-xs text-muted-foreground">
                                    Porcentaje mínimo para aprobar (1-100)
                                </p>
                                <p v-if="examForm.errors.passing_score" class="text-sm text-destructive">
                                    {{ examForm.errors.passing_score }}
                                </p>
                            </div>

                            <!-- Tiempo límite -->
                            <div class="space-y-2">
                                <Label for="time_limit">Tiempo Límite (minutos)</Label>
                                <Input
                                    id="time_limit"
                                    v-model.number="examForm.time_limit"
                                    type="number"
                                    min="1"
                                    placeholder="Sin límite"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Dejar vacío para sin límite de tiempo
                                </p>
                                <p v-if="examForm.errors.time_limit" class="text-sm text-destructive">
                                    {{ examForm.errors.time_limit }}
                                </p>
                            </div>

                            <!-- Intentos máximos -->
                            <div class="space-y-2">
                                <Label for="max_attempts">Intentos Máximos *</Label>
                                <Input
                                    id="max_attempts"
                                    v-model.number="examForm.max_attempts"
                                    type="number"
                                    min="1"
                                    max="10"
                                    required
                                />
                                <p class="text-xs text-muted-foreground">
                                    Número de intentos permitidos (1-10)
                                </p>
                                <p v-if="examForm.errors.max_attempts" class="text-sm text-destructive">
                                    {{ examForm.errors.max_attempts }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="examForm.processing">
                                <Save class="mr-2 h-4 w-4" />
                                Crear Examen
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Examen Existente -->
            <template v-if="exam">
                <!-- Detalles / Edición del Examen -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2">
                                <ClipboardList class="h-5 w-5" />
                                {{ editingExam ? 'Editar Examen' : 'Detalles del Examen' }}
                            </CardTitle>
                            <div class="flex gap-2">
                                <Button
                                    v-if="!editingExam"
                                    variant="outline"
                                    size="sm"
                                    @click="editingExam = true"
                                >
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Editar
                                </Button>
                                <Button
                                    v-if="!editingExam"
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteExam"
                                >
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Eliminar
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <!-- Vista de detalles -->
                        <div v-if="!editingExam" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <FileText class="h-4 w-4" />
                                    Título
                                </div>
                                <p class="mt-1 font-semibold">{{ exam.title }}</p>
                            </div>
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Target class="h-4 w-4" />
                                    Puntaje de Aprobación
                                </div>
                                <p class="mt-1 font-semibold">{{ exam.passing_score }}%</p>
                            </div>
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Clock class="h-4 w-4" />
                                    Tiempo Límite
                                </div>
                                <p class="mt-1 font-semibold">
                                    {{ exam.time_limit ? `${exam.time_limit} minutos` : 'Sin límite' }}
                                </p>
                            </div>
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <Hash class="h-4 w-4" />
                                    Intentos Máximos
                                </div>
                                <p class="mt-1 font-semibold">{{ exam.max_attempts }}</p>
                            </div>
                            <div v-if="exam.description" class="rounded-lg border p-4 md:col-span-2 lg:col-span-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <BookOpen class="h-4 w-4" />
                                    Descripción
                                </div>
                                <p class="mt-1 text-sm">{{ exam.description }}</p>
                            </div>
                        </div>

                        <!-- Formulario de edición -->
                        <form v-else @submit.prevent="submitExam" class="space-y-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="space-y-2 md:col-span-2">
                                    <Label for="edit-title">Título del Examen *</Label>
                                    <Input
                                        id="edit-title"
                                        v-model="examForm.title"
                                        placeholder="Ej: Examen Final del Curso"
                                        required
                                    />
                                    <p v-if="examForm.errors.title" class="text-sm text-destructive">
                                        {{ examForm.errors.title }}
                                    </p>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <Label for="edit-description">Descripción</Label>
                                    <textarea
                                        id="edit-description"
                                        v-model="examForm.description"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        placeholder="Descripción o instrucciones del examen..."
                                        rows="3"
                                    ></textarea>
                                    <p v-if="examForm.errors.description" class="text-sm text-destructive">
                                        {{ examForm.errors.description }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="edit-passing_score">Puntaje de Aprobación (%) *</Label>
                                    <Input
                                        id="edit-passing_score"
                                        v-model.number="examForm.passing_score"
                                        type="number"
                                        min="1"
                                        max="100"
                                        required
                                    />
                                    <p v-if="examForm.errors.passing_score" class="text-sm text-destructive">
                                        {{ examForm.errors.passing_score }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="edit-time_limit">Tiempo Límite (minutos)</Label>
                                    <Input
                                        id="edit-time_limit"
                                        v-model.number="examForm.time_limit"
                                        type="number"
                                        min="1"
                                        placeholder="Sin límite"
                                    />
                                    <p v-if="examForm.errors.time_limit" class="text-sm text-destructive">
                                        {{ examForm.errors.time_limit }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="edit-max_attempts">Intentos Máximos *</Label>
                                    <Input
                                        id="edit-max_attempts"
                                        v-model.number="examForm.max_attempts"
                                        type="number"
                                        min="1"
                                        max="10"
                                        required
                                    />
                                    <p v-if="examForm.errors.max_attempts" class="text-sm text-destructive">
                                        {{ examForm.errors.max_attempts }}
                                    </p>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input
                                        id="edit-is_active"
                                        v-model="examForm.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                    />
                                    <Label for="edit-is_active">Examen Activo</Label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <Button type="button" variant="outline" @click="editingExam = false">
                                    <X class="mr-2 h-4 w-4" />
                                    Cancelar
                                </Button>
                                <Button type="submit" :disabled="examForm.processing">
                                    <Save class="mr-2 h-4 w-4" />
                                    Guardar Cambios
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Gestión de Preguntas -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <FileText class="h-5 w-5" />
                                    Preguntas del Examen
                                </CardTitle>
                                <CardDescription>
                                    {{ exam.questions.length }} {{ exam.questions.length === 1 ? 'pregunta' : 'preguntas' }} configuradas
                                </CardDescription>
                            </div>
                            <Button
                                v-if="!showQuestionForm"
                                @click="showQuestionForm = true"
                            >
                                <Plus class="mr-2 h-4 w-4" />
                                Agregar Pregunta
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Formulario de nueva pregunta -->
                        <div v-if="showQuestionForm" class="rounded-lg border-2 border-dashed border-primary/30 bg-muted/30 p-6">
                            <h3 class="mb-4 text-lg font-semibold">Nueva Pregunta</h3>
                            <form @submit.prevent="submitQuestion" class="space-y-4">
                                <!-- Tipo de pregunta -->
                                <div class="space-y-2">
                                    <Label>Tipo de Pregunta *</Label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="questionType"
                                                type="radio"
                                                value="multiple_choice"
                                                class="h-4 w-4 text-primary focus:ring-primary"
                                            />
                                            <span class="text-sm">Opción Múltiple</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="questionType"
                                                type="radio"
                                                value="true_false"
                                                class="h-4 w-4 text-primary focus:ring-primary"
                                            />
                                            <span class="text-sm">Verdadero/Falso</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Texto de la pregunta -->
                                <div class="space-y-2">
                                    <Label for="question_text">Pregunta *</Label>
                                    <textarea
                                        id="question_text"
                                        v-model="questionText"
                                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        placeholder="Escribe el texto de la pregunta..."
                                        rows="3"
                                        required
                                    ></textarea>
                                </div>

                                <!-- Puntos -->
                                <div class="space-y-2">
                                    <Label for="question_points">Puntos *</Label>
                                    <Input
                                        id="question_points"
                                        v-model.number="questionPoints"
                                        type="number"
                                        min="1"
                                        class="w-32"
                                        required
                                    />
                                </div>

                                <!-- Opciones para multiple_choice -->
                                <div v-if="questionType === 'multiple_choice'" class="space-y-3">
                                    <Label>Opciones de Respuesta *</Label>
                                    <div
                                        v-for="(option, index) in questionOptions"
                                        :key="index"
                                        class="flex items-center gap-2"
                                    >
                                        <input
                                            v-model="correctAnswer"
                                            type="radio"
                                            :value="questionOptions[index]"
                                            name="correct_answer_mc"
                                            class="h-4 w-4 text-primary focus:ring-primary"
                                            :disabled="!questionOptions[index]"
                                        />
                                        <Input
                                            v-model="questionOptions[index]"
                                            :placeholder="`Opción ${index + 1}`"
                                            class="flex-1"
                                            required
                                        />
                                        <Button
                                            v-if="questionOptions.length > 2"
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            @click="removeOption(index)"
                                        >
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>
                                    <Button type="button" variant="outline" size="sm" @click="addOption">
                                        <Plus class="mr-2 h-4 w-4" />
                                        Agregar Opción
                                    </Button>
                                    <p class="text-xs text-muted-foreground">
                                        Selecciona el radio junto a la opción correcta.
                                    </p>
                                </div>

                                <!-- Opciones para true_false -->
                                <div v-if="questionType === 'true_false'" class="space-y-3">
                                    <Label>Respuesta Correcta *</Label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="correctAnswer"
                                                type="radio"
                                                value="Verdadero"
                                                name="correct_answer_tf"
                                                class="h-4 w-4 text-primary focus:ring-primary"
                                            />
                                            <span class="text-sm">Verdadero</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="correctAnswer"
                                                type="radio"
                                                value="Falso"
                                                name="correct_answer_tf"
                                                class="h-4 w-4 text-primary focus:ring-primary"
                                            />
                                            <span class="text-sm">Falso</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 pt-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="showQuestionForm = false"
                                    >
                                        <X class="mr-2 h-4 w-4" />
                                        Cancelar
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="questionProcessing || !questionText || !correctAnswer"
                                    >
                                        <Save class="mr-2 h-4 w-4" />
                                        Guardar Pregunta
                                    </Button>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de preguntas existentes -->
                        <div v-if="exam.questions.length === 0 && !showQuestionForm" class="py-12 text-center text-muted-foreground">
                            <FileText class="mx-auto mb-4 h-12 w-12 opacity-50" />
                            <p class="mb-2 text-lg font-medium">No hay preguntas aún</p>
                            <p class="text-sm">Agrega preguntas para que los estudiantes puedan presentar el examen.</p>
                        </div>

                        <div v-for="(question, index) in exam.questions" :key="question.id" class="rounded-lg border p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                                            {{ index + 1 }}
                                        </span>
                                        <Badge variant="outline">
                                            {{ getQuestionTypeBadge(question.question_type) }}
                                        </Badge>
                                        <Badge variant="secondary">
                                            {{ question.points }} {{ question.points === 1 ? 'punto' : 'puntos' }}
                                        </Badge>
                                    </div>
                                    <p class="mb-3 text-sm font-medium">{{ question.question_text }}</p>

                                    <!-- Opciones de la pregunta -->
                                    <div v-if="question.question_type === 'multiple_choice' && question.options" class="space-y-1">
                                        <div
                                            v-for="(opt, optIndex) in question.options"
                                            :key="optIndex"
                                            class="flex items-center gap-2 text-sm"
                                            :class="opt === question.correct_answer ? 'font-semibold text-green-700' : 'text-muted-foreground'"
                                        >
                                            <CheckCircle v-if="opt === question.correct_answer" class="h-4 w-4 text-green-600" />
                                            <span v-else class="ml-4"></span>
                                            {{ opt }}
                                        </div>
                                    </div>
                                    <div v-else class="text-sm">
                                        <span class="text-muted-foreground">Respuesta correcta: </span>
                                        <span class="font-semibold text-green-700">{{ question.correct_answer }}</span>
                                    </div>
                                </div>

                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive hover:text-destructive"
                                    @click="deleteQuestion(question.id)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter v-if="exam.questions.length > 0">
                        <div class="flex w-full items-center justify-between text-sm text-muted-foreground">
                            <span>
                                Total: {{ exam.questions.length }} {{ exam.questions.length === 1 ? 'pregunta' : 'preguntas' }}
                            </span>
                            <span>
                                {{ exam.questions.reduce((sum, q) => sum + q.points, 0) }} puntos totales
                            </span>
                        </div>
                    </CardFooter>
                </Card>
            </template>
        </div>
    </AppLayout>
</template>
