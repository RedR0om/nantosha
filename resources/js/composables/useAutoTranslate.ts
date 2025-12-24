import { ref, watch, computed } from 'vue';
import { useLanguage } from './useLanguage';
import { translateText } from './useTranslation';

/**
 * Composable for automatically translating static text content
 * Usage: const { t } = useAutoTranslate();
 * Then use: {{ t('Your English text') }}
 */
export function useAutoTranslate() {
    const { language } = useLanguage();
    const translationCache = ref<Map<string, string>>(new Map());

    const t = async (text: string): Promise<string> => {
        if (!text) {
            return text;
        }

        // Check cache
        const cacheKey = `${text}_auto_${language.value}`;
        if (translationCache.value.has(cacheKey)) {
            return translationCache.value.get(cacheKey)!;
        }

        // Translate bidirectionally (auto-detect source)
        const translated = await translateText(text, language.value, 'auto');
        translationCache.value.set(cacheKey, translated);
        return translated;
    };

    // Reactive version that returns a computed ref
    const translate = (text: string) => {
        const translated = ref(text);

        watch(language, async () => {
            // Always translate based on current language (bidirectional)
            translated.value = await t(text);
        }, { immediate: true });

        return translated;
    };

    return {
        t,
        translate,
        language,
    };
}

