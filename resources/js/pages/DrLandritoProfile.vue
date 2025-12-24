<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { GraduationCap, Briefcase, Award, BookOpen, CreditCard, X } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

// Image modal state
const selectedImage = ref<string | null>(null);
const selectedImageAlt = ref<string>('');

const openImageModal = (imageUrl: string, alt: string) => {
    selectedImage.value = imageUrl;
    selectedImageAlt.value = alt;
};

const closeImageModal = () => {
    selectedImage.value = null;
    selectedImageAlt.value = '';
};

const texts = ref({
    title: 'Dr. Allan Landrito',
    subtitle: 'アラン・ランドリト医師',
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
    publication1: '"COVID-19 Liberation Now!"',
    publication2: '"Ivermectin: Testimony from the World\'s Clinicians"',
    publication2Role: 'Co-author',
    forMedicalProfessionals: 'For Medical Professionals',
    forMedicalProfessionalsText: 'Doctors requiring "Yakkan Shoumei" (Medicine Import Confirmation) for patient prescriptions should contact us directly for special arrangements.',
    bankAccountInfo: 'Bank Account Information',
    bankAccount1: 'GMO Aozora Net Bank',
    bankAccount1Details: 'Corporate Sales Dept. II | Ordinary (普) 1715362 | Name: カブシキガイシャナントウシャ (Nantosha Co., Ltd.)',
    bankAccount2: 'MUFG Bank',
    bankAccount2Details: 'Toranomon Branch | Ordinary (普) 1749447 | Name: イシヤマエイイチロウ (Eiichiro Ishiyama)',
    photos: 'Photos',
    achievements: 'Achievements & Recognition',
    clinicalPractice: 'Clinical Practice',
    clinicalPracticeText: 'Dr. Landrito has been practicing integrated medicine for over 15 years, combining Chelation, DMSO, Nutritional Medicine, Orthomolecular Medicine, and Herbal Therapy. During the COVID-19 pandemic, he provided free online consultations via Zoom, guiding the treatment of over 100,000 patients using Ivermectin as the primary agent.',
    currentFocus: 'Current Focus',
    currentFocusText: 'Since 2022, Dr. Landrito has focused on using Ivermectin for vaccine injuries, cancer, and autoimmune diseases. He now manufactures his own high-purity Ivermectin (over 99% pure) at his Molecular Nutrition Clinic.',
    publications: 'Publications',
    newsletterIberu: 'Newsletter "IBERU"',
    newsletterIberuText: '"IBERU" (Issue 1) – An alternative medicine email newsletter published every other month by Nantosha for crowdfunding supporters.',
    photo1Caption: 'Dr. Allan A. Landrito',
    photo1Description: 'Dr. Landrito earned his medical degree from De La Salle University in 1991. At the start of the COVID-19 pandemic, he was a primary healthcare physician for the Muntinlupa City Health Department. For 15 years prior, he practiced integrated approaches including Chelation, DMSO, Nutritional Medicine, Orthomolecular Medicine, and Herbal Therapy. During the pandemic, he provided free online consultations via Zoom, guiding the treatment of over 100,000 patients using Ivermectin as the primary agent. He is the author of "COVID-19 Liberation Now!" and co-author of "Ivermectin: Testimony from the World\'s Clinicians". Since 2022, he has focused on using Ivermectin for vaccine injuries, cancer, and autoimmune diseases, and now manufactures his own high-purity Ivermectin.',
    photo2Caption: 'Dr. Landrito showcasing his self-manufactured Ivermectin',
    photo2Description: 'Dr. Landrito showcasing his self-manufactured Ivermectin at his Molecular Nutrition Clinic.',
    photo3Caption: 'Patients visiting Dr. Landrito\'s clinic',
    photo3Description: 'Patients visiting Dr. Landrito\'s clinic for cancer treatments combining Ivermectin and Vitamin C.',
    photo4Caption: 'Dr. Landrito meets Dr. Satoshi Omura',
    photo4Description: 'In September 2025, during his visit to Japan, Dr. Landrito met with Dr. Satoshi Omura (2015 Nobel Laureate) at Kitasato University. Dr. Omura acknowledged Dr. Landrito\'s Ivermectin as being over 99% pure.',
    aboutCustoms: 'About Customs',
    aboutCustomsText: 'After shipping from the Philippines, the package will typically be delivered directly to your home. In rare cases, customs may contact you. Please state that it is for "personal use". Personal import of medicine is legal under Article 12 of the PMDA Act. If customs requests any documentation, please contact us. We will prepare the necessary documents at our expense.',
    aboutCustomsNote: 'Please note that you may be required by Japanese customs to pay an additional import tax (approx. 5% of the total amount paid). This tax is the responsibility of the recipient.',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    const keys = Object.keys(texts.value) as Array<keyof typeof texts.value>;
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
    <Head title="Dr. Allan Landrito - アラン・ランドリト医師 | Nantosha Import & Export Division" />
    
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

                <!-- Dr. Allan Landrito Portfolio -->
                <div class="max-w-4xl mx-auto">
                <!-- Profile Header with Photo -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                        <!-- Dr. Landrito Photo -->
                        <div>
                            <button
                                @click="openImageModal('https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544901/photo-1_fuooyw.jpg', 'Dr. Allan A. Landrito')"
                                class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                            >
                                <img
                                    src="https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544901/photo-1_fuooyw.jpg"
                                    alt="Dr. Allan A. Landrito"
                                    class="w-full rounded-lg object-cover"
                                />
                            </button>
                        </div>
                        <!-- Profile Info -->
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                {{ translated.drName || texts.drName }}
                            </h2>
                            <p class="text-lg text-gray-600 mb-6">
                                {{ translated.profile || texts.profile }}
                            </p>
                            
                            <div class="space-y-4">
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
                            </div>
                        </div>
                    </div>
                    
                    <!-- Photo 1 Description -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            {{ translated.photo1Caption || texts.photo1Caption }}
                        </h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ translated.photo1Description || texts.photo1Description }}
                        </p>
                    </div>
                </div>

                <!-- About Customs -->
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        {{ translated.aboutCustoms || texts.aboutCustoms }}
                    </h2>
                    <div class="space-y-4 text-gray-700">
                        <p>
                            {{ translated.aboutCustomsText || texts.aboutCustomsText }}
                        </p>
                        <p>
                            {{ translated.aboutCustomsNote || texts.aboutCustomsNote }}
                        </p>
                    </div>
                </div>

                <!-- Clinical Practice -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <Briefcase class="w-6 h-6 text-blue-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.clinicalPractice || texts.clinicalPractice }}
                        </h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        {{ translated.clinicalPracticeText || texts.clinicalPracticeText }}
                    </p>
                </div>

                <!-- Current Focus -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <Award class="w-6 h-6 text-green-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.currentFocus || texts.currentFocus }}
                        </h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        {{ translated.currentFocusText || texts.currentFocusText }}
                    </p>
                </div>

                <!-- Awards -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <Award class="w-6 h-6 text-yellow-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.awards || texts.awards }}
                        </h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="text-yellow-600 mt-1">•</span>
                            <span class="text-gray-600">{{ translated.award1 || texts.award1 }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-yellow-600 mt-1">•</span>
                            <span class="text-gray-600">{{ translated.award2 || texts.award2 }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-yellow-600 mt-1">•</span>
                            <span class="text-gray-600">{{ translated.award3 || texts.award3 }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Publications -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <BookOpen class="w-6 h-6 text-purple-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.publications || texts.publications }}
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <p class="text-gray-600">
                            {{ translated.booksText || texts.booksText }}
                        </p>
                        <p class="text-gray-600">
                            {{ translated.publication1 || texts.publication1 }} - {{ translated.drName || texts.drName }}
                        </p>
                        <p class="text-gray-600">
                            {{ translated.publication2 || texts.publication2 }} - {{ translated.publication2Role || texts.publication2Role }}
                        </p>
                    </div>
                </div>

                <!-- Bank Account Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <CreditCard class="w-6 h-6 text-gray-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.bankAccountInfo || texts.bankAccountInfo }}
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ translated.bankAccount1 || texts.bankAccount1 }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ translated.bankAccount1Details || texts.bankAccount1Details }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ translated.bankAccount2 || texts.bankAccount2 }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ translated.bankAccount2Details || texts.bankAccount2Details }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- For Medical Professionals -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
                    <h3 class="font-semibold text-gray-900 mb-2">
                        {{ translated.forMedicalProfessionals || texts.forMedicalProfessionals }}
                    </h3>
                    <p class="text-sm text-gray-700">
                        {{ translated.forMedicalProfessionalsText || texts.forMedicalProfessionalsText }}
                    </p>
                </div>

                <!-- Photo Gallery Section -->
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">
                        {{ translated.photos || texts.photos }}
                    </h2>

                    <!-- Newsletter IBERU -->
                    <div class="mb-12 bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ translated.newsletterIberu || texts.newsletterIberu }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ translated.newsletterIberuText || texts.newsletterIberuText }}
                        </p>
                    </div>

                    <!-- Photo Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Photo 2 -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            @click="openImageModal('https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-2_qwimeq.jpg', 'Dr. Landrito showcasing Ivermectin')"
                            class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                        >
                            <img
                                src="https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-2_qwimeq.jpg"
                                alt="Dr. Landrito showcasing Ivermectin"
                                class="w-full h-64 object-cover"
                            />
                        </button>
                        <div class="p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ translated.photo2Caption || texts.photo2Caption }}
                            </h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ translated.photo2Description || texts.photo2Description }}
                            </p>
                        </div>
                    </div>

                    <!-- Photo 3 -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            @click="openImageModal('https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-3_ocyvqa.jpg', 'Patients at Dr. Landrito\'s clinic')"
                            class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                        >
                            <img
                                src="https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-3_ocyvqa.jpg"
                                alt="Patients at Dr. Landrito's clinic"
                                class="w-full h-64 object-cover"
                            />
                        </button>
                        <div class="p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ translated.photo3Caption || texts.photo3Caption }}
                            </h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ translated.photo3Description || texts.photo3Description }}
                            </p>
                        </div>
                    </div>

                    <!-- Photo 4 -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <button
                            @click="openImageModal('https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-4_abw66r.jpg', 'Dr. Landrito meets Dr. Satoshi Omura')"
                            class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                        >
                            <img
                                src="https://res.cloudinary.com/dkcjftn5c/image/upload/v1766544902/photo-4_abw66r.jpg"
                                alt="Dr. Landrito meets Dr. Satoshi Omura"
                                class="w-full h-64 object-cover"
                            />
                        </button>
                        <div class="p-6">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                {{ translated.photo4Caption || texts.photo4Caption }}
                            </h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ translated.photo4Description || texts.photo4Description }}
                            </p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div
            v-if="selectedImage"
            @click="closeImageModal"
            class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
        >
            <div class="relative max-w-5xl max-h-[90vh] w-full">
                <button
                    @click="closeImageModal"
                    class="absolute top-4 right-4 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors z-10"
                >
                    <X class="w-6 h-6 text-gray-900" />
                </button>
                <img
                    :src="selectedImage"
                    :alt="selectedImageAlt"
                    class="w-full h-auto max-h-[90vh] object-contain rounded-lg"
                    @click.stop
                />
            </div>
        </div>
    </div>
</template>

