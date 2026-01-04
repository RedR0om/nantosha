<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { GraduationCap, Briefcase, Award, BookOpen, X } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const props = defineProps<{
    contents?: Record<string, any[]>;
}>();

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
    texts.title = getContentValue(pageTitle, 'title', 'Dr. Allan Landrito');
    texts.subtitle = getContentValue(pageSubtitle, 'title', 'アラン・ランドリト医師');
    
    // Profile header
    const profileHeader = getContent('profile_header');
    texts.drName = getContentValue(profileHeader, 'title', 'Dr. Allan Landrito');
    texts.profile = getContentValue(profileHeader, 'subtitle', 'Profile');
    
    // Education
    const education = getContent('education');
    texts.education = getContentValue(education, 'title', 'Education');
    texts.educationText = getContentValue(education, 'text', 'Graduated from De La Salle University College of Medicine.');
    
    // Career
    const career = getContent('career');
    texts.career = getContentValue(career, 'title', 'Career');
    texts.careerText = getContentValue(career, 'text', 'Former Health Officer for Muntinlupa City. Currently Director of Allan Landrito Molecular Nutrition Clinic.');
    
    // Clinical Practice
    const clinicalPractice = getContent('clinical_practice');
    texts.clinicalPractice = getContentValue(clinicalPractice, 'title', 'Clinical Practice');
    texts.clinicalPracticeText = getContentValue(clinicalPractice, 'text', 'Dr. Landrito has been practicing integrated medicine for over 15 years...');
    
    // Current Focus
    const currentFocus = getContent('current_focus');
    texts.currentFocus = getContentValue(currentFocus, 'title', 'Current Focus');
    texts.currentFocusText = getContentValue(currentFocus, 'text', 'Since 2022, Dr. Landrito has focused on using Ivermectin...');
    
    // Awards
    const awards = getContents('award');
    texts.awards = 'Awards';
    if (awards.length > 0) {
        awards.forEach((award, index) => {
            const awardText = getContentValue(award, 'title', '') || getContentValue(award, 'text', '');
            if (awardText) {
                texts[`award${index + 1}`] = awardText;
            }
        });
    }
    
    // Publications
    const publications = getContents('publication');
    texts.publications = 'Publications';
    if (publications.length > 0) {
        publications.forEach((pub, index) => {
            const pubText = getContentValue(pub, 'title', '') || getContentValue(pub, 'text', '');
            if (pubText) {
                texts[`publication${index + 1}`] = pubText;
            }
        });
    }
    
    // Medical Professionals
    const medicalProf = getContent('medical_professionals');
    texts.forMedicalProfessionals = getContentValue(medicalProf, 'title', 'For Medical Professionals');
    texts.forMedicalProfessionalsText = getContentValue(medicalProf, 'text', 'Doctors requiring "Yakkan Shoumei"...');
    
    // Photos
    texts.photos = 'Photos';
    
    // Photo captions and descriptions
    const photos = getContents('photo');
    photos.forEach((photo) => {
        const photoKey = photo.key || `photo${photo.id}`;
        if (photo.caption) {
            texts[`${photoKey}_caption`] = photo.caption;
        }
        if (photo.description) {
            texts[`${photoKey}_description`] = photo.description;
        }
    });
    
    // Newsletter
    const newsletter = getContent('newsletter_info');
    texts.newsletterIberu = getContentValue(newsletter, 'title', 'Newsletter "IBERU"');
    texts.newsletterIberuText = getContentValue(newsletter, 'text', '"IBERU" (Issue 1)...');
    
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
                <div v-if="getContent('profile_header')" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                        <!-- Dr. Landrito Photo -->
                        <div v-if="getContent('photo', 'photo1')?.image_url">
                            <button
                                @click="openImageModal(getContent('photo', 'photo1')!.image_url!, getContent('photo', 'photo1')!.image_alt || '')"
                                class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                            >
                                <img
                                    :src="getContent('photo', 'photo1')!.image_url"
                                    :alt="getContent('photo', 'photo1')!.image_alt || ''"
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
                                <div v-if="getContent('education')" class="flex items-start gap-4">
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
                                <div v-if="getContent('career')" class="flex items-start gap-4">
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
                    <div v-if="getContent('photo', 'photo1')" class="border-t border-gray-200 pt-6">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            {{ translated[`${getContent('photo', 'photo1')!.key || 'photo1'}_caption`] || getContent('photo', 'photo1')!.caption || getContent('photo', 'photo1')!.title || '' }}
                        </h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ translated[`${getContent('photo', 'photo1')!.key || 'photo1'}_description`] || getContent('photo', 'photo1')!.description || '' }}
                        </p>
                    </div>
                </div>

                <!-- Clinical Practice -->
                <div v-if="getContent('clinical_practice')" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
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
                <div v-if="getContent('current_focus')" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
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
                <div v-if="getContents('award').length > 0" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <Award class="w-6 h-6 text-yellow-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.awards || texts.awards }}
                        </h3>
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(award, index) in getContents('award')" :key="award.id" class="flex items-start gap-3">
                            <span class="text-yellow-600 mt-1">•</span>
                            <span class="text-gray-600">{{ translated[`award${index + 1}`] || award.title || award.text || '' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Publications -->
                <div v-if="getContents('publication').length > 0" class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <BookOpen class="w-6 h-6 text-purple-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ translated.publications || texts.publications }}
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <p v-for="(publication, index) in getContents('publication')" :key="publication.id" class="text-gray-600">
                            {{ translated[`publication${index + 1}`] || publication.title || publication.text || '' }}
                        </p>
                    </div>
                </div>

                <!-- For Medical Professionals -->
                <div v-if="getContent('medical_professionals')" class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
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
                    <div v-if="getContent('newsletter_info')" class="mb-12 bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ translated.newsletterIberu || texts.newsletterIberu }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ translated.newsletterIberuText || texts.newsletterIberuText }}
                        </p>
                    </div>

                    <!-- Photo Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div
                            v-for="photo in getContents('photo').filter(p => p.key !== 'photo1')"
                            :key="photo.id"
                            class="bg-white border border-gray-200 rounded-lg overflow-hidden"
                        >
                            <button
                                v-if="photo.image_url"
                                @click="openImageModal(photo.image_url, photo.image_alt || '')"
                                class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                            >
                                <img
                                    :src="photo.image_url"
                                    :alt="photo.image_alt || ''"
                                    class="w-full h-64 object-cover"
                                />
                            </button>
                            <div class="p-6">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    {{ translated[`${photo.key || `photo${photo.id}`}_caption`] || photo.caption || photo.title || '' }}
                                </h4>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ translated[`${photo.key || `photo${photo.id}`}_description`] || photo.description || '' }}
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
