<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft, Plus, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted } from 'vue';

const props = defineProps<{
    content: {
        id: number;
        section_type: string;
        key: string | null;
        title: string | null;
        content: string | null;
        description: string | null;
        sort_order: number;
        is_active: boolean;
    };
    allItems?: Array<{
        id: number;
        key: string | null;
        title: string | null;
        content: string | null;
        sort_order: number;
    }> | null;
}>();

const sectionTypes = [
    { value: 'risks_prohibitions', label: 'Risks & Prohibitions', description: 'Risks and prohibitions information' },
    { value: 'shipping_customs', label: 'Shipping & Customs Information', description: 'Shipping and customs information' },
];

const form = useForm({
    section_type: props.content.section_type,
    key: props.content.key || '',
    title: props.content.title || '',
    content: props.content.content || '',
    description: props.content.description || '',
    sort_order: props.content.sort_order,
    is_active: props.content.is_active,
});

// For risks_prohibitions: multiple items (risks, prohibitions, acknowledgments)
interface RiskProhibitionItem {
    id?: number;
    key: string;
    title: string;
    content: string;
    sort_order: number;
}

const riskItems = ref<RiskProhibitionItem[]>([]);
const prohibitionItems = ref<RiskProhibitionItem[]>([]);
const acknowledgmentItems = ref<RiskProhibitionItem[]>([]);

// Load existing items if editing risks_prohibitions
onMounted(() => {
    if (props.content.section_type === 'risks_prohibitions' && props.allItems) {
        props.allItems.forEach((item) => {
            if (item.key?.startsWith('risk_') && item.key !== 'risks_title') {
                riskItems.value.push({
                    id: item.id,
                    key: item.key || '',
                    title: item.title || '',
                    content: item.content || '',
                    sort_order: item.sort_order,
                });
            } else if (item.key?.startsWith('prohibition_') && item.key !== 'prohibitions_title') {
                prohibitionItems.value.push({
                    id: item.id,
                    key: item.key || '',
                    title: item.title || '',
                    content: item.content || '',
                    sort_order: item.sort_order,
                });
            } else if (item.key === 'risk_acknowledgment' || item.key === 'resale_prohibited') {
                acknowledgmentItems.value.push({
                    id: item.id,
                    key: item.key || '',
                    title: item.title || '',
                    content: item.content || '',
                    sort_order: item.sort_order,
                });
            }
        });
    }
});

const addRiskItem = () => {
    riskItems.value.push({
        key: `risk_${riskItems.value.length + 1}`,
        title: '',
        content: '',
        sort_order: riskItems.value.length,
    });
};

const removeRiskItem = (index: number) => {
    riskItems.value.splice(index, 1);
    riskItems.value.forEach((item, idx) => {
        item.sort_order = idx;
        item.key = `risk_${idx + 1}`;
    });
};

const addProhibitionItem = () => {
    prohibitionItems.value.push({
        key: `prohibition_${prohibitionItems.value.length + 1}`,
        title: '',
        content: '',
        sort_order: prohibitionItems.value.length,
    });
};

const removeProhibitionItem = (index: number) => {
    prohibitionItems.value.splice(index, 1);
    prohibitionItems.value.forEach((item, idx) => {
        item.sort_order = idx;
        item.key = `prohibition_${idx + 1}`;
    });
};

const addAcknowledgmentItem = () => {
    acknowledgmentItems.value.push({
        key: acknowledgmentItems.value.length === 0 ? 'risk_acknowledgment' : 'resale_prohibited',
        title: '',
        content: '',
        sort_order: acknowledgmentItems.value.length,
    });
};

const removeAcknowledgmentItem = (index: number) => {
    acknowledgmentItems.value.splice(index, 1);
    acknowledgmentItems.value.forEach((item, idx) => {
        item.sort_order = idx;
        item.key = idx === 0 ? 'risk_acknowledgment' : 'resale_prohibited';
    });
};

