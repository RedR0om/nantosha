<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Search, Eye, Mail, MailOpen, Trash2, Clock } from 'lucide-vue-next';

const props = defineProps<{
    inquiries: any;
    stats: {
        total_inquiries: number;
        unread_inquiries: number;
        read_inquiries: number;
    };
    filters: {
        search?: string;
        is_read?: string;
    };
}>();

const search = ref(props.filters.search || '');
const readFilter = ref(props.filters.is_read || '');

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const applyFilters = () => {
    router.get('/admin/inquiries', {
        search: search.value || undefined,
        is_read: readFilter.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteInquiry = (id: number) => {
    if (confirm('Are you sure you want to delete this inquiry?')) {
        router.delete(`/admin/inquiries/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Inquiries - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Inquiries</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manage customer inquiries and messages</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Inquiries</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_inquiries }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <Mail class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Unread</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.unread_inquiries }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                <MailOpen class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Read</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.read_inquiries }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <MailOpen class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                Search
                            </label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Name, Email, Subject..."
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
                                v-model="readFilter"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-900 dark:focus:border-gray-500 transition-colors"
                                @change="applyFilters"
                            >
                                <option value="">All Inquiries</option>
                                <option value="false">Unread</option>
                                <option value="true">Read</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button
                                @click="applyFilters"
                                class="w-full bg-gray-900 dark:bg-gray-700 text-white py-2 text-sm uppercase tracking-wide font-semibold hover:bg-gray-800 dark:hover:bg-gray-600 transition"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Inquiries Table -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="inquiry in inquiries.data"
                                    :key="inquiry.id"
                                    :class="[
                                        'hover:bg-gray-50 dark:hover:bg-gray-700',
                                        !inquiry.is_read && 'bg-yellow-50 dark:bg-yellow-900/10'
                                    ]"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded',
                                                inquiry.is_read
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                                            ]"
                                        >
                                            <MailOpen v-if="inquiry.is_read" class="w-3 h-3" />
                                            <Mail v-else class="w-3 h-3" />
                                            {{ inquiry.is_read ? 'Read' : 'Unread' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ inquiry.name }}
                                        </div>
                                        <div v-if="inquiry.phone" class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ inquiry.phone }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ inquiry.email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-md truncate">
                                            {{ inquiry.subject }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ formatDate(inquiry.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/admin/inquiries/${inquiry.id}`"
                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors inline-flex items-center gap-1"
                                                title="View Inquiry"
                                            >
                                                <Eye class="w-4 h-4" />
                                                View
                                            </Link>
                                            <button
                                                @click="deleteInquiry(inquiry.id)"
                                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors inline-flex items-center gap-1"
                                                title="Delete Inquiry"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="inquiries.data.length === 0" class="px-6 py-12 text-center">
                        <Mail class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No inquiries found</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">There are no inquiries matching your filters.</p>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="inquiries.links && inquiries.links.length > 3"
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between"
                    >
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ inquiries.from }} to {{ inquiries.to }} of {{ inquiries.total }} results
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in inquiries.links"
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

