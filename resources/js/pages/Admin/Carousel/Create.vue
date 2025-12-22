<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Upload, X, Video } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    title: '',
    subtitle: '',
    image: null as File | null,
    video: null as File | null,
    media_type: 'image',
    link: '',
    button_text: '',
    sort_order: 0,
    is_active: true,
});

const imagePreview = ref<string | null>(null);
const videoPreview = ref<string | null>(null);

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        // Check file size (5MB max for images)
        if (target.files[0].size > 5 * 1024 * 1024) {
            alert('Image file size must be less than 5MB');
            target.value = '';
            return;
        }
        form.image = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const handleVideoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        // Check file size (50MB max for videos)
        if (target.files[0].size > 50 * 1024 * 1024) {
            alert('Video file size must be less than 50MB');
            target.value = '';
            return;
        }
        form.video = target.files[0];
        videoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const removeVideo = () => {
    form.video = null;
    if (videoPreview.value) {
        URL.revokeObjectURL(videoPreview.value);
    }
    videoPreview.value = null;
};

// Watch for media_type changes to clear the other media type
watch(() => form.media_type, (newType) => {
    if (newType === 'image') {
        removeVideo();
    } else if (newType === 'video') {
        removeImage();
    }
});

const submit = () => {
    // Ensure only the correct media type is sent
    if (form.media_type === 'image') {
        form.video = null;
    } else if (form.media_type === 'video') {
        form.image = null;
    }

    form.post('/admin/carousel', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            videoPreview.value = null;
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Create Carousel Slide - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Create New Carousel Slide</h1>
                            <p class="text-gray-600">Add a new slide to your homepage carousel</p>
                        </div>
                        <Link
                            href="/admin/carousel"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            ← Back to Carousel
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                    <div class="space-y-6">
                        <!-- Media Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Media Type *
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.media_type"
                                        type="radio"
                                        value="image"
                                        class="mr-2"
                                    />
                                    <span class="text-sm text-gray-700">Image</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.media_type"
                                        type="radio"
                                        value="video"
                                        class="mr-2"
                                    />
                                    <span class="text-sm text-gray-700">Video</span>
                                </label>
                            </div>
                            <p v-if="form.errors.media_type" class="mt-1 text-xs text-red-600">
                                {{ form.errors.media_type }}
                            </p>
                        </div>

                        <!-- Image Upload -->
                        <div v-if="form.media_type === 'image'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image *
                            </label>
                            <div v-if="imagePreview" class="mb-4">
                                <div class="relative inline-block">
                                    <img
                                        :src="imagePreview"
                                        alt="Preview"
                                        class="w-full max-w-md h-64 object-cover border border-gray-200 rounded-lg"
                                    />
                                    <button
                                        type="button"
                                        @click="removeImage"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                    >
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <Upload class="w-8 h-8 text-gray-400 mb-2" />
                                    <p class="text-sm text-gray-600">Click to upload image</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                                </div>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="hidden"
                                    :required="form.media_type === 'image'"
                                />
                            </label>
                            <p v-if="form.errors.image" class="mt-1 text-xs text-red-600">
                                {{ form.errors.image }}
                            </p>
                        </div>

                        <!-- Video Upload -->
                        <div v-if="form.media_type === 'video'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Video *
                            </label>
                            <div v-if="videoPreview" class="mb-4">
                                <div class="relative inline-block">
                                    <video
                                        :src="videoPreview"
                                        controls
                                        class="w-full max-w-md h-64 object-cover border border-gray-200 rounded-lg"
                                    ></video>
                                    <button
                                        type="button"
                                        @click="removeVideo"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                    >
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <Video class="w-8 h-8 text-gray-400 mb-2" />
                                    <p class="text-sm text-gray-600">Click to upload video</p>
                                    <p class="text-xs text-gray-400 mt-1">MP4, MOV, AVI, WMV, FLV, WebM up to 50MB</p>
                                    <p class="text-xs text-red-500 mt-1">Note: PHP upload limit may need to be increased for large videos</p>
                                </div>
                                <input
                                    type="file"
                                    accept="video/*"
                                    @change="handleVideoChange"
                                    class="hidden"
                                    :required="form.media_type === 'video'"
                                />
                            </label>
                            <p v-if="form.errors.video" class="mt-1 text-xs text-red-600">
                                {{ form.errors.video }}
                            </p>
                            <p v-if="form.errors.media_type" class="mt-1 text-xs text-red-600">
                                {{ form.errors.media_type }}
                            </p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., Genuine Ivermectin Capsules"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Subtitle
                            </label>
                            <textarea
                                v-model="form.subtitle"
                                rows="3"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., Verified by Japanese Research Institutions"
                            ></textarea>
                            <p v-if="form.errors.subtitle" class="mt-1 text-xs text-red-600">
                                {{ form.errors.subtitle }}
                            </p>
                        </div>

                        <!-- Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Link URL
                            </label>
                            <input
                                v-model="form.link"
                                type="text"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., /products or https://example.com"
                            />
                            <p v-if="form.errors.link" class="mt-1 text-xs text-red-600">
                                {{ form.errors.link }}
                            </p>
                        </div>

                        <!-- Button Text -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Button Text
                            </label>
                            <input
                                v-model="form.button_text"
                                type="text"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., Shop Now"
                            />
                            <p class="mt-1 text-xs text-gray-500">Only shown if Link URL is provided</p>
                            <p v-if="form.errors.button_text" class="mt-1 text-xs text-red-600">
                                {{ form.errors.button_text }}
                            </p>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sort Order
                            </label>
                            <input
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">
                                {{ form.errors.sort_order }}
                            </p>
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-gray-900"
                                />
                                <span class="ml-2 text-sm text-gray-700">Active (visible on homepage)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end gap-4 border-t border-gray-200 pt-6">
                        <Link
                            href="/admin/carousel"
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed rounded-lg uppercase tracking-wide"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Slide' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

