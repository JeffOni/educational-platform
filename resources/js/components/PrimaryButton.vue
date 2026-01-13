<script setup lang="ts">
import { Button } from '@/components/ui/button';

defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <Button v-bind="$attrs">
        <slot />
    </Button>
</template>
