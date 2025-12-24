<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

const props = defineProps<{
    faqs: Array<{
        id: number;
        question: string;
        question_ja?: string;
        answer: string;
    }>;
}>();

const openFaqs = ref<number[]>([]);

const toggleFaq = (id: number) => {
    if (openFaqs.value.includes(id)) {
        openFaqs.value = openFaqs.value.filter(i => i !== id);
    } else {
        openFaqs.value.push(id);
    }
};

// Translate static text
const texts = ref({
    title: 'Frequently Asked Questions',
    subtitle: 'よくある質問',
    stillHaveQuestions: 'Still have questions?',
    stillHaveQuestionsText: 'If you have additional questions, please don\'t hesitate to contact us.',
    contactUs: 'Contact Us',
});

const translated = ref<Record<string, string>>({});
const translatedFaqs = ref<Array<{id: number; question: string; answer: string}>>([]);

const translateAll = async () => {
    // Translate static text (bidirectional)
    const keys = Object.keys(texts.value);
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            translated.value[key] = texts.value[key];
        }
    }

    // Translate FAQs (bidirectional)
    translatedFaqs.value = await Promise.all(
        props.faqs.map(async (faq) => ({
            id: faq.id,
            question: await translateText(faq.question, language.value, 'auto'),
            answer: await translateText(faq.answer, language.value, 'auto'),
        }))
    );
};

watch(language, translateAll, { immediate: true });
watch(() => props.faqs, translateAll, { deep: true });
onMounted(translateAll);
</script>

<template>
    <Head title="FAQ - よくある質問 | Nantosha Import & Export Division" />
    
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

            <div class="space-y-4">
                <div
                    v-for="faq in (translatedFaqs.length > 0 ? translatedFaqs : faqs)"
                    :key="faq.id"
                    class="border border-gray-200 rounded-lg overflow-hidden"
                >
                    <button
                        @click="toggleFaq(faq.id)"
                        class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition"
                    >
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1">
                                {{ faq.question }}
                            </h3>
                        </div>
                        <div class="ml-4">
                            <ChevronDown
                                v-if="!openFaqs.includes(faq.id)"
                                class="w-5 h-5 text-gray-500"
                            />
                            <ChevronUp
                                v-else
                                class="w-5 h-5 text-gray-500"
                            />
                        </div>
                    </button>
                    <div
                        v-if="openFaqs.includes(faq.id)"
                        class="px-6 py-4 bg-gray-50 border-t border-gray-200"
                    >
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ faq.answer }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ translated.stillHaveQuestions || texts.stillHaveQuestions }}
                </h3>
                <p class="text-gray-700 mb-4">
                    {{ translated.stillHaveQuestionsText || texts.stillHaveQuestionsText }}
                </p>
                <a
                    href="/contact"
                    class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition"
                >
                    {{ translated.contactUs || texts.contactUs }}
                </a>
            </div>
        </div>
        </div>
    </div>
</template>

