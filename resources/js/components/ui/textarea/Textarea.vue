<script setup lang="ts">
import { useAttrs } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: string;
  modelValue?: string;
}>();

defineEmits<{
  'update:modelValue': [value: string];
}>();

const attrs = useAttrs();
</script>

<template>
  <textarea
    :class="
      cn(
        'flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
        props.class
      )
    "
    :value="modelValue"
    @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    v-bind="attrs"
  />
</template>
