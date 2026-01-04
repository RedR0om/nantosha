<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { Printer, CheckCircle, Package, Truck, CreditCard, Banknote } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

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
            variant?: any;
        }>;
    };
}>();

const page = usePage();
const { language } = useLanguage();

const successMessage = computed(() => {
    const flash = page.props.flash as any;
    return flash?.success || null;
});

// Translation texts
const texts = ref({
    backToHome: '← Back to Home',
    printReceipt: 'Print Receipt',
    orderReceipt: 'Order Receipt',
    orderInformation: 'Order Information',
    orderNumber: 'Order Number:',
    orderDate: 'Order Date:',
    status: 'Status:',
    paymentStatus: 'Payment Status:',
    customerInformation: 'Customer Information',
    name: 'Name:',
    email: 'Email:',
    phone: 'Phone:',
    shippingAddress: 'Shipping Address',
    orderItems: 'Order Items',
    product: 'Product',
    sku: 'SKU',
    quantity: 'Quantity',
    price: 'Price',
    total: 'Total',
    tier: 'Tier:',
    capsulesPerBottle: 'capsules per bottle',
    na: 'N/A',
    subtotal: 'Subtotal:',
    taxIncluded: 'Tax (included):',
    shipping: 'Shipping:',
    free: 'Free',
    paymentMethod: 'Payment Method',
    bankTransfer: 'Bank Transfer',
    creditCard: 'Credit Card',
    thankYou: 'Thank you for your order!',
    inquiries: 'For any inquiries, please contact us at support@nantosha.com',
    success: 'Success!',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    const keys = Object.keys(texts.value) as Array<keyof typeof texts.value>;
    for (const key of keys) {
        if (texts.value[key]) {
            try {
                translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
            } catch (error) {
                translated.value[key] = texts.value[key];
            }
        }
    }
};

watch(language, translateAll, { immediate: true });
onMounted(translateAll);

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
    if (method === 'bank_transfer') {
        return translated.value.bankTransfer || texts.value.bankTransfer;
    }
    return translated.value.creditCard || texts.value.creditCard;
};

const getPaymentMethodIcon = (method: string) => {
    return method === 'bank_transfer' ? Banknote : CreditCard;
};

// Parse variant if it's a string
const parseVariant = (item: any) => {
    if (!item.variant) return null;
    return typeof item.variant === 'string' ? JSON.parse(item.variant) : item.variant;
};

// Get tier information for display (if applicable)
const getTierInfo = (item: any): string | null => {
    const variant = parseVariant(item);
    if (variant && variant.type === 'bottle' && variant.tier) {
        const tier = variant.tier;
        if (tier.capsules) {
            const capsulesText = translated.value.capsulesPerBottle || texts.value.capsulesPerBottle;
            return `${tier.capsules} ${capsulesText}`;
        }
    }
    return null;
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
                    <strong class="font-bold">{{ translated.success || texts.success }}</strong>
                    <span class="block sm:inline">{{ successMessage }}</span>
                </div>
            </div>

            <!-- Print Button (Hidden when printing) -->
            <div class="mb-6 flex justify-between items-center print:hidden">
                <Link
                    href="/"
                    class="text-gray-600 hover:text-gray-900 font-medium"
                >
                    {{ translated.backToHome || texts.backToHome }}
                </Link>
                <button
                    @click="printReceipt"
                    class="flex items-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition uppercase tracking-wide"
                >
                    <Printer class="w-5 h-5" />
                    {{ translated.printReceipt || texts.printReceipt }}
                </button>
            </div>

            <!-- Receipt Content -->
            <div class="bg-white rounded-lg shadow-md p-8 print:shadow-none print:p-6" id="receipt">
                <!-- Header -->
                <div class="text-center mb-8 border-b border-gray-200 pb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Nantosha Import & Export Division</h1>
                    <p class="text-gray-600 mb-1">南東舎輸出入部</p>
                    <p class="text-sm text-gray-500">{{ translated.orderReceipt || texts.orderReceipt }}</p>
                </div>

                <!-- Order Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ translated.orderInformation || texts.orderInformation }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ translated.orderNumber || texts.orderNumber }}</span>
                                <span class="font-semibold text-gray-900">{{ order.order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ translated.orderDate || texts.orderDate }}</span>
                                <span class="font-semibold text-gray-900">{{ formatDate(order.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ translated.status || texts.status }}</span>
                                <span class="font-semibold text-gray-900 capitalize">{{ order.status }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ translated.paymentStatus || texts.paymentStatus }}</span>
                                <span class="font-semibold capitalize"
                                    :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-orange-600'"
                                >
                                    {{ order.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ translated.customerInformation || texts.customerInformation }}</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-600">{{ translated.name || texts.name }}</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.first_name }} {{ order.last_name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">{{ translated.email || texts.email }}</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.email }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">{{ translated.phone || texts.phone }}</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ order.phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="mb-8 border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ translated.shippingAddress || texts.shippingAddress }}</h3>
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
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">{{ translated.orderItems || texts.orderItems }}</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ translated.product || texts.product }}</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ translated.sku || texts.sku }}</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ translated.quantity || texts.quantity }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ translated.price || texts.price }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ translated.total || texts.total }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                        <div>{{ item.product_name }}</div>
                                        <div v-if="getTierInfo(item)" class="text-xs text-gray-500 mt-1">
                                            {{ translated.tier || texts.tier }} {{ getTierInfo(item) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 text-center">
                                        {{ item.product_sku || (translated.na || texts.na) }}
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
                                <span>{{ translated.subtotal || texts.subtotal }}</span>
                                <span>{{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div v-if="order.tax > 0" class="flex justify-between text-gray-600">
                                <span>{{ translated.taxIncluded || texts.taxIncluded }}</span>
                                <span>{{ formatPrice(order.tax) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>{{ translated.shipping || texts.shipping }}</span>
                                <span>{{ order.shipping === 0 ? (translated.free || texts.free) : formatPrice(order.shipping) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between text-lg font-bold text-gray-900">
                                <span>{{ translated.total || texts.total }}</span>
                                <span>{{ formatPrice(order.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ translated.paymentMethod || texts.paymentMethod }}</h3>
                    <div class="flex items-center gap-3">
                        <component :is="getPaymentMethodIcon(order.payment_method)" class="w-5 h-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-900">{{ getPaymentMethodLabel(order.payment_method) }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 pt-6 text-center text-xs text-gray-500">
                    <p>{{ translated.thankYou || texts.thankYou }}</p>
                    <p class="mt-2">{{ translated.inquiries || texts.inquiries }}</p>
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

