<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Image, Plus, Edit, Trash2, Eye, EyeOff } from 'lucide-vue-next';

const props = defineProps<{
    slides: any[];
}>();

const deleteSlide = (id: number) => {
    if (confirm('Are you sure you want to delete this slide?')) {
        router.delete(`/admin/carousel/${id}`, {
            preserveScroll: true,
        });
    }
};

const toggleActive = (slide: any) => {
    router.put(`/admin/carousel/${slide.id}`, {
        ...slide,
        is_active: !slide.is_active,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Carousel Management - Nantosha Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Carousel Management</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage your homepage carousel slides</p>
                    </div>
                    <Link
                        href="/admin/carousel/create"
                        class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add New Slide</span>
                    </Link>
                </div>

                <div v-if="slides.length === 0" class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <Image class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No carousel slides yet</h3>
                    <p class="text-sm text-gray-600 mb-6">Get started by creating your first carousel slide</p>
                    <Link
                        href="/admin/carousel/create"
                        class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create First Slide</span>
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="slide in slides"
                        :key="slide.id"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
                    >
                        <div class="relative aspect-video bg-gray-100">
                            <video
                                v-if="slide.media_type === 'video' && slide.video"
                                :src="slide.video"
                                class="w-full h-full object-cover"
                                muted
                            ></video>
                            <img
                                v-else-if="slide.image"
                                :src="slide.image"
                                :alt="slide.title || 'Carousel slide'"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                No Media
                            </div>
                            <div class="absolute top-2 left-2">
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-medium rounded',
                                        slide.media_type === 'video'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-purple-100 text-purple-800'
                                    ]"
                                >
                                    {{ slide.media_type === 'video' ? 'Video' : 'Image' }}
                                </span>
                            </div>
                            <div class="absolute top-2 right-2">
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-medium rounded',
                                        slide.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ slide.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-base font-semibold text-gray-900 mb-1 truncate">
                                {{ slide.title || 'Untitled Slide' }}
                            </h3>
                            <p v-if="slide.subtitle" class="text-sm text-gray-600 mb-2 line-clamp-2">
                                {{ slide.subtitle }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <span>Order: {{ slide.sort_order }}</span>
                                <span v-if="slide.link" class="text-blue-600">Has Link</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="`/admin/carousel/${slide.id}/edit`"
                                    class="flex-1 flex items-center justify-center gap-2 bg-gray-900 text-white px-3 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded"
                                >
                                    <Edit class="w-4 h-4" />
                                    <span>Edit</span>
                                </Link>
                                <button
                                    @click="toggleActive(slide)"
                                    :class="[
                                        'p-2 rounded transition-colors',
                                        slide.is_active
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                    :title="slide.is_active ? 'Deactivate' : 'Activate'"
                                >
                                    <Eye v-if="slide.is_active" class="w-4 h-4" />
                                    <EyeOff v-else class="w-4 h-4" />
                                </button>
                                <button
                                    @click="deleteSlide(slide.id)"
                                    class="p-2 bg-red-100 text-red-700 hover:bg-red-200 transition-colors rounded"
                                    title="Delete"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

