<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Upload, X, Plus } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const sectionTypes = [
    { value: 'page_title', label: 'Page Title & Subtitle', description: 'Main page heading' },
    { value: 'profile_header', label: 'Profile Header', description: 'Doctor name and profile label' },
    { value: 'education', label: 'Education', description: 'Education information' },
    { value: 'career', label: 'Career', description: 'Career information' },
    { value: 'photo', label: 'Photo', description: 'Photo with caption and description' },
    { value: 'clinical_practice', label: 'Clinical Practice', description: 'Clinical practice section' },
    { value: 'current_focus', label: 'Current Focus', description: 'Current focus section' },
    { value: 'award', label: 'Award', description: 'Individual award entry' },
    { value: 'publication', label: 'Publication', description: 'Individual publication entry' },
    { value: 'medical_professionals', label: 'Medical Professionals', description: 'Information for medical professionals' },
    { value: 'newsletter_info', label: 'Newsletter Info', description: 'Newsletter information' },
];

const form = useForm({
    section_type: 'page_title',
    key: '',
    title: '',
    subtitle: '',
    text: '',
    description: '',
    image: null as File | null,
    image_url: '',
    image_alt: '',
    caption: '',
    sort_order: 0,
    is_active: true,
});

const imagePreview = ref<string | null>(null);

// For multiple items (awards, publications, photos)
interface MultipleItem {
    title: string;
    text?: string;
    image?: File | null;
    image_url?: string;
    image_alt?: string;
    caption?: string;
    description?: string;
    key?: string;
    sort_order: number;
}

const multipleItems = ref<MultipleItem[]>([]);
const itemImagePreviews = ref<Record<number, string>>({});

const addItem = () => {
    const newItem: MultipleItem = {
        title: '',
        text: '',
        image: null,
        image_url: '',
        image_alt: '',
        caption: '',
        description: '',
        key: '',
        sort_order: multipleItems.value.length,
    };
    multipleItems.value.push(newItem);
};

const removeItem = (index: number) => {
    if (multipleItems.value.length > 1) {
        multipleItems.value.splice(index, 1);
        delete itemImagePreviews.value[index];
        // Recalculate sort orders
        multipleItems.value.forEach((item, idx) => {
            item.sort_order = idx;
        });
    }
};

const handleImageChange = (e: Event, itemIndex?: number) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        // Check file size (5MB max for images)
        if (target.files[0].size > 5 * 1024 * 1024) {
            alert('Image file size must be less than 5MB');
            target.value = '';
            return;
        }
        
        if (itemIndex !== undefined) {
            // For multiple items (photos)
            multipleItems.value[itemIndex].image = target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                itemImagePreviews.value[itemIndex] = e.target?.result as string;
            };
            reader.readAsDataURL(target.files[0]);
        } else {
            // For single photo
            form.image = target.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target?.result as string;
            };
            reader.readAsDataURL(target.files[0]);
        }
    }
};

const removeImage = (itemIndex?: number) => {
    if (itemIndex !== undefined) {
        multipleItems.value[itemIndex].image = null;
        multipleItems.value[itemIndex].image_url = '';
        delete itemImagePreviews.value[itemIndex];
    } else {
        form.image = null;
        form.image_url = '';
        imagePreview.value = null;
    }
};

// Reset multiple items when section type changes
watch(() => form.section_type, (newType) => {
    if (['award', 'publication', 'photo'].includes(newType)) {
        multipleItems.value = [{
            title: '',
            text: '',
            image: null,
            image_url: '',
            image_alt: '',
            caption: '',
            description: '',
            key: '',
            sort_order: 0,
        }];
    } else {
        multipleItems.value = [];
    }
});

// Determine which fields to show based on section type
const showFields = computed(() => {
    const type = form.section_type;
    return {
        key: ['page_title', 'photo'].includes(type),
        title: true, // Most sections need title
        subtitle: ['page_title', 'profile_header', 'photo'].includes(type),
        text: ['education', 'career', 'clinical_practice', 'current_focus', 'medical_professionals', 'newsletter_info', 'award', 'publication'].includes(type),
        description: ['photo'].includes(type),
        image_url: ['photo'].includes(type),
        image_alt: ['photo'].includes(type),
        caption: ['photo'].includes(type),
    };
});

