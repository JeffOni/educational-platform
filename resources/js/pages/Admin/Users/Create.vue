<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Info } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Role {
    name: string;
}

interface Props {
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    student_type: 'external',
});

const selectedRole = ref('');

watch(selectedRole, (newRole) => {
    form.role = newRole;
    // Si no es student, limpiar el tipo
    if (newRole !== 'student') {
        form.student_type = 'external';
    }
});

const submit = () => {
    form.post('/admin/users');
};
</script>

<template>
    <Head title="Crear Usuario" />

    <AuthenticatedLayout>
        <div class="max-w-2xl space-y-6">
            <div class="flex items-center gap-4">
                <Link href="/admin/users">
                    <Button variant="ghost" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">
                        Crear Usuario
                    </h2>
                    <p class="text-muted-foreground">
                        Registra un nuevo usuario en el sistema
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit">
                <Card>
                    <CardHeader>
                        <CardTitle>Información del Usuario</CardTitle>
                        <CardDescription>
                            Completa los datos del nuevo usuario
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Nombre Completo</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Correo Electrónico</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                            />
                            <p
                                v-if="form.errors.email"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="password">Contraseña</Label>
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation"
                                    >Confirmar Contraseña</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    required
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="role">Rol del Usuario</Label>
                            <Select v-model="selectedRole" required>
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Selecciona un rol"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="role in roles"
                                        :key="role.name"
                                        :value="role.name"
                                    >
                                        {{
                                            role.name.charAt(0).toUpperCase() +
                                            role.name.slice(1)
                                        }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.role"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.role }}
                            </p>
                        </div>

                        <!-- Modalidad de Estudiante (solo si el rol es student) -->
                        <div
                            v-if="selectedRole === 'student'"
                            class="space-y-4"
                        >
                            <Label>Modalidad de Estudiante</Label>

                            <Alert>
                                <Info class="h-4 w-4" />
                                <AlertDescription>
                                    Selecciona la modalidad según el tipo de
                                    acceso y permisos
                                </AlertDescription>
                            </Alert>

                            <RadioGroup
                                v-model="form.student_type"
                                class="space-y-3"
                            >
                                <div
                                    class="flex items-start space-x-3 rounded-lg border bg-card p-4 transition-colors hover:bg-accent/50"
                                >
                                    <RadioGroupItem
                                        value="external"
                                        id="external"
                                        class="mt-1"
                                    />
                                    <div class="flex-1">
                                        <Label
                                            for="external"
                                            class="cursor-pointer font-medium"
                                        >
                                            Estudiante Externo (Solo Cursos)
                                        </Label>
                                        <p
                                            class="mt-1 text-sm text-muted-foreground"
                                        >
                                            Estudiantes que se registran por su
                                            cuenta. Pueden comprar cursos, ver
                                            contenido, escribir comentarios y
                                            acceder a recursos.
                                            <strong
                                                >No pueden enviar tareas ni
                                                recibir calificaciones.</strong
                                            >
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-start space-x-3 rounded-lg border bg-card p-4 transition-colors hover:bg-accent/50"
                                >
                                    <RadioGroupItem
                                        value="internal"
                                        id="internal"
                                        class="mt-1"
                                    />
                                    <div class="flex-1">
                                        <Label
                                            for="internal"
                                            class="cursor-pointer font-medium"
                                        >
                                            Estudiante de Academia (Cursos +
                                            Tareas)
                                        </Label>
                                        <p
                                            class="mt-1 text-sm text-muted-foreground"
                                        >
                                            Estudiantes institucionales o por
                                            convenio. Tienen todas las funciones
                                            del estudiante externo
                                            <strong
                                                >más la capacidad de enviar
                                                tareas y recibir
                                                calificaciones.</strong
                                            >
                                        </p>
                                    </div>
                                </div>
                            </RadioGroup>

                            <p
                                v-if="form.errors.student_type"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.student_type }}
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <Link href="/admin/users">
                                <Button variant="outline" type="button">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                {{
                                    form.processing
                                        ? 'Creando...'
                                        : 'Crear Usuario'
                                }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
