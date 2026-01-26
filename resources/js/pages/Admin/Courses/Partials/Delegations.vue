<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import {
    AlertCircle,
    Calendar,
    CheckCircle2,
    Clock,
    User,
    UserPlus,
    XCircle,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface Teacher {
    id: number;
    name: string;
    email: string;
}

interface Delegation {
    id: number;
    delegated_to: {
        id: number;
        name: string;
        email: string;
    };
    delegated_by: {
        id: number;
        name: string;
    };
    permissions: string[];
    starts_at: string | null;
    expires_at: string | null;
    reason: string | null;
    is_active: boolean;
    created_at: string;
}

interface Props {
    course: { id: number; title: string };
    teachers: Teacher[];
    isOwner: boolean;
    isAdmin: boolean;
}

const props = defineProps<Props>();

const delegations = ref<Delegation[]>([]);
const isOpen = ref(false);
const loading = ref(false);

const availablePermissions = {
    view_course: 'Ver contenido del curso',
    grade_assignments: 'Calificar tareas',
    answer_questions: 'Responder preguntas',
    edit_content: 'Editar contenido',
    view_analytics: 'Ver estadísticas',
};

const form = useForm({
    delegated_to: '',
    permissions: [] as string[],
    starts_at: '',
    expires_at: '',
    reason: '',
});

const canDelegate = computed(() => props.isOwner || props.isAdmin);

const loadDelegations = async () => {
    try {
        loading.value = true;
        const response = await axios.get(
            `/admin/courses/${props.course.id}/delegations`,
        );
        delegations.value = response.data.delegations;
    } catch (error) {
        console.error('Error cargando delegaciones:', error);
    } finally {
        loading.value = false;
    }
};

const submitDelegation = () => {
    form.post(`/admin/courses/${props.course.id}/delegations`, {
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
            loadDelegations();
        },
    });
};

const revokeDelegation = async (delegationId: number) => {
    if (!confirm('¿Estás seguro de revocar esta delegación?')) return;

    try {
        await axios.post(
            `/admin/courses/${props.course.id}/delegations/${delegationId}/revoke`,
        );
        loadDelegations();
    } catch (error) {
        console.error('Error revocando delegación:', error);
    }
};

