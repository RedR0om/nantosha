<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';

const props = defineProps<{
    text: string;
}>();

const { language } = useLanguage();
const translatedText = ref(props.text);

const updateTranslation = async () => {
    if (!props.text) {
        translatedText.value = props.text;
        return;
    }

    // Always translate based on current language (bidirectional)
    try {
        translatedText.value = await translateText(props.text, language.value, 'auto');
    } catch (error) {
        console.error('Translation error:', error);
        translatedText.value = props.text;
    }
};

watch(language, updateTranslation, { immediate: true });
watch(() => props.text, updateTranslation);
</script>

<template>
    <span>{{ translatedText }}</span>
</template>

