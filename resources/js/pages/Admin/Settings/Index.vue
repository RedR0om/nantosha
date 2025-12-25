<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Settings, ToggleLeft, ToggleRight } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    multiProductMode: boolean;
    taxSettings: {
        tax_enabled: boolean;
        tax_rate: number;
        price_increase_percentage: number;
    };
    shippingSettings: {
        shipping_enabled: boolean;
        shipping_fee: number;
        free_shipping_threshold: number;
    };
}>();

const form = useForm({
    multi_product_mode: props.multiProductMode,
    tax_enabled: props.taxSettings.tax_enabled,
    tax_rate: props.taxSettings.tax_rate,
    price_increase_percentage: props.taxSettings.price_increase_percentage,
    shipping_enabled: props.shippingSettings.shipping_enabled,
    shipping_fee: props.shippingSettings.shipping_fee,
    free_shipping_threshold: props.shippingSettings.free_shipping_threshold,
});

const isUpdating = ref(false);

const updateSettings = () => {
    isUpdating.value = true;
    
    form.put('/admin/settings', {
        preserveScroll: true,
        onSuccess: () => {
            isUpdating.value = false;
        },
        onError: () => {
            isUpdating.value = false;
        },
    });
};

const toggleMultiProductMode = () => {
    form.multi_product_mode = !form.multi_product_mode;
    updateSettings();
};

const toggleTaxEnabled = () => {
    form.tax_enabled = !form.tax_enabled;
    updateSettings();
};

const toggleShippingEnabled = () => {
    form.shipping_enabled = !form.shipping_enabled;
    updateSettings();
};
</script>

<template>
    <AppLayout>
        <Head title="Settings - Nantosha Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2 flex items-center gap-3">
                        <Settings class="w-8 h-8" />
                        System Settings
                    </h1>
                    <p class="text-gray-600">Configure system-wide settings for your store</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <!-- Multi-Product Mode Toggle -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Product Display Mode
                                </h3>
                                <p class="text-sm text-gray-600 mb-1">
                                    Control how products are displayed on the homepage.
                                </p>
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-gray-400 mt-1.5"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                <span v-if="form.multi_product_mode">Multiple Products Mode (Enabled)</span>
                                                <span v-else>Single Product Mode (Enabled)</span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <span v-if="form.multi_product_mode">
                                                    Shows multiple products in carousel format (Best Sellers, New Arrivals, etc.)
                                                </span>
                                                <span v-else>
                                                    Shows only one featured product with a large display
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-6">
                                <button
                                    @click="toggleMultiProductMode"
                                    :disabled="isUpdating"
                                    type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="form.multi_product_mode ? 'bg-gray-900' : 'bg-gray-200'"
                                    role="switch"
                                    :aria-checked="form.multi_product_mode"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                        :class="form.multi_product_mode ? 'translate-x-6' : 'translate-x-1'"
                                    >
                                    </span>
                                </button>
                                <div class="mt-2 text-center">
                                    <span class="text-xs font-medium"
                                        :class="form.multi_product_mode ? 'text-gray-900' : 'text-gray-500'"
                                    >
                                        {{ form.multi_product_mode ? 'ON' : 'OFF' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Settings -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Tax Settings
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Configure consumption tax and price increase settings.
                                </p>
                            </div>
                            <div class="ml-6">
                                <button
                                    @click="toggleTaxEnabled"
                                    :disabled="isUpdating"
                                    type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="form.tax_enabled ? 'bg-gray-900' : 'bg-gray-200'"
                                    role="switch"
                                    :aria-checked="form.tax_enabled"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                        :class="form.tax_enabled ? 'translate-x-6' : 'translate-x-1'"
                                    >
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div v-if="form.tax_enabled" class="space-y-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Price Increase Percentage (%)
                                </label>
                                <input
                                    v-model.number="form.price_increase_percentage"
                                    @blur="updateSettings"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Percentage to increase base prices before applying tax.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Consumption Tax Rate (%)
                                </label>
                                <input
                                    v-model.number="form.tax_rate"
                                    @blur="updateSettings"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Tax rate to apply to prices (e.g., 10 for 10%).
                                </p>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                <p class="text-xs text-blue-700">
                                    <strong>Calculation:</strong> Final Price = Base Price × (1 + Price Increase%) × (1 + Tax Rate%)
                                    <br>
                                    Example: Base ¥100, Increase 10%, Tax 10% = ¥100 × 1.1 × 1.1 = ¥121
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Settings -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    Shipping Settings
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Configure shipping fees and free shipping threshold.
                                </p>
                            </div>
                            <div class="ml-6">
                                <button
                                    @click="toggleShippingEnabled"
                                    :disabled="isUpdating"
                                    type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="form.shipping_enabled ? 'bg-gray-900' : 'bg-gray-200'"
                                    role="switch"
                                    :aria-checked="form.shipping_enabled"
                                >
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                        :class="form.shipping_enabled ? 'translate-x-6' : 'translate-x-1'"
                                    >
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div v-if="form.shipping_enabled" class="space-y-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Shipping Fee (¥)
                                </label>
                                <input
                                    v-model.number="form.shipping_fee"
                                    @blur="updateSettings"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Standard shipping fee charged to customers.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Free Shipping Threshold (¥)
                                </label>
                                <input
                                    v-model.number="form.free_shipping_threshold"
                                    @blur="updateSettings"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Orders above this amount will have free shipping.
                                </p>
                            </div>
                        </div>

                        <div v-else class="mt-4 text-sm text-gray-500">
                            Shipping fees are disabled. All shipping costs will be included in product prices.
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Note:</strong> Changes take effect immediately on the customer-facing homepage.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

