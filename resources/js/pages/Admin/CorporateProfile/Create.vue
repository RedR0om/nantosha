<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Plus, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const sectionTypes = [
    { value: 'page_title', label: 'Page Title & Subtitle', description: 'Main page heading' },
    { value: 'company_info', label: 'Company Information', description: 'Company name, CEO, etc.' },
    { value: 'office_location', label: 'Office Location', description: 'Office address and contact details' },
    { value: 'contact_info', label: 'Contact Information', description: 'Email and other contact details' },
];

const form = useForm({
    section_type: 'page_title',
    key: '',
    title: '',
    subtitle: '',
    label: '',
    text: '',
    value: '',
    field_type: '',
    sort_order: 0,
    is_active: true,
}, {
    resetOnSuccess: false,
});

// For office_location: multiple contact fields
interface ContactField {
    label: string;
    value: string;
    field_type: string;
    sort_order: number;
}

const officeContactFields = ref<ContactField[]>([
    { label: '', value: '', field_type: '', sort_order: 0 }
]);

const addContactField = () => {
    officeContactFields.value.push({
        label: '',
        value: '',
        field_type: '',
        sort_order: officeContactFields.value.length
    });
};

const removeContactField = (index: number) => {
    if (officeContactFields.value.length > 1) {
        officeContactFields.value.splice(index, 1);
        // Recalculate sort orders
        officeContactFields.value.forEach((field, idx) => {
            field.sort_order = idx;
        });
    }
};

// Reset office contact fields when section type changes
watch(() => form.section_type, (newType) => {
    if (newType === 'office_location') {
        officeContactFields.value = [
            { label: '', value: '', field_type: '', sort_order: 0 }
        ];
    }
});

// Determine which fields to show based on section type
const showFields = computed(() => {
    const type = form.section_type;
    return {
        key: ['page_title', 'company_info', 'office_location'].includes(type),
        title: ['page_title', 'office_location'].includes(type),
        subtitle: ['page_title'].includes(type),
        label: ['company_info', 'office_location', 'contact_info'].includes(type),
        value: ['company_info', 'office_location', 'contact_info'].includes(type),
        text: false, // Not used in corporate profile
        field_type: ['office_location', 'contact_info'].includes(type),
    };
});

const getFieldLabel = (field: string) => {
    const type = form.section_type;
    const labels: Record<string, Record<string, string>> = {
        page_title: {
            title: 'Page Title (English)',
            subtitle: 'Page Subtitle (Japanese)',
        },
        company_info: {
            label: 'Label (e.g., "Company Name", "CEO")',
            value: 'Value (e.g., "Nantosha Co., Ltd.")',
        },
        office_location: {
            title: 'Office Name',
            label: 'Field Label (e.g., "Address", "Tel")',
            value: 'Field Value',
            field_type: 'Field Type',
        },
        contact_info: {
            label: 'Label (e.g., "Email")',
            value: 'Value (e.g., "ishiyama@nantosha.com")',
            field_type: 'Field Type',
        },
    };
    return labels[type]?.[field] || field.charAt(0).toUpperCase() + field.slice(1).replace(/_/g, ' ');
};

const getPlaceholder = (field: string) => {
    const type = form.section_type;
    const placeholders: Record<string, Record<string, string>> = {
        page_title: {
            title: 'Corporate Profile',
            subtitle: '会社概要',
            key: 'title or subtitle',
        },
        company_info: {
            key: 'company_name or ceo',
            label: 'Company Name',
            value: 'Nantosha Co., Ltd.',
        },
        office_location: {
            key: 'head_office, saitama, or manila',
            title: 'Head Office (Publishing & Editorial Department)',
            label: 'Address',
            value: '1-2-10-102 Hakusan, Bunkyo-ku, Tokyo 113-0001, Japan',
            field_type: 'address, phone, fax, mobile',
        },
        contact_info: {
            label: 'Email',
            value: 'ishiyama@nantosha.com',
            field_type: 'email',
        },
    };
    return placeholders[type]?.[field] || '';
};

const fieldTypeOptions = computed(() => {
    const type = form.section_type;
    if (type === 'office_location') {
        return [
            { value: 'address', label: 'Address' },
            { value: 'phone', label: 'Phone / Tel' },
            { value: 'fax', label: 'Fax' },
            { value: 'mobile', label: 'Mobile' },
            { value: 'tel_fax', label: 'Tel & Fax' },
        ];
    } else if (type === 'contact_info') {
        return [
            { value: 'email', label: 'Email' },
            { value: 'phone', label: 'Phone' },
        ];
    }
    return [];
});

