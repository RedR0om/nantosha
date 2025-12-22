<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicNav from '@/components/PublicNav.vue';

const props = defineProps<{
    cartItems: any[];
    subtotal: number;
    tax: number;
    shipping: number;
    total: number;
}>();

const updateQuantity = (cartItem: any, quantity: number) => {
    if (quantity < 1) return;
    
    router.put(`/cart/${cartItem.id}`, {
        quantity,
    }, {
        preserveScroll: true,
    });
};

const removeItem = (cartItemId: number) => {
    router.delete(`/cart/${cartItemId}`, {
        preserveScroll: true,
    });
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head title="Shopping Cart - Nantosha" />
    
    <div class="min-h-screen bg-gray-50">
        <PublicNav />
        <div class="py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 tracking-tight">Shopping Cart</h1>
            
            <div
                v-if="cartItems.length === 0"
                class="bg-white border border-gray-200 rounded-lg p-12 text-center"
            >
                <p class="text-gray-600 text-lg mb-4">Your cart is empty</p>
                <Link
                    href="/products"
                    class="inline-block bg-gray-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition uppercase tracking-wide"
                >
                    Continue Shopping
                </Link>
            </div>
            
            <div
                v-else
                class="grid grid-cols-1 lg:grid-cols-3 gap-8"
            >
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        v-for="item in cartItems"
                        :key="item.id"
                        class="bg-white border border-gray-200 rounded-lg p-6"
                    >
                        <div class="flex gap-4">
                            <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    :alt="item.product.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    {{ item.product?.name }}
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    {{ formatPrice(item.price) }} each
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <button
                                            @click="updateQuantity(item, item.quantity - 1)"
                                            class="w-8 h-8 border border-gray-300 rounded-lg flex items-center justify-center"
                                        >
                                            -
                                        </button>
                                        <span class="w-12 text-center">{{ item.quantity }}</span>
                                        <button
                                            @click="updateQuantity(item, item.quantity + 1)"
                                            class="w-8 h-8 border border-gray-300 rounded-lg flex items-center justify-center"
                                        >
                                            +
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center gap-4">
                                        <span class="font-semibold text-gray-900">
                                            {{ formatPrice(item.price * item.quantity) }}
                                        </span>
                                        <button
                                            @click="removeItem(item.id)"
                                            class="text-red-600 hover:text-red-700"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 tracking-tight">
                            Order Summary
                        </h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Tax (8%)</span>
                                <span>{{ formatPrice(tax) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span>{{ shipping === 0 ? 'Free' : formatPrice(shipping) }}</span>
                            </div>
                            <div
                                v-if="subtotal >= 10000"
                                class="text-green-600 text-xs mt-2"
                            >
                                ✓ Free shipping on orders over ¥10,000!
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                        </div>
                        
                        <Link
                            href="/checkout"
                            class="block w-full bg-gray-900 text-white py-3 rounded-lg font-semibold text-center hover:bg-gray-800 transition uppercase tracking-wide"
                        >
                            Proceed to Checkout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

