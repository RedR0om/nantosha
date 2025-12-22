<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Printer, Package, CreditCard, Banknote, CheckCircle } from 'lucide-vue-next';

const props = defineProps<{
    order: {
        id: number;
        order_number: string;
        email: string;
        first_name: string;
        last_name: string;
        phone: string;
        address_line_1: string;
        address_line_2?: string;
        city: string;
        state: string;
        postal_code: string;
        country: string;
        subtotal: number;
        tax: number;
        shipping: number;
        total: number;
        payment_method: string;
        status: string;
        payment_status: string;
        created_at: string;
        user?: any;
        items: Array<{
            id: number;
            product_name: string;
            product_sku?: string;
            quantity: number;
            price: number;
            total: number;
        }>;
    };
}>();

const form = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
});

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
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const updateOrder = () => {
    form.put(`/admin/orders/${props.order.id}`, {
        preserveScroll: true,
    });
};

const printReceipt = () => {
    window.open(`/orders/${props.order.id}`, '_blank');
};

const getPaymentMethodLabel = (method: string) => {
    return method === 'bank_transfer' ? 'Bank Transfer' : 'Credit Card';
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
        <Head :title="`Order ${order.order_number} - Admin`" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <Link
                        href="/admin/orders"
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Orders
                    </Link>
                    <button
                        @click="printReceipt"
                        class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-800 transition"
                    >
                        <Printer class="w-4 h-4" />
                        Print Receipt
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order {{ order.order_number }}</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Placed on {{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    :class="[
                                        'inline-flex px-3 py-1 text-sm font-medium rounded',
                                        getStatusColor(order.status)
                                    ]"
                                >
                                    {{ order.status }}
                                </span>
                                <span
                                    :class="[
                                        'inline-flex px-3 py-1 text-sm font-medium rounded',
                                        getPaymentStatusColor(order.payment_status)
                                    ]"
                                >
                                    {{ order.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Order Details Form -->
                        <form @submit.prevent="updateOrder" class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Status</label>
                                    <select
                                        v-model="form.status"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                                    <select
                                        v-model="form.payment_status"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Order</span>
                            </button>
                        </form>

                        <!-- Customer Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Customer Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ order.first_name }} {{ order.last_name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ order.email }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ order.phone }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Shipping Address</h3>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <p class="font-semibold">{{ order.first_name }} {{ order.last_name }}</p>
                                    <p>{{ order.address_line_1 }}</p>
                                    <p v-if="order.address_line_2">{{ order.address_line_2 }}</p>
                                    <p>{{ order.city }}, {{ order.state }} {{ order.postal_code }}</p>
                                    <p>{{ order.country }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Order Items</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="item in order.items" :key="item.id">
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ item.product_name }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300 text-center">
                                                {{ item.product_sku || 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300 text-center">
                                                {{ item.quantity }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300 text-right">
                                                {{ formatPrice(item.price) }}
                                            </td>
                                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white text-right">
                                                {{ formatPrice(item.total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="max-w-md ml-auto">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Subtotal:</span>
                                        <span>{{ formatPrice(order.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Shipping:</span>
                                        <span>{{ order.shipping === 0 ? 'Free' : formatPrice(order.shipping) }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                                        <span>Total:</span>
                                        <span>{{ formatPrice(order.total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Payment Method</h3>
                            <div class="flex items-center gap-3">
                                <component :is="order.payment_method === 'bank_transfer' ? Banknote : CreditCard" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ getPaymentMethodLabel(order.payment_method) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

