<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Edit, Trash2, Eye, EyeOff, MoveUp, MoveDown } from 'lucide-vue-next';

const props = defineProps<{
    sections: any[];
}>();

const deleteSection = (id: number) => {
    if (confirm('Are you sure you want to delete this section?')) {
        router.delete(`/admin/homepage-sections/${id}`, {
            preserveScroll: true,
        });
    }
};

const toggleActive = (section: any) => {
    router.put(`/admin/homepage-sections/${section.id}`, {
        ...section,
        is_active: !section.is_active,
    }, {
        preserveScroll: true,
    });
};

const getTypeLabel = (type: string) => {
    const labels: { [key: string]: string } = {
        hero: 'Hero',
        features: 'Features',
        product_info: 'Product Info',
        pricing: 'Pricing',
        how_it_works: 'How It Works',
        custom: 'Custom',
        carousel: 'Carousel (Static)',
        best_sellers: 'Best Sellers (Static)',
        new_arrivals: 'New Arrivals (Static)',
        customer_favorites: 'Customer Favorites (Static)',
        featured_product: 'Featured Product (Static)',
    };
    return labels[type] || type;
};
</script>

<template>
    <AppLayout>
        <Head title="Homepage Sections Management - Nantosha Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Homepage Sections</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage your homepage content sections</p>
                    </div>
                    <Link
                        href="/admin/homepage-sections/create"
                        class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add New Section</span>
                    </Link>
                </div>

                <div v-if="sections.length === 0" class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No homepage sections yet</h3>
                    <p class="text-sm text-gray-600 mb-6">Get started by creating your first homepage section</p>
                    <Link
                        href="/admin/homepage-sections/create"
                        class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create First Section</span>
                    </Link>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="section in sections"
                        :key="section.id"
                        class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        {{ getTypeLabel(section.type) }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        Order: {{ section.sort_order }}
                                    </span>
                                    <span
                                        v-if="section.is_active"
                                        class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded"
                                    >
                                        Active
                                    </span>
                                    <span
                                        v-else
                                        class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded"
                                    >
                                        Inactive
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ section.title || '(No title)' }}
                                </h3>
                                <p v-if="section.title_ja" class="text-sm text-gray-600 mb-2">
                                    {{ section.title_ja }}
                                </p>
                                <p v-if="section.subtitle" class="text-sm text-gray-600 mb-2">
                                    {{ section.subtitle.substring(0, 150) }}{{ section.subtitle.length > 150 ? '...' : '' }}
                                </p>
                                <p v-if="['carousel', 'best_sellers', 'new_arrivals', 'customer_favorites', 'featured_product'].includes(section.type)" class="text-xs text-blue-600 mt-2">
                                    ⓘ Static section - Content is automatically populated. You can customize the title and order.
                                </p>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <button
                                    @click="toggleActive(section)"
                                    :class="[
                                        'p-2 rounded-lg transition-colors',
                                        section.is_active
                                            ? 'text-green-600 hover:bg-green-50'
                                            : 'text-gray-400 hover:bg-gray-100'
                                    ]"
                                    :title="section.is_active ? 'Hide section' : 'Show section'"
                                >
                                    <Eye v-if="section.is_active" class="w-5 h-5" />
                                    <EyeOff v-else class="w-5 h-5" />
                                </button>
                                <Link
                                    :href="`/admin/homepage-sections/${section.id}/edit`"
                                    class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    title="Edit section"
                                >
                                    <Edit class="w-5 h-5" />
                                </Link>
                                <button
                                    @click="deleteSection(section.id)"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete section"
                                >
                                    <Trash2 class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
