<script setup lang="ts">
import { SelectRoot, type SelectRootEmits, type SelectRootProps } from 'reka-ui';

interface Props extends SelectRootProps {
  modelValue?: string;
}

const props = defineProps<Props>();

const emits = defineEmits<SelectRootEmits & {
  'update:modelValue': [value: string];
}>();
</script>

<template>
  <SelectRoot
    :model-value="modelValue"
    @update:model-value="(value) => emits('update:modelValue', value as string)"
  >
    <slot />
  </SelectRoot>
</template>
