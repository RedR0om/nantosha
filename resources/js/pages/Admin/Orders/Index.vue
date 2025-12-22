<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Search, Eye, Package, DollarSign, TrendingUp, Clock } from 'lucide-vue-next';

const props = defineProps<{
    orders: any;
    stats: {
        total_orders: number;
        pending_orders: number;
        completed_orders: number;
        total_revenue: number;
    };
    filters: {
        search?: string;
        status?: string;
        payment_status?: string;
    };
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const paymentStatusFilter = ref(props.filters.payment_status || '');

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const applyFilters = () => {
    router.get('/admin/orders', {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        payment_status: paymentStatusFilter.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout>
        <Head title="Orders - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Orders</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manage and track all customer orders</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_orders }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center">
                                <Package class="w-6 h-6 text-gray-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Pending</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pending_orders }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                                <Clock class="w-6 h-6 text-yellow-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Completed</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.completed_orders }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                                <TrendingUp class="w-6 h-6 text-green-600" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatPrice(stats.total_revenue) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                <DollarSign class="w-6 h-6 text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Search
                            </label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Order #, Email, Name..."
                                    class="w-full pl-10 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Status
                            </label>
                            <select
                                v-model="statusFilter"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                                @change="applyFilters"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Payment Status
                            </label>
                            <select
                                v-model="paymentStatusFilter"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                                @change="applyFilters"
                            >
                                <option value="">All Payment Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button
                                @click="applyFilters"
                                class="w-full bg-gray-900 text-white py-2 text-sm uppercase tracking-wide font-semibold hover:bg-gray-800 transition"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Order #
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Payment
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="order in orders.data"
                                    :key="order.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ order.order_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ order.first_name }} {{ order.last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ order.email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ formatDate(order.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ order.items?.length || 0 }} item(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ formatPrice(order.total) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-medium rounded',
                                                getStatusColor(order.status)
                                            ]"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-medium rounded',
                                                getPaymentStatusColor(order.payment_status)
                                            ]"
                                        >
                                            {{ order.payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="`/admin/orders/${order.id}`"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors inline-flex items-center gap-1"
                                            title="View Order"
                                        >
                                            <Eye class="w-4 h-4" />
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="orders.links && orders.links.length > 3"
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between"
                    >
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} results
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in orders.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-4 py-2 text-sm border transition-colors rounded-lg',
                                    link.active
                                        ? 'bg-gray-900 dark:bg-gray-600 text-white border-gray-900 dark:border-gray-600'
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
    </AppLayout>
</template>

