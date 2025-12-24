<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

const texts = ref({
    title: 'Corporate Profile',
    subtitle: '会社概要',
    corporateProfile: 'Corporate Profile (Company Information)',
    companyName: 'Company Name',
    companyNameValue: 'Nantosha Co., Ltd.',
    ceo: 'CEO / Representative Director',
    ceoValue: 'Eiichiro Ishiyama',
    officeLocations: 'Office Locations',
    headquarters: 'Headquarters (Editorial & Publishing)',
    headquartersAddress: '1-2-10-102 Hakusan, Bunkyo-ku, Tokou 113-0001, Japan',
    phone: 'Phone',
    mobile: 'Mobile',
    fax: 'FAX',
    saitamaOffice: 'Saitama Office (Shipping, Import & Export)',
    saitamaAddress: 'Korpo Viage Bldg Ah, 5-47-13 Tsukagoshi, Warabishi, Saitama 335-0002, Japan',
    saitamaPhoneFax: 'Phone & FAX',
    manilaBureau: 'Manila Bureau (Philippines)',
    manilaAddress: 'Unit 2409 Royal Plaza, 638 Remedios St, Malate, Manila M.M, Philippines',
    tel: 'Tel',
    contactInformation: 'Contact Information',
    email: 'Email',
    address: 'Address',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    const keys = Object.keys(texts.value) as Array<keyof typeof texts.value>;
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            console.error(`Translation error for ${key}:`, error);
            translated.value[key] = texts.value[key];
        }
    }
};

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
                    <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">
                            {{ translated.corporateProfile || texts.corporateProfile }}
                        </h2>
                        
                        <div class="space-y-4">
                            <!-- Company Name -->
                            <div class="flex items-start gap-2">
                                <span class="text-gray-900 mt-1">•</span>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ translated.companyName || texts.companyName }}: </span>
                                    <span class="text-gray-600">{{ translated.companyNameValue || texts.companyNameValue }}</span>
                                </div>
                            </div>

                            <!-- CEO -->
                            <div class="flex items-start gap-2">
                                <span class="text-gray-900 mt-1">•</span>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ translated.ceo || texts.ceo }}: </span>
                                    <span class="text-gray-600">{{ translated.ceoValue || texts.ceoValue }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Locations -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            {{ translated.officeLocations || texts.officeLocations }}
                        </h3>
                        
                        <div class="space-y-8">
                            <!-- Headquarters -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                    {{ translated.headquarters || texts.headquarters }}
                                </h4>
                                <div class="space-y-2 text-gray-600">
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.address || texts.address }}:</span> {{ translated.headquartersAddress || texts.headquartersAddress }}</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.phone || texts.phone }}:</span> +81-3-6801-5561</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.mobile || texts.mobile }}:</span> +81-80-6260-0853 (Ishiyama)</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.fax || texts.fax }}:</span> +81-48-202-1322</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Saitama Office -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                    {{ translated.saitamaOffice || texts.saitamaOffice }}
                                </h4>
                                <div class="space-y-2 text-gray-600">
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.address || texts.address }}:</span> {{ translated.saitamaAddress || texts.saitamaAddress }}</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.saitamaPhoneFax || texts.saitamaPhoneFax }}:</span> +81-48-202-1322</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Manila Bureau -->
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                    {{ translated.manilaBureau || texts.manilaBureau }}
                                </h4>
                                <div class="space-y-2 text-gray-600">
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.address || texts.address }}:</span> {{ translated.manilaAddress || texts.manilaAddress }}</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="mt-1">•</span>
                                        <span><span class="font-medium">{{ translated.tel || texts.tel }}:</span> +63-912-484-1551</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ translated.contactInformation || texts.contactInformation }}
                        </h3>
                        <p class="flex items-start gap-2 text-gray-600">
                            <span class="mt-1">•</span>
                            <span><span class="font-medium">{{ translated.email || texts.email }}:</span> ishiyama@nantosha.com</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

