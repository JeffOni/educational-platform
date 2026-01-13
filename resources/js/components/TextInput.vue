<script setup lang="ts">
import { Input } from '@/components/ui/input';

defineOptions({
    inheritAttrs: false,
});

const model = defineModel<string | number>();
</script>

<template>
    <Input v-bind="$attrs" v-model="model" />
</template>
