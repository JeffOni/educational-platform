<script setup lang="ts">
import { computed, inject, type Ref } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  value: string
  id?: string
  disabled?: boolean
  class?: string
}>()

const currentValue = inject<Ref<string | undefined>>('radioGroupValue')
const updateValue = inject<(value: string) => void>('radioGroupUpdate', () => {})

const isChecked = computed(() => currentValue?.value === props.value)

const handleClick = () => {
  if (!props.disabled) {
    updateValue(props.value)
  }
}
</script>

<template>
  <button
    type="button"
    role="radio"
    :id="id"
    :aria-checked="isChecked"
    :disabled="disabled"
    :class="cn(
      'aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class,
    )"
    @click="handleClick"
  >
    <span
      v-if="isChecked"
      class="flex h-full w-full items-center justify-center"
    >
      <span class="h-2.5 w-2.5 rounded-full bg-current" />
    </span>
  </button>
</template>
