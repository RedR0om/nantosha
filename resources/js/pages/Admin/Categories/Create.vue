<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Save, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    image: null as File | null,
    is_active: true,
    sort_order: 0,
});

const imagePreview = ref<string | null>(null);

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const submit = () => {
    form.post('/admin/categories', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Create Category - Admin" />
        
        <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <Link
                        href="/admin/categories"
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Categories
                    </Link>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Category</h1>
                    
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image</label>
                                <div v-if="imagePreview" class="mb-2">
                                    <div class="relative inline-block">
                                        <img
                                            :src="imagePreview"
                                            alt="Image preview"
                                            class="w-32 h-32 object-cover border border-gray-200 dark:border-gray-600 rounded-lg"
                                        />
                                        <button
                                            type="button"
                                            @click="removeImage"
                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <Upload class="w-8 h-8 text-gray-400 dark:text-gray-500 mb-2" />
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload image</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                                    </div>
                                    <input
                                        id="image"
                                        type="file"
                                        accept="image/*"
                                        @change="handleImageChange"
                                        class="hidden"
                                    />
                                </label>
                                <p v-if="form.errors.image" class="mt-2 text-sm text-red-600">{{ form.errors.image }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort Order</label>
                                    <input
                                        id="sort_order"
                                        v-model.number="form.sort_order"
                                        type="number"
                                        min="0"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-gray-900 dark:focus:border-gray-500"
                                    />
                                    <p v-if="form.errors.sort_order" class="mt-2 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <div class="flex items-center h-10">
                                        <input
                                            id="is_active"
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="h-4 w-4 text-gray-900 dark:text-gray-500 border-gray-300 dark:border-gray-600 rounded focus:ring-gray-900 dark:focus:ring-gray-500"
                                        />
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">Active</label>
                                    </div>
                                    <p v-if="form.errors.is_active" class="mt-2 text-sm text-red-600">{{ form.errors.is_active }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors rounded-lg uppercase tracking-wide shadow-sm disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:hover:bg-gray-700"
                            >
                                <Save class="w-4 h-4" />
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Category</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