const getFieldLabel = (field: string) => {
    const type = form.section_type;
    const labels: Record<string, Record<string, string>> = {
        page_title: {
            title: 'Page Title (English)',
            subtitle: 'Page Subtitle (Japanese)',
        },
        profile_header: {
            title: 'Doctor Name',
            subtitle: 'Profile Label',
        },
        education: {
            title: 'Section Title',
            text: 'Education Details',
        },
        career: {
            title: 'Section Title',
            text: 'Career Details',
        },
        photo: {
            title: 'Photo Title (optional)',
            caption: 'Photo Caption',
            description: 'Photo Description',
            image_url: 'Image URL',
            image_alt: 'Image Alt Text',
        },
        clinical_practice: {
            title: 'Section Title',
            text: 'Clinical Practice Details',
        },
        current_focus: {
            title: 'Section Title',
            text: 'Current Focus Details',
        },
        award: {
            title: 'Award Title',
            text: 'Award Description (optional)',
        },
        publication: {
            title: 'Publication Title',
            text: 'Publication Details (optional)',
        },
        medical_professionals: {
            title: 'Section Title',
            text: 'Information Text',
        },
        newsletter_info: {
            title: 'Newsletter Title',
            text: 'Newsletter Description',
        },
    };
    return labels[type]?.[field] || field.charAt(0).toUpperCase() + field.slice(1).replace(/_/g, ' ');
};

const getPlaceholder = (field: string) => {
    const type = form.section_type;
    const placeholders: Record<string, Record<string, string>> = {
        page_title: {
            title: 'Dr. Allan Landrito',
            subtitle: 'アラン・ランドリト医師',
        },
        profile_header: {
            title: 'Dr. Allan Landrito',
            subtitle: 'Profile',
        },
        education: {
            title: 'Education',
            text: 'Graduated from De La Salle University College of Medicine.',
        },
        career: {
            title: 'Career',
            text: 'Former Health Officer for Muntinlupa City...',
        },
        photo: {
            key: 'photo1, photo2, photo3, etc.',
            caption: 'Dr. Allan A. Landrito',
            description: 'Detailed description of the photo...',
            image_url: 'https://example.com/image.jpg',
            image_alt: 'Dr. Allan Landrito',
        },
        clinical_practice: {
            title: 'Clinical Practice',
            text: 'Dr. Landrito has been practicing...',
        },
        current_focus: {
            title: 'Current Focus',
            text: 'Since 2022, Dr. Landrito has focused...',
        },
        award: {
            title: '2021 Outstanding Citizen & Humanitarian Award',
        },
        publication: {
            title: '"COVID-19 Liberation Now!"',
        },
        medical_professionals: {
            title: 'For Medical Professionals',
            text: 'Doctors requiring "Yakkan Shoumei"...',
        },
        newsletter_info: {
            title: 'Newsletter "IBERU"',
            text: '"IBERU" (Issue 1) – An alternative medicine...',
        },
    };
    return placeholders[type]?.[field] || '';
};

