<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Edit, Trash2, Eye } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    products: any;
}>();

const deleteProduct = (productId: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(`/admin/products/${productId}`, {
            preserveScroll: true,
        });
    }
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
    <AppLayout>
        <Head title="Products - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Products</h1>
                        <p class="text-gray-600">Manage your product catalog and inventory</p>
                    </div>
                    <Link
                        href="/admin/products/create"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors rounded-lg uppercase tracking-wide shadow-sm"
                    >
                        <Plus class="w-4 h-4" />
                        Add Product
                    </Link>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Image
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Brand
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Stock
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="product in products.data"
                                    :key="product.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="w-16 h-16 object-cover border border-gray-200 rounded-lg"
                                        />
                                        <div
                                            v-else
                                            class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400"
                                        >
                                            No Image
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ product.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            SKU: {{ product.sku || 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ product.brand?.name || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ formatPrice(product.price) }}
                                        </div>
                                        <div
                                            v-if="product.sale_price"
                                            class="text-xs text-gray-500 line-through"
                                        >
                                            {{ formatPrice(product.sale_price) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'text-sm',
                                                product.in_stock ? 'text-green-600' : 'text-red-600'
                                            ]"
                                        >
                                            {{ product.in_stock ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            Qty: {{ product.stock_quantity }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-medium rounded',
                                                product.is_active
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-800'
                                            ]"
                                        >
                                            {{ product.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link
                                                :href="`/products/${product.slug}`"
                                                target="_blank"
                                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                                title="View"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </Link>
                                            <Link
                                                :href="`/admin/products/${product.id}/edit`"
                                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                                title="Edit"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="deleteProduct(product.id)"
                                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="products.links && products.links.length > 3"
                        class="px-6 py-4 border-t border-gray-200 flex items-center justify-between"
                    >
                        <div class="text-sm text-gray-700">
                            Showing {{ products.from }} to {{ products.to }} of {{ products.total }} results
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in products.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-4 py-2 text-sm border transition-colors rounded-lg',
                                    link.active
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-gray-900 hover:text-gray-900',
                                    !link.url && 'opacity-50 cursor-not-allowed pointer-events-none',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

