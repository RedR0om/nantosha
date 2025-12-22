<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Edit, Trash2, Folder } from 'lucide-vue-next';

const props = defineProps<{
    categories: any;
}>();

const deleteCategory = (categoryId: number) => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(`/admin/categories/${categoryId}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Categories - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Categories</h1>
                        <p class="text-gray-600 dark:text-gray-400">Manage product categories</p>
                    </div>
                    <Link
                        href="/admin/categories/create"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors rounded-lg uppercase tracking-wide shadow-sm dark:bg-gray-800 dark:hover:bg-gray-700"
                    >
                        <Plus class="w-4 h-4" />
                        Add Category
                    </Link>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Image
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Products
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Sort Order
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="category in categories.data"
                                    :key="category.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-12 h-12 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                            <img
                                                v-if="category.image"
                                                :src="category.image"
                                                :alt="category.name"
                                                class="w-full h-full object-cover"
                                            />
                                            <Folder
                                                v-else
                                                class="w-6 h-6 text-gray-400 dark:text-gray-500"
                                            />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ category.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 max-w-md line-clamp-2">
                                            {{ category.description || 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ category.products_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ category.sort_order || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-medium rounded',
                                                category.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            ]"
                                        >
                                            {{ category.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <Link
                                                :href="`/admin/categories/${category.id}/edit`"
                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Edit"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="deleteCategory(category.id)"
                                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors"
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
                        v-if="categories.links && categories.links.length > 3"
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between"
                    >
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ categories.from }} to {{ categories.to }} of {{ categories.total }} results
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in categories.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-4 py-2 text-sm border transition-colors rounded-lg',
                                    link.active
                                        ? 'bg-gray-900 text-white border-gray-900 dark:bg-gray-700 dark:border-gray-700'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-gray-900 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:text-white',
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

