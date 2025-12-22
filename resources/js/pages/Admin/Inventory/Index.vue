<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Package, Search, Filter, Edit, Save, X, AlertTriangle, CheckCircle, TrendingDown } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    products: any;
    stats: {
        total_products: number;
        in_stock: number;
        out_of_stock: number;
        low_stock: number;
        total_quantity: number;
    };
    filters: {
        search?: string;
        stock_status?: string;
    };
}>();

const search = ref(props.filters.search || '');
const stockStatus = ref(props.filters.stock_status || '');
const editingProducts = ref<Record<number, { stock_quantity: number; in_stock: boolean }>>({});
const bulkEditMode = ref(false);

const applyFilters = () => {
    router.get('/admin/inventory', {
        search: search.value || undefined,
        stock_status: stockStatus.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const startEdit = (product: any) => {
    editingProducts.value[product.id] = {
        stock_quantity: product.stock_quantity,
        in_stock: product.in_stock,
    };
};

const cancelEdit = (productId: number) => {
    delete editingProducts.value[productId];
};

const saveProduct = (product: any) => {
    const editedData = editingProducts.value[product.id];
    
    router.put(`/admin/inventory/${product.id}`, {
        stock_quantity: editedData.stock_quantity,
        in_stock: editedData.in_stock,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            delete editingProducts.value[product.id];
        },
    });
};

const getStockStatus = (product: any) => {
    if (!product.in_stock || product.stock_quantity <= 0) {
        return { label: 'Out of Stock', color: 'text-red-600 bg-red-50', icon: X };
    }
    if (product.stock_quantity <= 10) {
        return { label: 'Low Stock', color: 'text-orange-600 bg-orange-50', icon: AlertTriangle };
    }
    return { label: 'In Stock', color: 'text-green-600 bg-green-50', icon: CheckCircle };
};

const bulkForm = useForm({
    products: [] as Array<{ id: number; stock_quantity: number; in_stock: boolean }>,
});

const toggleBulkEdit = () => {
    bulkEditMode.value = !bulkEditMode.value;
    if (bulkEditMode.value) {
        bulkForm.products = props.products.data.map((product: any) => ({
            id: product.id,
            stock_quantity: product.stock_quantity,
            in_stock: product.in_stock,
        }));
    } else {
        bulkForm.reset();
    }
};

const saveBulkUpdate = () => {
    bulkForm.post('/admin/inventory/bulk-update', {
        preserveScroll: true,
        onSuccess: () => {
            bulkEditMode.value = false;
            bulkForm.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Inventory Management - Nantosha Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Inventory Management</h1>
                            <p class="text-gray-600">Track and manage stock levels for all products</p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="toggleBulkEdit"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                                    bulkEditMode
                                        ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                        : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                ]"
                            >
                                {{ bulkEditMode ? 'Cancel Bulk Edit' : 'Bulk Edit' }}
                            </button>
                            <Link
                                href="/admin/products"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                            >
                                ← Back to Products
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_products }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                                <Package class="w-6 h-6 text-gray-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">In Stock</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.in_stock }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                                <CheckCircle class="w-6 h-6 text-green-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Out of Stock</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.out_of_stock }}</p>
                            </div>
                            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                                <X class="w-6 h-6 text-red-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Low Stock</p>
                                <p class="text-2xl font-bold text-orange-600">{{ stats.low_stock }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                                <AlertTriangle class="w-6 h-6 text-orange-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Quantity</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_quantity.toLocaleString() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                <TrendingDown class="w-6 h-6 text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">
                                Search
                            </label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search by name or SKU..."
                                    class="w-full border border-gray-300 pl-10 pr-4 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">
                                Stock Status
                            </label>
                            <select
                                v-model="stockStatus"
                                @change="applyFilters"
                                class="w-full border border-gray-300 px-4 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                            >
                                <option value="">All Status</option>
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="low_stock">Low Stock (≤10)</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                @click="applyFilters"
                                class="w-full bg-gray-900 text-white py-2 text-sm uppercase tracking-wide font-semibold hover:bg-gray-800 transition-colors rounded-lg"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Edit Form -->
                <div v-if="bulkEditMode" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-blue-800">
                            <strong>Bulk Edit Mode:</strong> Edit multiple products at once. Click "Save All" when done.
                        </p>
                        <button
                            @click="saveBulkUpdate"
                            :disabled="bulkForm.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed"
                        >
                            {{ bulkForm.processing ? 'Saving...' : 'Save All' }}
                        </button>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        SKU
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Current Stock
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
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img
                                                v-if="product.image"
                                                :src="product.image"
                                                :alt="product.name"
                                                class="w-12 h-12 object-cover border border-gray-200 rounded-lg mr-3"
                                            />
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ product.name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ product.brand?.name || 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ product.sku || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="editingProducts[product.id] || (bulkEditMode && bulkForm.products.find(p => p.id === product.id))">
                                            <input
                                                v-if="bulkEditMode"
                                                v-model.number="bulkForm.products.find(p => p.id === product.id)!.stock_quantity"
                                                type="number"
                                                min="0"
                                                class="w-24 border border-gray-300 px-2 py-1 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded"
                                            />
                                            <input
                                                v-else
                                                v-model.number="editingProducts[product.id].stock_quantity"
                                                type="number"
                                                min="0"
                                                class="w-24 border border-gray-300 px-2 py-1 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded"
                                            />
                                        </div>
                                        <div v-else class="text-sm font-medium text-gray-900">
                                            {{ product.stock_quantity }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded',
                                                getStockStatus(product).color
                                            ]"
                                        >
                                            <component :is="getStockStatus(product).icon" class="w-3 h-3" />
                                            {{ getStockStatus(product).label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div v-if="editingProducts[product.id]" class="flex items-center justify-end gap-2">
                                            <button
                                                @click="saveProduct(product)"
                                                class="p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Save"
                                            >
                                                <Save class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="cancelEdit(product.id)"
                                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                                title="Cancel"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div v-else-if="!bulkEditMode" class="flex items-center justify-end gap-2">
                                            <button
                                                @click="startEdit(product)"
                                                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                                title="Edit"
                                            >
                                                <Edit class="w-4 h-4" />
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

