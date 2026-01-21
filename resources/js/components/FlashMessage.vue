<script setup lang="ts">
import { Alert } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, Info, AlertCircle, XCircle, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage();
const visible = ref(false);

const flash = computed(() => ({
    success: page.props.flash?.success,
    error: page.props.flash?.error,
    info: page.props.flash?.info,
    warning: page.props.flash?.warning,
}));

const hasMessage = computed(() => {
    return !!(flash.value.success || flash.value.error || flash.value.info || flash.value.warning);
});

const messageType = computed(() => {
    if (flash.value.success) return 'success';
    if (flash.value.error) return 'error';
    if (flash.value.warning) return 'warning';
    if (flash.value.info) return 'info';
    return null;
});

const message = computed(() => {
    return flash.value.success || flash.value.error || flash.value.info || flash.value.warning;
});

const iconComponent = computed(() => {
    switch (messageType.value) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return XCircle;
        case 'warning':
            return AlertCircle;
        case 'info':
            return Info;
        default:
            return Info;
    }
});

const alertClass = computed(() => {
    switch (messageType.value) {
        case 'success':
            return 'border-green-500 bg-green-50 text-green-900 dark:bg-green-950 dark:text-green-100';
        case 'error':
            return 'border-red-500 bg-red-50 text-red-900 dark:bg-red-950 dark:text-red-100';
        case 'warning':
            return 'border-orange-500 bg-orange-50 text-orange-900 dark:bg-orange-950 dark:text-orange-100';
        case 'info':
            return 'border-blue-500 bg-blue-50 text-blue-900 dark:bg-blue-950 dark:text-blue-100';
        default:
            return '';
    }
});

watch(
    () => hasMessage.value,
    (newValue) => {
        if (newValue) {
            visible.value = true;
            setTimeout(() => {
                visible.value = false;
            }, 5000);
        }
    },
    { immediate: true }
);

const close = () => {
    visible.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform translate-y-[-100%] opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-[-100%] opacity-0"
    >
        <div
            v-if="visible && hasMessage"
            class="fixed top-4 right-4 z-[100] w-full max-w-md"
        >
            <div
                :class="[
                    'flex items-start gap-3 rounded-lg border-2 p-4 shadow-lg',
                    alertClass
                ]"
            >
                <component
                    :is="iconComponent"
                    class="mt-0.5 h-5 w-5 flex-shrink-0"
                />
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ message }}</p>
                </div>
                <button
                    @click="close"
                    class="flex-shrink-0 rounded-md p-1 transition-colors hover:bg-black/10 dark:hover:bg-white/10"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </div>
    </Transition>
</template>
