<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

defineProps<{
    product: {
        id: number;
        name: string;
        slug: string;
        price: number;
        sale_price?: number;
        image?: string;
        brand?: { name: string };
        in_stock: boolean;
    };
}>();

const addToCart = (productId: number, event: Event) => {
    event.preventDefault();
    event.stopPropagation();
    
    router.post('/cart', {
        product_id: productId,
        quantity: 1,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Open cart sidebar
            window.dispatchEvent(new CustomEvent('openCartSidebar'));
        },
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
    <div class="bg-white border border-gray-200 rounded-none overflow-hidden hover:border-gray-300 transition-colors group">
        <Link :href="`/products/${product.slug}`" class="block">
            <div class="aspect-square bg-gray-50 relative overflow-hidden">
                <img
                    v-if="product.image"
                    :src="product.image"
                    :alt="product.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div
                    v-else
                    class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"
                >
                    No Image
                </div>
                <div
                    v-if="product.sale_price"
                    class="absolute top-2 right-2 bg-black text-white px-2 py-1 text-xs font-medium uppercase tracking-wide"
                >
                    Sale
                </div>
            </div>
        </Link>
        
        <div class="p-4">
            <p
                v-if="product.brand"
                class="text-xs text-gray-500 mb-1 uppercase tracking-wide"
            >
                Vendor: {{ product.brand.name }}
            </p>
            <Link :href="`/products/${product.slug}`" class="block">
                <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2 hover:text-gray-700 transition-colors leading-snug">
                    {{ product.name }}
                </h3>
            </Link>
            
            <div class="mb-3">
                <div class="flex items-baseline gap-2">
                    <span
                        v-if="product.sale_price"
                        class="text-base font-semibold text-gray-900"
                    >
                        {{ formatPrice(product.sale_price) }}
                    </span>
                    <span
                        :class="product.sale_price ? 'text-sm text-gray-500 line-through' : 'text-base font-semibold text-gray-900'"
                    >
                        {{ product.sale_price ? formatPrice(product.price) : formatPrice(product.price) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Unit price / per
                </p>
            </div>
            
            <div class="mb-2">
                <span class="text-xs text-gray-600">
                    Availability : <span :class="product.in_stock ? 'text-green-600 font-medium' : 'text-red-600'">
                        {{ product.in_stock ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </span>
            </div>
            
            <button
                @click="(e) => addToCart(product.id, e)"
                :disabled="!product.in_stock"
                class="w-full bg-black text-white py-2.5 text-sm font-medium hover:bg-gray-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:bg-gray-300 uppercase tracking-wide"
            >
                {{ product.in_stock ? 'Add to cart' : 'Sold out' }}
            </button>
        </div>
    </div>
</template>

