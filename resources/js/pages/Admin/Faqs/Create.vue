<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const form = useForm({
    question: '',
    question_ja: '',
    answer: '',
    sort_order: 0,
    is_active: true,
});

const submit = () => {
    form.post('/admin/faqs', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Create FAQ - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        href="/admin/faqs"
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium mb-4"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to FAQs
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Create FAQ</h1>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question *
                                </label>
                                <input
                                    v-model="form.question"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    placeholder="Enter the question"
                                />
                                <p v-if="form.errors.question" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.question }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Question (Japanese)
                                </label>
                                <input
                                    v-model="form.question_ja"
                                    type="text"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    placeholder="質問を入力してください"
                                />
                                <p v-if="form.errors.question_ja" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.question_ja }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Answer *
                                </label>
                                <textarea
                                    v-model="form.answer"
                                    required
                                    rows="6"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    placeholder="Enter the answer"
                                ></textarea>
                                <p v-if="form.errors.answer" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.answer }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Sort Order
                                    </label>
                                    <input
                                        v-model.number="form.sort_order"
                                        type="number"
                                        min="0"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Status
                                    </label>
                                    <select
                                        v-model="form.is_active"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    >
                                        <option :value="true">Active</option>
                                        <option :value="false">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-4">
                            <Link
                                href="/admin/faqs"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gray-900 dark:bg-gray-600 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 dark:hover:bg-gray-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create FAQ</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

