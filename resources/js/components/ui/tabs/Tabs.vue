<script setup lang="ts">
import { TabsRoot, type TabsRootEmits, type TabsRootProps } from 'reka-ui';

const props = defineProps<TabsRootProps>();
const emits = defineEmits<TabsRootEmits>();
</script>

<template>
  <TabsRoot v-bind="props" v-on="emits">
    <slot />
  </TabsRoot>
</template>
