<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Mail, MailOpen, Trash2, User, Phone, Calendar } from 'lucide-vue-next';

const props = defineProps<{
    inquiry: {
        id: number;
        name: string;
        email: string;
        phone?: string;
        subject: string;
        message: string;
        is_read: boolean;
        created_at: string;
    };
}>();

const form = useForm({
    is_read: props.inquiry.is_read,
});

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

const updateInquiry = () => {
    form.put(`/admin/inquiries/${props.inquiry.id}`, {
        preserveScroll: true,
    });
};

const deleteInquiry = () => {
    if (confirm('Are you sure you want to delete this inquiry?')) {
        router.delete(`/admin/inquiries/${props.inquiry.id}`, {
            onSuccess: () => {
                router.visit('/admin/inquiries');
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="View Inquiry - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        href="/admin/inquiries"
                        class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-4"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Inquiries
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Inquiry Details</h1>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span
                                    :class="[
                                        'inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded',
                                        inquiry.is_read
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                                    ]"
                                >
                                    <MailOpen v-if="inquiry.is_read" class="w-4 h-4" />
                                    <Mail v-else class="w-4 h-4" />
                                    {{ inquiry.is_read ? 'Read' : 'Unread' }}
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatDate(inquiry.created_at) }}
                                </span>
                            </div>
                            <button
                                @click="deleteInquiry"
                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors inline-flex items-center gap-1"
                                title="Delete Inquiry"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6 space-y-6">
                        <!-- Subject -->
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ inquiry.subject }}
                            </h2>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <User class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Name</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ inquiry.name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <Mail class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email</p>
                                    <a
                                        :href="`mailto:${inquiry.email}`"
                                        class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                                    >
                                        {{ inquiry.email }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="inquiry.phone" class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <Phone class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Phone</p>
                                    <a
                                        :href="`tel:${inquiry.phone}`"
                                        class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline"
                                    >
                                        {{ inquiry.phone }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <Calendar class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Submitted</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(inquiry.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Message</p>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap leading-relaxed">
                                    {{ inquiry.message }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input
                                        v-model="form.is_read"
                                        type="checkbox"
                                        @change="updateInquiry"
                                        class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                                    />
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mark as read</span>
                                </label>
                                <div class="flex gap-3">
                                    <a
                                        :href="`mailto:${inquiry.email}?subject=Re: ${inquiry.subject}`"
                                        class="px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors"
                                    >
                                        Reply via Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

