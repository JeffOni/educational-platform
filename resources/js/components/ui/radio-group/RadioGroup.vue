<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string
  class?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const classes = computed(() =>
  cn('grid gap-2', props.class)
)
</script>

<template>
  <div
    :class="classes"
    role="radiogroup"
  >
    <slot />
  </div>
</template>
