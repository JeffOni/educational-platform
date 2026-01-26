<script setup lang="ts">
import { provide, toRef } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string
  class?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

// Proveer el valor actual (reactivo) y función de actualización a los items
provide('radioGroupValue', toRef(props, 'modelValue'))
provide('radioGroupUpdate', (value: string) => {
  emit('update:modelValue', value)
})
</script>

<template>
  <div
    :class="cn('grid gap-2', props.class)"
    role="radiogroup"
  >
    <slot />
  </div>
</template>
