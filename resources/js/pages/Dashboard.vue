<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Package, Plus, ShoppingBag, TrendingUp, Users, DollarSign, Image, Settings, ShoppingCart, HelpCircle, Tag, Folder } from 'lucide-vue-next';

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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Manage Products</h3>
                    <p class="text-sm text-gray-600">View, edit, and manage your product catalog</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Create Product</h3>
                    <p class="text-sm text-gray-600">Add a new product to your store</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Inventory</h3>
                    <p class="text-sm text-gray-600">Track stock levels and availability</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Manage Carousel</h3>
                    <p class="text-sm text-gray-600">Manage homepage carousel slides</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Settings</h3>
                    <p class="text-sm text-gray-600">Configure system settings</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Orders</h3>
                    <p class="text-sm text-gray-600">View and manage all orders</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Manage FAQs</h3>
                    <p class="text-sm text-gray-600">Add and manage FAQ questions</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Manage Brands</h3>
                    <p class="text-sm text-gray-600">Add and manage product brands</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Manage Categories</h3>
                    <p class="text-sm text-gray-600">Add and manage product categories</p>
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                            <p class="text-sm font-medium text-gray-600 mb-1">Active Products</p>
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
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Sales</p>
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
                            <p class="text-sm font-medium text-gray-600 mb-1">Customers</p>
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
