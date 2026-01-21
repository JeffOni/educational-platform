<script setup lang="ts">
import PublicNavbar from '@/components/PublicNavbar.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    transparent?: boolean;
    showRegister?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    transparent: false,
    showRegister: true,
});

const page = usePage();
const cartCount = computed(() => page.props.cartCount || 0);
</script>

<template>
    <div class="min-h-screen bg-background">
        <FlashMessage />
        
        <PublicNavbar
            :cart-count="cartCount"
            :transparent="transparent"
            :show-register="showRegister"
        />

        <!-- Main Content with padding to account for fixed navbar -->
        <main class="pt-16">
            <slot />
        </main>

        <!-- Optional Footer Slot -->
        <slot name="footer" />
    </div>
</template>
