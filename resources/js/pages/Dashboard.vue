<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { Package, Plus, ShoppingBag, TrendingUp, Users, DollarSign, Image, Settings, ShoppingCart, HelpCircle, Tag, Folder, Layout } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

const props = defineProps<{
    stats: {
        total_products: number;
        active_products: number;
        total_sales: number;
        total_customers: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(amount);
};

// Translation for admin dashboard (simplified - admin typically uses English)
// But we'll support translation for consistency
const texts = ref({
    manageProducts: 'Manage Products',
    manageProductsDesc: 'View, edit, and manage your product catalog',
    createProduct: 'Create Product',
    createProductDesc: 'Add a new product to your store',
    inventory: 'Inventory',
    inventoryDesc: 'Track stock levels and availability',
    manageCarousel: 'Manage Carousel',
    manageCarouselDesc: 'Manage homepage carousel slides',
    homepageSections: 'Homepage Sections',
    homepageSectionsDesc: 'Manage homepage content sections',
    settings: 'Settings',
    settingsDesc: 'Configure system settings',
    orders: 'Orders',
    ordersDesc: 'View and manage all orders',
    manageFaqs: 'Manage FAQs',
    manageFaqsDesc: 'Add and manage FAQ questions',
    manageBrands: 'Manage Brands',
    manageBrandsDesc: 'Add and manage product brands',
    manageCategories: 'Manage Categories',
    manageCategoriesDesc: 'Add and manage product categories',
    totalProducts: 'Total Products',
    activeProducts: 'Active Products',
    totalSales: 'Total Sales',
    customers: 'Customers',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    // Translate bidirectionally
    const keys = Object.keys(texts.value);
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            translated.value[key] = texts.value[key];
        }
    }
};

watch(language, translateAll, { immediate: true });
onMounted(translateAll);
</script>

<template>
    <Head title="Dashboard - Nantosha Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <Link
                    href="/admin/products"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <ShoppingBag class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.manageProducts || texts.manageProducts }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.manageProductsDesc || texts.manageProductsDesc }}</p>
                </Link>

                <Link
                    href="/admin/products/create"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Plus class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.createProduct || texts.createProduct }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.createProductDesc || texts.createProductDesc }}</p>
                </Link>

                <Link
                    href="/admin/inventory"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Package class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.inventory || texts.inventory }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.inventoryDesc || texts.inventoryDesc }}</p>
                </Link>

                <Link
                    href="/admin/carousel"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Image class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.manageCarousel || texts.manageCarousel }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.manageCarouselDesc || texts.manageCarouselDesc }}</p>
                </Link>

                <Link
                    href="/admin/homepage-sections"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Layout class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.homepageSections || texts.homepageSections }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.homepageSectionsDesc || texts.homepageSectionsDesc }}</p>
                </Link>

                <Link
                    href="/admin/settings"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Settings class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.settings || texts.settings }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.settingsDesc || texts.settingsDesc }}</p>
                </Link>

                <Link
                    href="/admin/orders"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <ShoppingCart class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.orders || texts.orders }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.ordersDesc || texts.ordersDesc }}</p>
                </Link>

                <Link
                    href="/admin/faqs"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <HelpCircle class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.manageFaqs || texts.manageFaqs }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.manageFaqsDesc || texts.manageFaqsDesc }}</p>
                </Link>

                <Link
                    href="/admin/brands"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Tag class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.manageBrands || texts.manageBrands }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.manageBrandsDesc || texts.manageBrandsDesc }}</p>
                </Link>

                <Link
                    href="/admin/categories"
                    class="group bg-white border border-gray-200 rounded-lg p-6 hover:border-gray-900 hover:shadow-lg transition-all"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <Folder class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ translated.manageCategories || texts.manageCategories }}</h3>
                    <p class="text-sm text-gray-600">{{ translated.manageCategoriesDesc || texts.manageCategoriesDesc }}</p>
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">{{ translated.totalProducts || texts.totalProducts }}</p>
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
                            <p class="text-sm font-medium text-gray-600 mb-1">{{ translated.activeProducts || texts.activeProducts }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.active_products }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <TrendingUp class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">{{ translated.totalSales || texts.totalSales }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_sales) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <DollarSign class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">{{ translated.customers || texts.customers }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_customers }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                            <Users class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
