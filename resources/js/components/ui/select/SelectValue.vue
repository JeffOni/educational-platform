<script setup lang="ts">
import { SelectValue, type SelectValueProps } from 'reka-ui';

const props = defineProps<
  SelectValueProps & {
    class?: string;
  }
>();
</script>

<template>
  <SelectValue v-bind="props" :class="props.class">
    <slot />
  </SelectValue>
</template>
