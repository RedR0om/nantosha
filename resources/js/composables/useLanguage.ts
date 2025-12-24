import { ref, onMounted } from 'vue';

type Language = 'en' | 'ja';

const language = ref<Language>('en');

export function useLanguage() {
    onMounted(() => {
        const savedLanguage = localStorage.getItem('language') as Language | null;
        if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'ja')) {
            language.value = savedLanguage;
        } else {
            // Default to browser language if Japanese, otherwise English
            const browserLang = navigator.language.toLowerCase();
            language.value = browserLang.startsWith('ja') ? 'ja' : 'en';
        }
    });

    function setLanguage(value: Language) {
        language.value = value;
        localStorage.setItem('language', value);
    }

    return {
        language,
        setLanguage,
    };
}

