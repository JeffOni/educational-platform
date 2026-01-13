<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="aspect-video rounded-xl bg-muted/50 p-4">
                    <h3 class="font-semibold">Card 1</h3>
                    <p class="text-sm text-muted-foreground">Contenido de ejemplo</p>
                </div>
                <div class="aspect-video rounded-xl bg-muted/50 p-4">
                    <h3 class="font-semibold">Card 2</h3>
                    <p class="text-sm text-muted-foreground">Contenido de ejemplo</p>
                </div>
                <div class="aspect-video rounded-xl bg-muted/50 p-4">
                    <h3 class="font-semibold">Card 3</h3>
                    <p class="text-sm text-muted-foreground">Contenido de ejemplo</p>
                </div>
            </div>
            <div class="min-h-[50vh] rounded-xl bg-muted/50 p-8">
                <h2 class="text-2xl font-bold">Bienvenido al Dashboard</h2>
                <p class="mt-2 text-muted-foreground">Este es tu panel de control</p>
            </div>
        </div>
    </AppLayout>
</template>
