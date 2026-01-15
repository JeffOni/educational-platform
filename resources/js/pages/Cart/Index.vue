<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ShoppingCart, Trash2, ArrowLeft, CreditCard, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

interface Teacher {
    id: number;
    name: string;
}

interface CartItem {
    id: number;
    title: string;
    subtitle: string;
    price: number;
    image_path: string | null;
    teacher: Teacher;
    __raw_id: string;
}

interface Props {
    cartItems: CartItem[];
    total: number;
    cartCount: number;
}

const props = defineProps<Props>();

const removeItem = (rawId: string) => {
    if (confirm('¿Eliminar este curso del carrito?')) {
        router.delete(`/cart/remove/${rawId}`, {
            preserveScroll: true,
        });
    }
};

const clearCart = () => {
    if (confirm('¿Vaciar todo el carrito?')) {
        router.delete('/cart/clear');
    }
};

const checkout = () => {
    if (!props.cartItems.length) {
        return;
    }
    
    router.post('/checkout', {}, {
        onSuccess: () => {
            // Redirigido automáticamente por el controlador
        },
    });
};
</script>

<template>
    <Head title="Carrito de Compras" />

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-950">
        <!-- Navbar -->
        <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <Link href="/" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    EduPlatform
                </Link>
                <Link href="/" class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <ArrowLeft :size="20" />
                    Volver al Inicio
                </Link>
            </div>
        </nav>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-6 py-12">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <ShoppingCart :size="32" class="text-white" />
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        Carrito de Compras
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ cartCount }} {{ cartCount === 1 ? 'curso' : 'cursos' }} en tu carrito
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!cartItems.length" class="text-center py-24">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <ShoppingCart :size="48" class="text-gray-400" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    Tu carrito está vacío
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Explora nuestros cursos y agrega los que te interesen para comenzar a aprender.
                </p>
                <Link href="/" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-full font-semibold hover:from-indigo-700 hover:to-purple-700 transition shadow-lg">
                    Explorar Cursos
                </Link>
            </div>

            <!-- Cart Items -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Items List -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Cursos seleccionados
                        </h2>
                        <button 
                            @click="clearCart"
                            class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition flex items-center gap-2"
                        >
                            <X :size="16" />
                            Vaciar carrito
                        </button>
                    </div>

                    <div 
                        v-for="item in cartItems" 
                        :key="item.__raw_id"
                        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:shadow-lg transition"
                    >
                        <div class="flex gap-4">
                            <!-- Image -->
                            <div class="flex-shrink-0 w-32 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-950 dark:to-purple-950 rounded-xl overflow-hidden">
                                <img 
                                    v-if="item.image_path"
                                    :src="`/storage/${item.image_path}`"
                                    :alt="item.title"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <ShoppingCart :size="32" class="text-indigo-300 dark:text-indigo-700" />
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 line-clamp-2">
                                    {{ item.title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-1">
                                    {{ item.subtitle }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    Por {{ item.teacher.name }}
                                </p>
                            </div>

                            <!-- Price & Actions -->
                            <div class="flex flex-col items-end justify-between">
                                <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    ${{ item.price }}
                                </span>
                                <button 
                                    @click="removeItem(item.__raw_id)"
                                    class="flex items-center gap-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition text-sm"
                                >
                                    <Trash2 :size="16" />
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 sticky top-6 shadow-xl">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                            Resumen de Compra
                        </h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal ({{ cartCount }} {{ cartCount === 1 ? 'curso' : 'cursos' }})</span>
                                <span>${{ total }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Descuento</span>
                                <span class="text-green-600 dark:text-green-400">-$0.00</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    ${{ total }}
                                </span>
                            </div>
                        </div>

                        <template v-if="$page.props.auth.user">
                            <button
                                @click="checkout"
                                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-4 rounded-xl font-bold text-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                            >
                                <CreditCard :size="20" />
                                Procesar Compra
                            </button>
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                                ✓ Pago seguro • Acceso inmediato
                            </p>
                        </template>
                        <template v-else>
                            <Link 
                                href="/login"
                                class="block w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-4 rounded-xl font-bold text-lg transition shadow-lg hover:shadow-xl text-center"
                            >
                                Inicia Sesión para Comprar
                            </Link>
                        </template>

                        <div class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Acceso de por vida
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Certificado al completar
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Garantía de 30 días
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
