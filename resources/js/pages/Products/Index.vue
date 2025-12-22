<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import PublicNav from '@/components/PublicNav.vue';
import CartSidebarWrapper from '@/components/CartSidebarWrapper.vue';

const props = defineProps<{
    products: any;
    categories: any[];
    brands: any[];
    filters: {
        search?: string;
        category?: string;
        brand?: string;
    };
}>();

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedBrand = ref(props.filters.brand || '');

const applyFilters = () => {
    router.get('/products', {
        search: search.value || undefined,
        category: selectedCategory.value || undefined,
        brand: selectedBrand.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Products - Nantosha" />
    
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <PublicNav />
        <CartSidebarWrapper />
        <div class="py-8 md:py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white mb-8 tracking-tight">All Products</h1>
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 md:p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                            Search
                        </label>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search products..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                            Category
                        </label>
                        <select
                            v-model="selectedCategory"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                            @change="applyFilters"
                        >
                            <option value="">All Categories</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                            Brand
                        </label>
                        <select
                            v-model="selectedBrand"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                            @change="applyFilters"
                        >
                            <option value="">All Brands</option>
                            <option
                                v-for="brand in brands"
                                :key="brand.id"
                                :value="brand.id"
                            >
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button
                            @click="applyFilters"
                            class="w-full bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 transition-colors uppercase tracking-wide"
                        >
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                />
            </div>
            
            <!-- Pagination -->
            <div
                v-if="products.links"
                class="mt-12 flex justify-center"
            >
                <div class="flex gap-1">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-2 text-sm border transition-colors',
                            link.active
                                ? 'bg-black dark:bg-gray-600 text-white border-black dark:border-gray-600'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:border-gray-900 dark:hover:border-gray-500 hover:text-gray-900 dark:hover:text-white',
                            !link.url && 'opacity-50 cursor-not-allowed pointer-events-none',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

