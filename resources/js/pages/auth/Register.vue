<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { BookOpen, CheckCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    enrollment_code: '',
});

const courseName = ref<string | null>(null);
const codeValidating = ref(false);
const codeValid = ref<boolean | null>(null);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => form.enrollment_code,
    (value) => {
        if (debounceTimer) clearTimeout(debounceTimer);
        courseName.value = null;
        codeValid.value = null;

        if (value.length !== 8) return;

        codeValidating.value = true;
        debounceTimer = setTimeout(async () => {
            try {
                const { data } = await axios.post('/enrollment-code/validate', {
                    code: value,
                });
                codeValid.value = data.valid;
                courseName.value = data.valid ? data.course : null;
            } catch {
                codeValid.value = false;
            } finally {
                codeValidating.value = false;
            }
        }, 400);
    },
);

const submit = () => {
    form.post('/register', {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthBase
        title="Crear cuenta"
        description="Ingresa tus datos y el código de inscripción proporcionado"
    >
        <Head title="Registro" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="enrollment_code">Código de inscripción</Label>
                    <Input
                        id="enrollment_code"
                        type="text"
                        v-model="form.enrollment_code"
                        required
                        autofocus
                        :tabindex="1"
                        placeholder="Ej: AB1C2D3E"
                        class="text-center font-mono text-lg tracking-widest uppercase"
                        maxlength="8"
                    />
                    <p class="text-xs text-muted-foreground">
                        Código de 8 caracteres proporcionado por tu profesor o
                        administrador
                    </p>

                    <!-- Validando -->
                    <div
                        v-if="codeValidating"
                        class="flex items-center gap-2 text-sm text-muted-foreground"
                    >
                        <Spinner class="h-4 w-4" />
                        <span>Verificando código...</span>
                    </div>

                    <!-- Código válido: muestra el curso -->
                    <div
                        v-else-if="codeValid && courseName"
                        class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-3"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100"
                        >
                            <BookOpen class="h-5 w-5 text-green-600" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium text-green-700">
                                Serás inscrito en el curso:
                            </p>
                            <p
                                class="truncate text-sm font-semibold text-green-900"
                            >
                                {{ courseName }}
                            </p>
                        </div>
                        <CheckCircle class="h-5 w-5 shrink-0 text-green-500" />
                    </div>

                    <!-- Código inválido -->
                    <div
                        v-else-if="
                            codeValid === false &&
                            form.enrollment_code.length === 8
                        "
                        class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"
                    >
                        Código no válido, ya utilizado o expirado.
                    </div>

                    <InputError :message="form.errors.enrollment_code" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Nombre completo</Label>
                    <Input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        :tabindex="2"
                        autocomplete="name"
                        placeholder="Tu nombre completo"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Correo electrónico</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        :tabindex="3"
                        autocomplete="email"
                        placeholder="correo@ejemplo.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        placeholder="Contraseña"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation"
                        >Confirmar contraseña</Label
                    >
                    <Input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        placeholder="Confirmar contraseña"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :tabindex="6"
                    :disabled="form.processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="form.processing" />
                    Crear cuenta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                ¿Ya tienes una cuenta?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="7"
                    >Iniciar sesión</TextLink
                >
            </div>
        </form>
    </AuthBase>
</template>