const submit = () => {
    // If risks_prohibitions with multiple items, submit all at once
    if (form.section_type === 'risks_prohibitions') {
        const allItems: any[] = [];
        
        // Add main title (if it exists, keep it; otherwise create it)
        const titleItem = props.allItems?.find(item => item.key === 'title');
        if (titleItem) {
            allItems.push({
                id: titleItem.id,
                key: 'title',
                title: 'Risks & Prohibitions',
                content: '',
                sort_order: 0,
            });
        } else {
            allItems.push({
                key: 'title',
                title: 'Risks & Prohibitions',
                content: '',
                sort_order: 0,
            });
        }
        
        // Add Risks section title
        if (riskItems.value.length > 0) {
            const risksTitleItem = props.allItems?.find(item => item.key === 'risks_title');
            allItems.push({
                id: risksTitleItem?.id,
                key: 'risks_title',
                title: 'Risks:',
                content: '',
                sort_order: 1,
            });
        }
        
        // Add risk items
        riskItems.value.forEach((item, index) => {
            if (item.content) {
                allItems.push({
                    id: item.id,
                    key: item.key,
                    title: '',
                    content: item.content,
                    sort_order: 2 + index,
                });
            }
        });
        
        // Add Prohibitions section title
        if (prohibitionItems.value.length > 0) {
            const prohibitionsTitleItem = props.allItems?.find(item => item.key === 'prohibitions_title');
            allItems.push({
                id: prohibitionsTitleItem?.id,
                key: 'prohibitions_title',
                title: 'Prohibitions:',
                content: '',
                sort_order: 2 + riskItems.value.length,
            });
        }
        
        // Add prohibition items
        prohibitionItems.value.forEach((item, index) => {
            if (item.content) {
                allItems.push({
                    id: item.id,
                    key: item.key,
                    title: '',
                    content: item.content,
                    sort_order: 3 + riskItems.value.length + index,
                });
            }
        });
        
        // Add acknowledgment items
        acknowledgmentItems.value.forEach((item, index) => {
            if (item.content) {
                allItems.push({
                    id: item.id,
                    key: item.key,
                    title: '',
                    content: item.content,
                    sort_order: 3 + riskItems.value.length + prohibitionItems.value.length + index,
                });
            }
        });
        
        // Get IDs of existing items to potentially delete
        const existingIds = props.allItems?.map(item => item.id) || [];
        const newIds = allItems.filter(item => item.id).map(item => item.id);
        const deleteIds = existingIds.filter(id => !newIds.includes(id));
        
        // Create bulk update form
        const bulkForm = useForm({
            section_type: 'risks_prohibitions',
            sort_order: form.sort_order,
            is_active: form.is_active,
            items: allItems,
            delete_ids: deleteIds,
        });
        
        bulkForm.put(`/admin/checkout-content/${props.content.id}`, {
            preserveScroll: true,
        });
    } else {
        // Regular single entry submission
        form.put(`/admin/checkout-content/${props.content.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Edit Checkout Content - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        href="/admin/checkout-content"
                        class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium mb-4"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Checkout Content
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Content</h1>
                    <p class="text-sm text-gray-600 mt-1">Edit content for the Checkout page</p>
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
                            </div>

                            <!-- Risks & Prohibitions: Multiple Items -->
                            <div v-if="form.section_type === 'risks_prohibitions'">
                                <div class="space-y-6">
                                    <!-- Risks Section -->
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Risks *
                                            </label>
                                            <button
                                                type="button"
                                                @click="addRiskItem"
                                                class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                            >
                                                <Plus class="w-4 h-4" />
                                                Add Risk
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div
                                                v-for="(item, index) in riskItems"
                                                :key="index"
                                                class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                            >
                                                <div class="flex items-start justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-700">Risk {{ index + 1 }}</span>
                                                    <button
                                                        type="button"
                                                        @click="removeRiskItem(index)"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
                                                    >
                                                        <X class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Risk Description *
                                                    </label>
                                                    <textarea
                                                        v-model="item.content"
                                                        rows="2"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        placeholder="e.g., Potential for wrong medication"
                                                    ></textarea>
                                                </div>
                                            </div>
                                            
                                            <p v-if="riskItems.length === 0" class="text-xs text-gray-500 italic">
                                                Click "Add Risk" to add risk items
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Prohibitions Section -->
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Prohibitions *
                                            </label>
                                            <button
                                                type="button"
                                                @click="addProhibitionItem"
                                                class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                            >
                                                <Plus class="w-4 h-4" />
                                                Add Prohibition
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div
                                                v-for="(item, index) in prohibitionItems"
                                                :key="index"
                                                class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                            >
                                                <div class="flex items-start justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-700">Prohibition {{ index + 1 }}</span>
                                                    <button
                                                        type="button"
                                                        @click="removeProhibitionItem(index)"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
                                                    >
                                                        <X class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Prohibition Description *
                                                    </label>
                                                    <textarea
                                                        v-model="item.content"
                                                        rows="2"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        placeholder="e.g., Resale or transfer to third parties is strictly prohibited"
                                                    ></textarea>
                                                </div>
                                            </div>
                                            
                                            <p v-if="prohibitionItems.length === 0" class="text-xs text-gray-500 italic">
                                                Click "Add Prohibition" to add prohibition items
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Acknowledgment Checkboxes Section -->
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Acknowledgment Checkboxes *
                                            </label>
                                            <button
                                                type="button"
                                                @click="addAcknowledgmentItem"
                                                class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                            >
                                                <Plus class="w-4 h-4" />
                                                Add Acknowledgment
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div
                                                v-for="(item, index) in acknowledgmentItems"
                                                :key="index"
                                                class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                            >
                                                <div class="flex items-start justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        Acknowledgment {{ index + 1 }}
                                                        <span class="text-xs text-gray-500 ml-2">
                                                            ({{ item.key === 'risk_acknowledgment' ? 'Risk Acknowledgment' : 'Resale Prohibited' }})
                                                        </span>
                                                    </span>
                                                    <button
                                                        v-if="acknowledgmentItems.length > 1"
                                                        type="button"
                                                        @click="removeAcknowledgmentItem(index)"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors"
                                                    >
                                                        <X class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Checkbox Text *
                                                    </label>
                                                    <textarea
                                                        v-model="item.content"
                                                        rows="2"
                                                        required
                                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                                        :placeholder="item.key === 'risk_acknowledgment' ? 'e.g., I acknowledge the risks of self-medication and understand that I am responsible for my personal import. *' : 'e.g., I understand that resale or transfer to third parties is strictly prohibited. *'"
                                                    ></textarea>
                                                </div>
                                            </div>
                                            
                                            <p v-if="acknowledgmentItems.length === 0" class="text-xs text-gray-500 italic">
                                                Click "Add Acknowledgment" to add acknowledgment checkboxes (typically 2: Risk Acknowledgment and Resale Prohibited)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping & Customs: Regular Form -->
                            <template v-else>
                                <!-- Key -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Identifier Key (Optional)
                                    </label>
                                    <input
                                        v-model="form.key"
                                        type="text"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        placeholder="e.g., shipping_info_1"
                                    />
                                    <p v-if="form.errors.key" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.key }}
                                    </p>
                                </div>

                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Title (Optional)
                                    </label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        placeholder="e.g., Shipping & Customs Information"
                                    />
                                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <!-- Content -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Content *
                                    </label>
                                    <textarea
                                        v-model="form.content"
                                        rows="6"
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        placeholder="Enter the main content text"
                                    ></textarea>
                                    <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.content }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Description (Optional)
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="4"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        placeholder="Enter additional description or notes"
                                    ></textarea>
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.description }}
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
                        </div>

                        <div class="mt-6 flex justify-end gap-4">
                            <Link
                                href="/admin/checkout-content"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Content</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