const submit = () => {
    // If office_location with multiple fields, submit all at once
    if (form.section_type === 'office_location' && officeContactFields.value.length > 0) {
        const fieldsToSubmit = officeContactFields.value.filter(f => f.label && f.value && f.field_type);
        
        if (fieldsToSubmit.length === 0) {
            alert('Please add at least one contact field');
            return;
        }

        if (!form.key || !form.title) {
            alert('Please fill in the Office Key and Office Name');
            return;
        }

        // Create a new form with fields array
        const bulkForm = useForm({
            section_type: 'office_location',
            key: form.key,
            title: form.title,
            sort_order: form.sort_order,
            is_active: form.is_active,
            fields: fieldsToSubmit.map(f => ({
                label: f.label,
                value: f.value,
                field_type: f.field_type,
            })),
        });

        bulkForm.post('/admin/corporate-profile', {
            preserveScroll: true,
            onSuccess: () => {
                // Form will redirect automatically
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
            },
        });
    } else {
        // Regular single entry submission
        form.post('/admin/corporate-profile', {
            preserveScroll: true,
            onSuccess: () => {
                // Form will redirect automatically
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Create Corporate Profile Content - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        href="/admin/corporate-profile"
                        class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium mb-4"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Corporate Profile Content
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Create Content</h1>
                    <p class="text-sm text-gray-600 mt-1">Add new content to the Corporate Profile page</p>
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

                            <!-- Key -->
                            <div v-if="showFields.key">
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
                                    <p class="text-xs text-blue-800 font-medium mb-1">📌 Key Usage:</p>
                                    <p class="text-xs text-blue-700">
                                        <span v-if="form.section_type === 'page_title'">
                                            Use <code class="bg-blue-100 px-1 rounded">title</code> for page title, <code class="bg-blue-100 px-1 rounded">subtitle</code> for page subtitle
                                        </span>
                                        <span v-else-if="form.section_type === 'company_info'">
                                            Use <code class="bg-blue-100 px-1 rounded">company_name</code> for company name, <code class="bg-blue-100 px-1 rounded">ceo</code> for CEO
                                        </span>
                                        <span v-else>
                                            Use <code class="bg-blue-100 px-1 rounded">head_office</code>, <code class="bg-blue-100 px-1 rounded">saitama</code>, or <code class="bg-blue-100 px-1 rounded">manila</code> for office locations
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Title -->
                            <div v-if="showFields.title">
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

                            <!-- Office Location: Multiple Contact Fields -->
                            <div v-if="form.section_type === 'office_location'">
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Contact Fields *
                                        </label>
                                        <button
                                            type="button"
                                            @click="addContactField"
                                            class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                        >
                                            <Plus class="w-4 h-4" />
                                            Add Field
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div
                                            v-for="(field, index) in officeContactFields"
                                            :key="index"
                                            class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                        >
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700">Field {{ index + 1 }}</span>
                                                <button
                                                    v-if="officeContactFields.length > 1"
                                                    type="button"
                                                    @click="removeContactField(index)"
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
                                                >
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Field Type *
                                                    </label>
                                                    <select
                                                        v-model="field.field_type"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                    >
                                                        <option value="">Select type</option>
                                                        <option v-for="option in fieldTypeOptions" :key="option.value" :value="option.value">
                                                            {{ option.label }}
                                                        </option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Label *
                                                    </label>
                                                    <input
                                                        v-model="field.label"
                                                        type="text"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        placeholder="e.g., Address, Tel, Fax"
                                                    />
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Value *
                                                    </label>
                                                    <input
                                                        v-model="field.value"
                                                        type="text"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        placeholder="Enter value"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="mt-2 text-xs text-gray-500">
                                        Add all contact fields for this office location (Address, Tel, Fax, Mobile, etc.)
                                    </p>
                                </div>
                            </div>

                            <!-- Regular Label/Value Fields (for company_info and contact_info) -->
                            <template v-else>
                                <!-- Label -->
                                <div v-if="showFields.label">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ getFieldLabel('label') }} *
                                    </label>
                                    <input
                                        v-model="form.label"
                                        type="text"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        :placeholder="getPlaceholder('label')"
                                    />
                                    <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.label }}
                                    </p>
                                </div>

                                <!-- Value -->
                                <div v-if="showFields.value">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ getFieldLabel('value') }} *
                                    </label>
                                    <input
                                        v-model="form.value"
                                        type="text"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        :placeholder="getPlaceholder('value')"
                                    />
                                    <p v-if="form.errors.value" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.value }}
                                    </p>
                                </div>

                                <!-- Field Type -->
                                <div v-if="showFields.field_type">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ getFieldLabel('field_type') }} *
                                    </label>
                                    <select
                                        v-model="form.field_type"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    >
                                        <option value="">Select field type</option>
                                        <option v-for="option in fieldTypeOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.field_type" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.field_type }}
                                    </p>
                                </div>
                            </template>

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
                                href="/admin/corporate-profile"
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

