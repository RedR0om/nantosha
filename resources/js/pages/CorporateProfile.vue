<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const props = defineProps<{
    contents?: Record<string, any[]>;
}>();

const { language } = useLanguage();

// Helper function to get content by section type and key
const getContent = (sectionType: string, key?: string) => {
    if (!props.contents || !props.contents[sectionType]) return null;
    const sectionContents = props.contents[sectionType];
    if (key) {
        return sectionContents.find(c => c.key === key) || sectionContents[0] || null;
    }
    return sectionContents[0] || null;
};

// Helper function to get all content by section type
const getContents = (sectionType: string) => {
    if (!props.contents || !props.contents[sectionType]) return [];
    return props.contents[sectionType];
};

// Get content values with fallback
const getContentValue = (content: any, field: string, fallback: string = '') => {
    if (!content) return fallback;
    return content[field] || fallback;
};

// Build texts object from dynamic content
const buildTexts = () => {
    const texts: Record<string, string> = {};
    
    // Page title and subtitle
    const pageTitle = getContent('page_title', 'title');
    const pageSubtitle = getContent('page_title', 'subtitle');
    texts.title = getContentValue(pageTitle, 'title', 'Corporate Profile');
    texts.subtitle = getContentValue(pageSubtitle, 'title', '会社概要');
    
    // Corporate Profile section
    texts.corporateProfile = 'Corporate Profile (Company Information)';
    
    // Company Info
    const companyName = getContent('company_info', 'company_name');
    texts.companyName = getContentValue(companyName, 'label', 'Company Name');
    texts.companyNameValue = getContentValue(companyName, 'value', 'Nantosha Co., Ltd.');
    
    const ceo = getContent('company_info', 'ceo');
    texts.ceo = getContentValue(ceo, 'label', 'CEO / Representative Director');
    texts.ceoValue = getContentValue(ceo, 'value', 'Eiichiro Ishiyama (石山永一郎)');
    
    // Office Locations
    texts.officeLocations = 'Office Locations';
    
    // Contact Information
    texts.contactInformation = 'Contact Information';
    texts.email = 'Email';
    texts.address = 'Address';
    texts.phone = 'Tel';
    texts.mobile = 'Mobile';
    texts.fax = 'Fax';
    
    return texts;
};

const texts = ref(buildTexts());
const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    const keys = Object.keys(texts.value) as Array<keyof typeof texts.value>;
    for (const key of keys) {
        if (texts.value[key]) {
            try {
                translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
            } catch (error) {
                translated.value[key] = texts.value[key];
            }
        }
    }
};

// Rebuild texts when contents change
watch(() => props.contents, () => {
    texts.value = buildTexts();
    translateAll();
}, { deep: true });

watch(language, translateAll, { immediate: true });
onMounted(translateAll);
</script>

<template>
    <Head title="Corporate Profile - 会社概要 | Nantosha Import & Export Division" />

    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">
                            {{ translated.title || texts.title }}
                        </h1>
                        <p class="text-lg text-gray-600">
                            {{ translated.subtitle || texts.subtitle }}
                        </p>
                    </div>

                    <!-- Corporate Profile -->
                    <div v-if="getContents('company_info').length > 0" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">
                            {{ translated.corporateProfile || texts.corporateProfile }}
                        </h2>
                        
                        <div class="space-y-4">
                            <div
                                v-for="info in getContents('company_info')"
                                :key="info.id"
                                class="flex items-start gap-2"
                            >
                                <span class="text-gray-900 mt-1">•</span>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ info.label || '' }}: </span>
                                    <span class="text-gray-600">{{ info.value || '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Locations -->
                    <div v-if="getContents('office_location').length > 0" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            {{ translated.officeLocations || texts.officeLocations }}
                        </h3>
                        
                        <div class="space-y-8">
                            <!-- Group office locations by key (head_office, saitama, manila) -->
                            <div
                                v-for="officeKey in ['head_office', 'saitama', 'manila']"
                                :key="officeKey"
                            >
                                <div v-if="getContents('office_location').some(c => c.key === officeKey)">
                                    <!-- Office Title (first item with this key) -->
                                    <h4
                                        v-if="getContents('office_location').find(c => c.key === officeKey && c.title)"
                                        class="text-lg font-semibold text-gray-900 mb-4"
                                    >
                                        {{ getContents('office_location').find(c => c.key === officeKey && c.title)?.title }}
                                    </h4>
                                    
                                    <div class="space-y-2 text-gray-600">
                                        <p
                                            v-for="location in getContents('office_location').filter(c => c.key === officeKey)"
                                            :key="location.id"
                                            class="flex items-start gap-2"
                                        >
                                            <span class="mt-1">•</span>
                                            <span>
                                                <span class="font-medium">{{ location.label || '' }}:</span>
                                                {{ location.value || '' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div v-if="getContents('contact_info').length > 0" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ translated.contactInformation || texts.contactInformation }}
                        </h3>
                        <div class="space-y-2">
                            <p
                                v-for="contact in getContents('contact_info')"
                                :key="contact.id"
                                class="flex items-start gap-2 text-gray-600"
                            >
                                <span class="mt-1">•</span>
                                <span>
                                    <span class="font-medium">{{ contact.label || '' }}:</span>
                                    {{ contact.value || '' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