const formatDate = (date: string | null) => {
    if (!date) return 'Sin límite';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getDelegationStatus = (delegation: Delegation) => {
    if (!delegation.is_active)
        return {
            text: 'Revocada',
            variant: 'secondary' as const,
            icon: XCircle,
        };

    const now = new Date();
    if (delegation.starts_at && new Date(delegation.starts_at) > now) {
        return { text: 'Programada', variant: 'default' as const, icon: Clock };
    }
    if (delegation.expires_at && new Date(delegation.expires_at) < now) {
        return {
            text: 'Expirada',
            variant: 'secondary' as const,
            icon: XCircle,
        };
    }
    return { text: 'Activa', variant: 'default' as const, icon: CheckCircle2 };
};

onMounted(() => {
    loadDelegations();
});
</script>

<template>
    <div class="space-y-6 rounded-lg bg-white p-6 shadow">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Delegaciones del Curso</h2>
                <p class="text-muted-foreground">
                    Gestiona profesores temporales o asistentes
                </p>
            </div>

            <Dialog v-model:open="isOpen">
                <DialogTrigger as-child>
                    <Button v-if="canDelegate" variant="default">
                        <UserPlus class="mr-2 h-4 w-4" />
                        Asignar Profesor Temporal
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[600px]">
                    <DialogHeader>
                        <DialogTitle>Crear Delegación</DialogTitle>
                        <DialogDescription>
                            Asigna permisos temporales a otro profesor para
                            gestionar este curso
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitDelegation" class="space-y-6">
                        <!-- Profesor -->
                        <div class="space-y-2">
                            <Label for="teacher">Profesor *</Label>
                            <Select v-model="form.delegated_to" required>
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Selecciona un profesor"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                        :value="String(teacher.id)"
                                    >
                                        {{ teacher.name }} ({{ teacher.email }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.delegated_to"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.delegated_to }}
                            </p>
                        </div>

                        <!-- Permisos -->
                        <div class="space-y-3">
                            <Label>Permisos *</Label>
                            <div class="space-y-2">
                                <div
                                    v-for="(label, key) in availablePermissions"
                                    :key="key"
                                    class="flex items-center space-x-2"
                                >
                                    <Checkbox
                                        :id="key"
                                        :checked="
                                            form.permissions.includes(key)
                                        "
                                        @update:checked="
                                            (checked) => {
                                                if (checked) {
                                                    form.permissions.push(key);
                                                } else {
                                                    form.permissions =
                                                        form.permissions.filter(
                                                            (p) => p !== key,
                                                        );
                                                }
                                            }
                                        "
                                    />
                                    <Label
                                        :for="key"
                                        class="cursor-pointer text-sm font-normal"
                                    >
                                        {{ label }}
                                    </Label>
                                </div>
                            </div>
                            <p
                                v-if="form.errors.permissions"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.permissions }}
                            </p>
                        </div>

                        <!-- Fechas -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="starts_at">Fecha de Inicio</Label>
                                <Input
                                    id="starts_at"
                                    v-model="form.starts_at"
                                    type="datetime-local"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Opcional. Si no se especifica, comienza
                                    ahora
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="expires_at"
                                    >Fecha de Expiración</Label
                                >
                                <Input
                                    id="expires_at"
                                    v-model="form.expires_at"
                                    type="datetime-local"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Opcional. Deja vacío para permanente
                                </p>
                            </div>
                        </div>

                        <!-- Razón -->
                        <div class="space-y-2">
                            <Label for="reason">Razón</Label>
                            <Input
                                id="reason"
                                v-model="form.reason"
                                placeholder="Ej: Licencia médica, vacaciones..."
                            />
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                @click="isOpen = false"
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'Creando...'
                                        : 'Crear Delegación'
                                }}
                            </Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Alerta si no puede delegar -->
        <Alert v-if="!canDelegate" variant="destructive">
            <AlertCircle class="h-4 w-4" />
            <AlertDescription>
                Solo el profesor titular o los administradores pueden crear
                delegaciones.
            </AlertDescription>
        </Alert>

        <!-- Lista de delegaciones -->
        <div v-if="loading" class="py-8 text-center">
            <p class="text-muted-foreground">Cargando delegaciones...</p>
        </div>

        <div v-else-if="delegations.length === 0" class="py-12 text-center">
            <User class="mx-auto h-12 w-12 text-muted-foreground/50" />
            <p class="mt-4 text-muted-foreground">
                No hay delegaciones activas para este curso
            </p>
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="delegation in delegations"
                :key="delegation.id"
                class="rounded-lg border p-4 transition-colors hover:bg-accent/50"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold">
                                {{ delegation.delegated_to.name }}
                            </h3>
                            <Badge
                                :variant="
                                    getDelegationStatus(delegation).variant
                                "
                            >
                                <component
                                    :is="getDelegationStatus(delegation).icon"
                                    class="mr-1 h-3 w-3"
                                />
                                {{ getDelegationStatus(delegation).text }}
                            </Badge>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            {{ delegation.delegated_to.email }}
                        </p>

                        <!-- Permisos -->
                        <div class="mt-2 flex flex-wrap gap-1">
                            <Badge
                                v-for="permission in delegation.permissions"
                                :key="permission"
                                variant="outline"
                                class="text-xs"
                            >
                                {{
                                    availablePermissions[
                                        permission as keyof typeof availablePermissions
                                    ]
                                }}
                            </Badge>
                        </div>

                        <!-- Fechas -->
                        <div
                            class="mt-3 flex items-center gap-4 text-xs text-muted-foreground"
                        >
                            <div class="flex items-center gap-1">
                                <Calendar class="h-3 w-3" />
                                <span
                                    >Inicio:
                                    {{ formatDate(delegation.starts_at) }}</span
                                >
                            </div>
                            <div class="flex items-center gap-1">
                                <Calendar class="h-3 w-3" />
                                <span
                                    >Expira:
                                    {{
                                        formatDate(delegation.expires_at)
                                    }}</span
                                >
                            </div>
                        </div>

                        <!-- Razón -->
                        <p
                            v-if="delegation.reason"
                            class="mt-2 text-sm text-muted-foreground italic"
                        >
                            "{{ delegation.reason }}"
                        </p>

                        <!-- Delegado por -->
                        <p class="mt-2 text-xs text-muted-foreground">
                            Delegado por: {{ delegation.delegated_by.name }}
                        </p>
                    </div>

                    <!-- Acciones -->
                    <div v-if="canDelegate && delegation.is_active">
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="revokeDelegation(delegation.id)"
                        >
                            Revocar
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
