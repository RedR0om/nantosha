<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Edit, Trash2, Eye, EyeOff } from 'lucide-vue-next';

const props = defineProps<{
    contents: Record<string, any[]>;
}>();

const deleteContent = (id: number) => {
    if (confirm('Are you sure you want to delete this content?')) {
        router.delete(`/admin/dr-landrito-profile/${id}`, {
            preserveScroll: true,
        });
    }
};

const toggleActive = (content: any) => {
    router.put(`/admin/dr-landrito-profile/${content.id}`, {
        ...content,
        is_active: !content.is_active,
    }, {
        preserveScroll: true,
    });
};

const getSectionTypeLabel = (type: string) => {
    const labels: { [key: string]: string } = {
        page_title: 'Page Title & Subtitle',
        profile_header: 'Profile Header',
        education: 'Education',
        career: 'Career',
        clinical_practice: 'Clinical Practice',
        current_focus: 'Current Focus',
        award: 'Award',
        publication: 'Publication',
        photo: 'Photo',
        newsletter_info: 'Newsletter Info',
        medical_professionals: 'Medical Professionals',
    };
    return labels[type] || type;
};
</script>

<template>
    <AppLayout>
        <Head title="Dr. Landrito Profile Content - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Dr. Landrito Profile Content</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage content for the Dr. Landrito profile page</p>
                    </div>
                    <Link
                        href="/admin/dr-landrito-profile/create"
                        class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add New Content</span>
                    </Link>
                </div>

                <div v-if="Object.keys(contents).length === 0" class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No content yet</h3>
                    <p class="text-sm text-gray-600 mb-6">Get started by creating your first content item</p>
                    <Link
                        href="/admin/dr-landrito-profile/create"
                        class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 text-sm font-medium hover:bg-gray-800 transition-colors rounded-lg"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create First Content</span>
                    </Link>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="(sectionContents, sectionType) in contents"
                        :key="sectionType"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden"
                    >
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ getSectionTypeLabel(sectionType) }}
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="content in sectionContents"
                                :key="content.id"
                                class="p-6 hover:bg-gray-50 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                v-if="content.is_active"
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
                                            <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                                Order: {{ content.sort_order }}
                                            </span>
                                            <span v-if="content.key" class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                                Key: {{ content.key }}
                                            </span>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">
                                            {{ content.title || content.caption || '(No title)' }}
                                        </h3>
                                        <p v-if="content.subtitle" class="text-sm text-gray-600 mb-2">
                                            {{ content.subtitle }}
                                        </p>
                                        <p v-if="content.text" class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ content.text }}
                                        </p>
                                        <p v-if="content.description" class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ content.description }}
                                        </p>
                                        <div v-if="content.image_url" class="mt-2">
                                            <img
                                                :src="content.image_url"
                                                :alt="content.image_alt || ''"
                                                class="h-20 w-20 object-cover rounded border border-gray-200"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        <button
                                            @click="toggleActive(content)"
                                            :class="[
                                                'p-2 rounded-lg transition-colors',
                                                content.is_active
                                                    ? 'text-green-600 hover:bg-green-50'
                                                    : 'text-gray-400 hover:bg-gray-100'
                                            ]"
                                            :title="content.is_active ? 'Hide content' : 'Show content'"
                                        >
                                            <Eye v-if="content.is_active" class="w-5 h-5" />
                                            <EyeOff v-else class="w-5 h-5" />
                                        </button>
                                        <Link
                                            :href="`/admin/dr-landrito-profile/${content.id}/edit`"
                                            class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                            title="Edit content"
                                        >
                                            <Edit class="w-5 h-5" />
                                        </Link>
                                        <button
                                            @click="deleteContent(content.id)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete content"
                                        >
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

