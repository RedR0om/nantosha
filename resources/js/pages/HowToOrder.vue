<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { CheckCircle, Package, FileText, CreditCard, Truck } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

// All translatable text
const texts = ref({
    title: 'How to Order',
    subtitle: '購入までの流れ',
    supportPolicy: 'Support Policy:',
    supportPolicyText: 'We handle only the sourcing and export procedures. Personal import is your responsibility, and we provide support throughout the process.',
    step1Title: 'Select Quantity',
    step1Text: 'Choose the quantity of Ivermectin capsules you need. We offer packages from 50 to 1,000 capsules with volume discounts.',
    step1Available: 'Available quantities:',
    step1Quantities: '50, 100, 200, 300, 500, or 1,000 capsules',
    step2Title: 'Enter Shipping Info & Health Check Sheet',
    step2Text: 'Provide your shipping address and complete the Health Check Sheet. This information is required for personal import procedures and to ensure safe usage.',
    step2Important: 'Important:',
    step2ImportantText: 'The Health Check Sheet helps the manufacturing doctor provide minimal advice based on your health information.',
    step3Title: 'Bank Transfer',
    step3Text: 'Complete payment via bank transfer. You will receive payment instructions after confirming your order.',
    step3Note: 'Payment must be completed before order processing begins.',
    step4Title: 'Order Processing & Shipping',
    step4Text: 'Once payment is confirmed, we process your order and ship it from the Philippines. Delivery typically takes 3–10 days.',
    step4Guarantee: 'Re-delivery Guarantee:',
    step4GuaranteeText: 'Free reshipment if the package is lost during transit.',
    step5Title: 'Receive Your Order',
    step5Text: 'Your order will be delivered to your specified address. Please ensure someone is available to receive the package.',
    importantNotes: 'Important Notes',
    risks: 'Risks:',
    risksText: 'Potential for wrong medication, incorrect dosage without a doctor, and customs delays.',
    prohibitions: 'Prohibitions:',
    prohibitionsText: 'Resale or transfer to third parties is strictly prohibited (except for doctors with proper Yakkan Shoumei permits).',
    doctors: 'Doctors:',
    doctorsText: 'Doctors purchasing for patient prescriptions require "Yakkan Shoumei" (Medicine Import Confirmation).',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    // Translate bidirectionally
    const keys = Object.keys(texts.value);
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            translated.value[key] = texts.value[key];
        }
    }
};

watch(language, translateAll, { immediate: true });
onMounted(translateAll);
</script>

<template>
    <Head title="How to Order - 購入までの流れ | Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    {{ translated.title || texts.title }}
                </h1>
                <p class="text-2xl text-gray-600">{{ translated.subtitle || texts.subtitle }}</p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded">
                <p class="text-gray-700">
                    <strong>{{ translated.supportPolicy || texts.supportPolicy }}</strong> {{ translated.supportPolicyText || texts.supportPolicyText }}
                </p>
            </div>

            <div class="space-y-8">
                <!-- Step 1 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">1</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <Package class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">{{ translated.step1Title || texts.step1Title }}</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ translated.step1Text || texts.step1Text }}
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>{{ translated.step1Available || texts.step1Available }}</strong> {{ translated.step1Quantities || texts.step1Quantities }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">2</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <FileText class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">{{ translated.step2Title || texts.step2Title }}</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ translated.step2Text || texts.step2Text }}
                        </p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-gray-700">
                                <strong>{{ translated.step2Important || texts.step2Important }}</strong> {{ translated.step2ImportantText || texts.step2ImportantText }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">3</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <CreditCard class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">{{ translated.step3Title || texts.step3Title }}</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ translated.step3Text || texts.step3Text }}
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">
                                {{ translated.step3Note || texts.step3Note }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">4</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <Truck class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">{{ translated.step4Title || texts.step4Title }}</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ translated.step4Text || texts.step4Text }}
                        </p>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-sm text-gray-700">
                                <strong>{{ translated.step4Guarantee || texts.step4Guarantee }}</strong> {{ translated.step4GuaranteeText || texts.step4GuaranteeText }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <CheckCircle class="w-8 h-8 text-green-600" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h2 class="text-2xl font-bold text-gray-900">{{ translated.step5Title || texts.step5Title }}</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            {{ translated.step5Text || texts.step5Text }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="mt-12 bg-red-50 border-l-4 border-red-500 p-6 rounded">
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ translated.importantNotes || texts.importantNotes }}</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>{{ translated.risks || texts.risks }}</strong> {{ translated.risksText || texts.risksText }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>{{ translated.prohibitions || texts.prohibitions }}</strong> {{ translated.prohibitionsText || texts.prohibitionsText }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>{{ translated.doctors || texts.doctors }}</strong> {{ translated.doctorsText || texts.doctorsText }}</span>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </div>
</template>

