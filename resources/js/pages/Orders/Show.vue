<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { Printer, CheckCircle, Package, Truck, CreditCard, Banknote } from 'lucide-vue-next';

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

const page = usePage();
const successMessage = computed(() => {
    const flash = page.props.flash as any;
    return flash?.success || null;
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

const printReceipt = () => {
    window.print();
};

const getPaymentMethodLabel = (method: string) => {
    return method === 'bank_transfer' ? 'Bank Transfer' : 'Credit Card';
};

const getPaymentMethodIcon = (method: string) => {
    return method === 'bank_transfer' ? Banknote : CreditCard;
};

onMounted(() => {
    // Auto-print if coming from order confirmation
    if (successMessage.value) {
        // Small delay to ensure page is fully loaded
        setTimeout(() => {
            // Uncomment the line below if you want auto-print on page load
            // window.print();
        }, 500);
    }
});
</script>

<template>
    <Head :title="`Order ${order.order_number} - Receipt`" />
    
    <div class="min-h-screen bg-gray-50">
        <PublicNav />
        <div class="py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Success Message -->
            <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <div class="flex items-center gap-2">
                    <CheckCircle class="w-5 h-5" />
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ successMessage }}</span>
                </div>
            </div>

            <!-- Print Button (Hidden when printing) -->
            <div class="mb-6 flex justify-between items-center print:hidden">
                <Link
                    href="/"
                    class="text-gray-600 hover:text-gray-900 font-medium"
                >
                    ← Back to Home
                </Link>
                <button
                    @click="printReceipt"
                    class="flex items-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition uppercase tracking-wide"
                >
                    <Printer class="w-5 h-5" />
                    Print Receipt
                </button>
            </div>

            <!-- Receipt Content -->
            <div class="bg-white rounded-lg shadow-md p-8 print:shadow-none print:p-6" id="receipt">
                <!-- Header -->
                <div class="text-center mb-8 border-b border-gray-200 pb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Nantosha Import & Export Division</h1>
                    <p class="text-gray-600 mb-1">南東舎輸出入部</p>
                    <p class="text-sm text-gray-500">Order Receipt</p>
                </div>

                <!-- Order Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Order Information</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-semibold text-gray-900">{{ order.order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Date:</span>
                                <span class="font-semibold text-gray-900">{{ formatDate(order.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-semibold text-gray-900 capitalize">{{ order.status }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="font-semibold capitalize"
                                    :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-orange-600'"
                                >
                                    {{ order.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Customer Information</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-600">Name:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.first_name }} {{ order.last_name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Email:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.email }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="mb-8 border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Shipping Address</h3>
                    <div class="text-sm text-gray-900">
                        <p class="font-semibold">{{ order.first_name }} {{ order.last_name }}</p>
                        <p>{{ order.address_line_1 }}</p>
                        <p v-if="order.address_line_2">{{ order.address_line_2 }}</p>
                        <p>{{ order.city }}, {{ order.state }} {{ order.postal_code }}</p>
                        <p>{{ order.country }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Order Items</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">SKU</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        {{ item.product_name }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 text-center">
                                        {{ item.product_sku || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 text-center">
                                        {{ item.quantity }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 text-right">
                                        {{ formatPrice(item.price) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right">
                                        {{ formatPrice(item.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <div class="max-w-md ml-auto">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>{{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping:</span>
                                <span>{{ order.shipping === 0 ? 'Free' : formatPrice(order.shipping) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between text-lg font-bold text-gray-900">
                                <span>Total:</span>
                                <span>{{ formatPrice(order.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Payment Method</h3>
                    <div class="flex items-center gap-3">
                        <component :is="getPaymentMethodIcon(order.payment_method)" class="w-5 h-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-900">{{ getPaymentMethodLabel(order.payment_method) }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 pt-6 text-center text-xs text-gray-500">
                    <p>Thank you for your order!</p>
                    <p class="mt-2">For any inquiries, please contact us at support@nantosha.com</p>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt, #receipt * {
        visibility: visible;
    }
    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>