const submit = () => {
    // If award, publication, or photo with multiple items, submit all at once
    if (['award', 'publication', 'photo'].includes(form.section_type) && multipleItems.value.length > 0) {
        const itemsToSubmit = multipleItems.value.filter(item => {
            if (form.section_type === 'photo') {
                return item.title || item.caption || item.image || item.image_url;
            }
            return item.title || item.text;
        });
        
        if (itemsToSubmit.length === 0) {
            alert('Please add at least one item');
            return;
        }

        // Create bulk form with items array
        const images = itemsToSubmit.map(item => item.image).filter(img => img !== null);
        
        const bulkForm = useForm({
            section_type: form.section_type,
            sort_order: form.sort_order,
            is_active: form.is_active,
            items: itemsToSubmit.map((item, index) => ({
                title: item.title,
                text: item.text || '',
                caption: item.caption || '',
                description: item.description || '',
                image_url: item.image_url || '',
                image_alt: item.image_alt || '',
                key: item.key || `${form.section_type}${index + 1}`,
                sort_order: form.sort_order + index,
            })),
        });

        // Add images to form if any
        if (images.length > 0) {
            bulkForm.transform((data) => {
                const formData = new FormData();
                formData.append('section_type', data.section_type);
                formData.append('sort_order', data.sort_order);
                formData.append('is_active', data.is_active);
                formData.append('items', JSON.stringify(data.items));
                images.forEach((img, idx) => {
                    if (img) {
                        formData.append(`images[${idx}]`, img);
                    }
                });
                return formData;
            }).post('/admin/dr-landrito-profile', {
                preserveScroll: true,
                forceFormData: true,
            });
        } else {
            bulkForm.post('/admin/dr-landrito-profile', {
                preserveScroll: true,
            });
        }
    } else {
        // Regular single entry submission
        if (form.image) {
            form.transform((data) => ({
                ...data,
                image: form.image,
            })).post('/admin/dr-landrito-profile', {
                preserveScroll: true,
                forceFormData: true,
            });
        } else {
            form.post('/admin/dr-landrito-profile', {
                preserveScroll: true,
            });
        }
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Create Dr. Landrito Profile Content - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        href="/admin/dr-landrito-profile"
                        class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium mb-4"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Profile Content
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Create Content</h1>
                    <p class="text-sm text-gray-600 mt-1">Add new content to the Dr. Landrito profile page</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Section Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Section Type *
                                </label>
                                <select
                                    v-model="form.section_type"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                >
                                    <option v-for="type in sectionTypes" :key="type.value" :value="type.value">
                                        {{ type.label }} - {{ type.description }}
                                    </option>
                                </select>
                                <p v-if="form.errors.section_type" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.section_type }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ sectionTypes.find(t => t.value === form.section_type)?.description }}
                                </p>
                            </div>

                            <!-- Key (for page_title and single photos) -->
                            <div v-if="showFields.key && !['award', 'publication', 'photo'].includes(form.section_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Identifier Key *
                                </label>
                                <input
                                    v-model="form.key"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('key')"
                                />
                                <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.key }}
                                </p>
                                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-xs text-blue-800 font-medium mb-1">
                                        <span v-if="form.section_type === 'page_title'">📌 For Page Title & Subtitle:</span>
                                        <span v-else>📌 For Photos:</span>
                                    </p>
                                    <p class="text-xs text-blue-700">
                                        <span v-if="form.section_type === 'page_title'">
                                            You need to create <strong>two separate entries</strong>:<br/>
                                            • One with key = <code class="bg-blue-100 px-1 rounded">title</code> (for the English title)<br/>
                                            • One with key = <code class="bg-blue-100 px-1 rounded">subtitle</code> (for the Japanese subtitle)
                                        </span>
                                        <span v-else>
                                            Use unique keys to identify each photo:<br/>
                                            • <code class="bg-blue-100 px-1 rounded">photo1</code> = Main profile photo (shown in header)<br/>
                                            • <code class="bg-blue-100 px-1 rounded">photo2</code>, <code class="bg-blue-100 px-1 rounded">photo3</code>, etc. = Additional photos in gallery
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Title -->
                            <div v-if="showFields.title && !['award', 'publication', 'photo'].includes(form.section_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('title') }} *
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('title')"
                                />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Subtitle -->
                            <div v-if="showFields.subtitle">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('subtitle') }} *
                                </label>
                                <input
                                    v-model="form.subtitle"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('subtitle')"
                                />
                                <p v-if="form.errors.subtitle" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.subtitle }}
                                </p>
                            </div>

                            <!-- Text -->
                            <div v-if="showFields.text && !['award', 'publication'].includes(form.section_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('text') }} *
                                </label>
                                <textarea
                                    v-model="form.text"
                                    required
                                    rows="6"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('text')"
                                ></textarea>
                                <p v-if="form.errors.text" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.text }}
                                </p>
                            </div>

                            <!-- Image Upload (only for single photo, not multiple) -->
                            <div v-if="showFields.image_url && form.section_type !== 'photo'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('image_url') }} *
                                </label>
                                
                                <!-- Image Preview -->
                                <div v-if="imagePreview || form.image_url" class="mb-4">
                                    <div class="relative inline-block">
                                        <img
                                            :src="imagePreview || form.image_url"
                                            :alt="form.image_alt || 'Preview'"
                                            class="w-full max-w-md h-64 object-cover border border-gray-200 rounded-lg"
                                        />
                                        <button
                                            type="button"
                                            @click="removeImage"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Upload Button -->
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
                                    />
                                </label>
                                
                                <!-- Manual URL Input (Alternative) -->
                                <div class="mt-4">
                                    <p class="text-xs text-gray-500 mb-2">Or enter image URL manually:</p>
                                    <input
                                        v-model="form.image_url"
                                        type="url"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        :placeholder="getPlaceholder('image_url')"
                                        @input="imagePreview = null; form.image = null"
                                    />
                                </div>
                                
                                <p v-if="form.errors.image || form.errors.image_url" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.image || form.errors.image_url }}
                                </p>
                            </div>

                            <!-- Image Alt Text (only for photos) -->
                            <div v-if="showFields.image_alt">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('image_alt') }} *
                                </label>
                                <input
                                    v-model="form.image_alt"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('image_alt')"
                                />
                                <p v-if="form.errors.image_alt" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.image_alt }}
                                </p>
                            </div>

                            <!-- Caption (only for photos) -->
                            <div v-if="showFields.caption">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('caption') }} *
                                </label>
                                <input
                                    v-model="form.caption"
                                    type="text"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('caption')"
                                />
                                <p v-if="form.errors.caption" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.caption }}
                                </p>
                            </div>

                            <!-- Description (only for photos) -->
                            <div v-if="showFields.description">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ getFieldLabel('description') }} *
                                </label>
                                <textarea
                                    v-model="form.description"
                                    required
                                    rows="6"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    :placeholder="getPlaceholder('description')"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Multiple Items (Awards, Publications, Photos) -->
                            <div v-if="['award', 'publication', 'photo'].includes(form.section_type)">
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ form.section_type === 'award' ? 'Awards' : form.section_type === 'publication' ? 'Publications' : 'Photos' }} *
                                        </label>
                                        <button
                                            type="button"
                                            @click="addItem"
                                            class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                        >
                                            <Plus class="w-4 h-4" />
                                            Add {{ form.section_type === 'award' ? 'Award' : form.section_type === 'publication' ? 'Publication' : 'Photo' }}
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div
                                            v-for="(item, index) in multipleItems"
                                            :key="index"
                                            class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                        >
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ form.section_type === 'award' ? 'Award' : form.section_type === 'publication' ? 'Publication' : 'Photo' }} {{ index + 1 }}
                                                </span>
                                                <button
                                                    v-if="multipleItems.length > 1"
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
                                                >
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>
                                            
                                            <!-- Title -->
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ getFieldLabel('title') }} *
                                                </label>
                                                <input
                                                    v-model="item.title"
                                                    type="text"
                                                    required
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                    :placeholder="getPlaceholder('title')"
                                                />
                                            </div>
                                            
                                            <!-- Text (for awards and publications) -->
                                            <div v-if="['award', 'publication'].includes(form.section_type)" class="mb-3">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ getFieldLabel('text') }} (Optional)
                                                </label>
                                                <textarea
                                                    v-model="item.text"
                                                    rows="3"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                    :placeholder="getPlaceholder('text')"
                                                ></textarea>
                                            </div>
                                            
                                            <!-- Photo-specific fields -->
                                            <template v-if="form.section_type === 'photo'">
                                                <!-- Caption -->
                                                <div class="mb-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ getFieldLabel('caption') }} *
                                                    </label>
                                                    <input
                                                        v-model="item.caption"
                                                        type="text"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        :placeholder="getPlaceholder('caption')"
                                                    />
                                                </div>
                                                
                                                <!-- Description -->
                                                <div class="mb-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ getFieldLabel('description') }} *
                                                    </label>
                                                    <textarea
                                                        v-model="item.description"
                                                        rows="3"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        :placeholder="getPlaceholder('description')"
                                                    ></textarea>
                                                </div>
                                                
                                                <!-- Image Upload for Photo -->
                                                <div class="mb-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Image *
                                                    </label>
                                                    
                                                    <!-- Image Preview -->
                                                    <div v-if="itemImagePreviews[index] || item.image_url" class="mb-2">
                                                        <div class="relative inline-block">
                                                            <img
                                                                :src="itemImagePreviews[index] || item.image_url"
                                                                alt="Preview"
                                                                class="w-full max-w-xs h-48 object-cover border border-gray-200 rounded-lg"
                                                            />
                                                            <button
                                                                type="button"
                                                                @click="removeImage(index)"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                                            >
                                                                <X class="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Upload Button -->
                                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                                        <div class="flex flex-col items-center justify-center pt-3 pb-4">
                                                            <Upload class="w-6 h-6 text-gray-400 mb-1" />
                                                            <p class="text-xs text-gray-600">Click to upload image</p>
                                                            <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                                                        </div>
                                                        <input
                                                            type="file"
                                                            accept="image/*"
                                                            @change="(e) => handleImageChange(e, index)"
                                                            class="hidden"
                                                        />
                                                    </label>
                                                    
                                                    <!-- Manual URL Input -->
                                                    <div class="mt-2">
                                                        <p class="text-xs text-gray-500 mb-1">Or enter image URL:</p>
                                                        <input
                                                            v-model="item.image_url"
                                                            type="url"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                            :placeholder="getPlaceholder('image_url')"
                                                            @input="itemImagePreviews[index] = null; item.image = null"
                                                        />
                                                    </div>
                                                    
                                                    <!-- Image Alt Text -->
                                                    <div class="mt-2">
                                                        <input
                                                            v-model="item.image_alt"
                                                            type="text"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                            :placeholder="getPlaceholder('image_alt')"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <p v-if="multipleItems.length === 0" class="text-xs text-gray-500 italic">
                                            Click "Add {{ form.section_type === 'award' ? 'Award' : form.section_type === 'publication' ? 'Publication' : 'Photo' }}" to add items
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sort Order and Status -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Sort Order
                                    </label>
                                    <input
                                        v-model.number="form.sort_order"
                                        type="number"
                                        min="0"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select
                                        v-model="form.is_active"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    >
                                        <option :value="true">Active</option>
                                        <option :value="false">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                                <p class="text-sm text-blue-700">
                                    <strong>Note:</strong> Content will be automatically translated to Japanese when viewed on the public page.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-4">
                            <Link
                                href="/admin/dr-landrito-profile"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Content</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
