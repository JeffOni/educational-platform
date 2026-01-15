<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CreditCard,
    ShoppingCart,
    Trash2,
    X,
} from 'lucide-vue-next';

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

    router.post(
        '/checkout',
        {},
        {
            onSuccess: () => {
                // Redirigido automáticamente por el controlador
            },
        },
    );
};
</script>

<template>
    <Head title="Carrito de Compras" />

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-950"
    >
        <!-- Navbar -->
        <nav
            class="border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <Link
                    href="/"
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-2xl font-bold text-transparent"
                >
                    EduPlatform
                </Link>
                <Link
                    href="/"
                    class="flex items-center gap-2 text-gray-600 transition hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"
                >
                    <ArrowLeft :size="20" />
                    Volver al Inicio
                </Link>
            </div>
        </nav>

        <!-- Content -->
        <div class="mx-auto max-w-7xl px-6 py-12">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg"
                >
                    <ShoppingCart :size="32" class="text-white" />
                </div>
                <div>
                    <h1
                        class="text-4xl font-bold text-gray-900 dark:text-white"
                    >
                        Carrito de Compras
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ cartCount }}
                        {{ cartCount === 1 ? 'curso' : 'cursos' }} en tu carrito
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!cartItems.length" class="py-24 text-center">
                <div
                    class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
                >
                    <ShoppingCart :size="48" class="text-gray-400" />
                </div>
                <h2
                    class="mb-3 text-2xl font-bold text-gray-900 dark:text-white"
                >
                    Tu carrito está vacío
                </h2>
                <p
                    class="mx-auto mb-8 max-w-md text-gray-600 dark:text-gray-400"
                >
                    Explora nuestros cursos y agrega los que te interesen para
                    comenzar a aprender.
                </p>
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 font-semibold text-white shadow-lg transition hover:from-indigo-700 hover:to-purple-700"
                >
                    Explorar Cursos
                </Link>
            </div>

            <!-- Cart Items -->
            <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Items List -->
                <div class="space-y-4 lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h2
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Cursos seleccionados
                        </h2>
                        <button
                            @click="clearCart"
                            class="flex items-center gap-2 text-sm text-red-600 transition hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                        >
                            <X :size="16" />
                            Vaciar carrito
                        </button>
                    </div>

                    <div
                        v-for="item in cartItems"
                        :key="item.__raw_id"
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="flex gap-4">
                            <!-- Image -->
                            <div
                                class="h-24 w-32 flex-shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-950 dark:to-purple-950"
                            >
                                <img
                                    v-if="item.image_path"
                                    :src="`/storage/${item.image_path}`"
                                    :alt="item.title"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <ShoppingCart
                                        :size="32"
                                        class="text-indigo-300 dark:text-indigo-700"
                                    />
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-grow">
                                <h3
                                    class="mb-1 line-clamp-2 text-lg font-bold text-gray-900 dark:text-white"
                                >
                                    {{ item.title }}
                                </h3>
                                <p
                                    class="mb-2 line-clamp-1 text-sm text-gray-600 dark:text-gray-400"
                                >
                                    {{ item.subtitle }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-500"
                                >
                                    Por {{ item.teacher.name }}
                                </p>
                            </div>

                            <!-- Price & Actions -->
                            <div
                                class="flex flex-col items-end justify-between"
                            >
                                <span
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-2xl font-bold text-transparent"
                                >
                                    ${{ item.price }}
                                </span>
                                <button
                                    @click="removeItem(item.__raw_id)"
                                    class="flex items-center gap-2 text-sm text-red-600 transition hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
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
                    <div
                        class="sticky top-6 rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 shadow-xl dark:border-gray-800 dark:from-gray-900 dark:to-gray-800"
                    >
                        <h2
                            class="mb-6 text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Resumen de Compra
                        </h2>

                        <div class="mb-6 space-y-3">
                            <div
                                class="flex justify-between text-gray-600 dark:text-gray-400"
                            >
                                <span
                                    >Subtotal ({{ cartCount }}
                                    {{
                                        cartCount === 1 ? 'curso' : 'cursos'
                                    }})</span
                                >
                                <span>${{ total }}</span>
                            </div>
                            <div
                                class="flex justify-between text-gray-600 dark:text-gray-400"
                            >
                                <span>Descuento</span>
                                <span class="text-green-600 dark:text-green-400"
                                    >-$0.00</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700"
                            >
                                <span
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                    >Total</span
                                >
                                <span
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-3xl font-bold text-transparent"
                                >
                                    ${{ total }}
                                </span>
                            </div>
                        </div>

                        <template v-if="$page.props.auth.user">
                            <button
                                @click="checkout"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-4 text-lg font-bold text-white shadow-lg transition hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl"
                            >
                                <CreditCard :size="20" />
                                Procesar Compra
                            </button>
                            <p
                                class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400"
                            >
                                ✓ Pago seguro • Acceso inmediato
                            </p>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                class="block w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-4 text-center text-lg font-bold text-white shadow-lg transition hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl"
                            >
                                Inicia Sesión para Comprar
                            </Link>
                        </template>

                        <div
                            class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-400"
                        >
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
