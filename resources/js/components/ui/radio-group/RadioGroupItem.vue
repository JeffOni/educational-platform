<script setup lang="ts">
import { computed, inject } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  value: string
  id?: string
  disabled?: boolean
  class?: string
}>()

const modelValue = inject<any>('radioGroupModelValue')
const updateModelValue = inject<any>('radioGroupUpdate')

const isChecked = computed(() => modelValue?.value === props.value)

const classes = computed(() =>
  cn(
    'aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
    props.class
  )
)

const handleClick = () => {
  if (!props.disabled && updateModelValue) {
    updateModelValue(props.value)
  }
}
</script>

<template>
  <div class="flex items-center space-x-2">
    <button
      type="button"
      role="radio"
      :aria-checked="isChecked"
      :disabled="disabled"
      :class="classes"
      @click="handleClick"
    >
      <span
        v-if="isChecked"
        class="flex items-center justify-center"
      >
        <span class="h-2.5 w-2.5 rounded-full bg-current" />
      </span>
    </button>
  </div>
</template>
