<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { Mail, Phone, MapPin, GraduationCap, Briefcase, Award, BookOpen } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

const texts = ref({
    title: 'Contact Us',
    subtitle: 'お問い合わせ',
    profile: 'Profile',
    drName: 'Dr. Allan Landrito',
    education: 'Education',
    educationText: 'Graduated from De La Salle University College of Medicine.',
    career: 'Career',
    careerText: 'Former Health Officer for Muntinlupa City. Currently Director of Allan Landrito Molecular Nutrition Clinic.',
    awards: 'Awards',
    award1: '2021 Outstanding Citizen & Humanitarian Award',
    award2: '2023 Modern Hero of Asia Award',
    award3: '2024 Outstanding Public Health Promoter Award',
    books: 'Books',
    booksText: '"COVID-19 Now: Prevention and Immediate Treatment" (2021).',
    getInTouch: 'Get in Touch',
    getInTouchText: 'Have questions about our Ivermectin capsules or need assistance with your order? We\'re here to help. Please contact us using the information below.',
    email: 'Email',
    emailResponse: 'We typically respond within 24 hours',
    phone: 'Phone',
    phoneHours: 'Business hours: Mon-Fri 9:00-18:00 JST',
    address: 'Address',
    forMedicalProfessionals: 'For Medical Professionals',
    forMedicalProfessionalsText: 'Doctors requiring "Yakkan Shoumei" (Medicine Import Confirmation) for patient prescriptions should contact us directly for special arrangements.',
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
    <Head title="Contact Us - お問い合わせ | Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    {{ translated.title || texts.title }}
                </h1>
                <p class="text-2xl text-gray-600">{{ translated.subtitle || texts.subtitle }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Dr. Allan Landrito Profile -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        {{ translated.profile || texts.profile }}
                    </h2>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            {{ translated.drName || texts.drName }}
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Education -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <GraduationCap class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ translated.education || texts.education }}</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ translated.educationText || texts.educationText }}
                                    </p>
                                </div>
                            </div>

                            <!-- Career -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <Briefcase class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ translated.career || texts.career }}</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ translated.careerText || texts.careerText }}
                                    </p>
                                </div>
                            </div>

                            <!-- Awards -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <Award class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ translated.awards || texts.awards }}</h4>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>{{ translated.award1 || texts.award1 }}</li>
                                        <li>{{ translated.award2 || texts.award2 }}</li>
                                        <li>{{ translated.award3 || texts.award3 }}</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Books -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <BookOpen class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ translated.books || texts.books }}</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ translated.booksText || texts.booksText }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        {{ translated.getInTouch || texts.getInTouch }}
                    </h2>
                    <p class="text-gray-600 mb-8">
                        {{ translated.getInTouchText || texts.getInTouchText }}
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <Mail class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">{{ translated.email || texts.email }}</h3>
                                <p class="text-gray-600">info@nantosha.com</p>
                                <p class="text-sm text-gray-500 mt-1">{{ translated.emailResponse || texts.emailResponse }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <Phone class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">{{ translated.phone || texts.phone }}</h3>
                                <p class="text-gray-600">+81-XX-XXXX-XXXX</p>
                                <p class="text-sm text-gray-500 mt-1">{{ translated.phoneHours || texts.phoneHours }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <MapPin class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">{{ translated.address || texts.address }}</h3>
                                <p class="text-gray-600">
                                    Nantosha Import & Export Division<br>
                                    南東舎輸出入部<br>
                                    Tokyo, Japan
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            {{ translated.forMedicalProfessionals || texts.forMedicalProfessionals }}
                        </h3>
                        <p class="text-sm text-gray-700">
                            {{ translated.forMedicalProfessionalsText || texts.forMedicalProfessionalsText }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

