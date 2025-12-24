import { ref } from 'vue';
import axios from 'axios';

// Configure axios defaults for Laravel CSRF
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

const translationCache = new Map<string, string>();

/**
 * Translate text bidirectionally (English ↔ Japanese)
 * Auto-detects source language and translates to target
 */
export async function translateText(text: string, targetLang: 'en' | 'ja' = 'ja', sourceLang: 'en' | 'ja' | 'auto' = 'auto'): Promise<string> {
    if (!text) {
        return text;
    }

    // Check cache first
    const cacheKey = `${text}_${sourceLang}_${targetLang}`;
    if (translationCache.has(cacheKey)) {
        return translationCache.get(cacheKey)!;
    }

    try {
        const response = await axios.post('/api/translate', {
            text,
            target: targetLang,
            source: sourceLang,
        });

        const translated = response.data.translated;
        translationCache.set(cacheKey, translated);
        return translated;
    } catch (error) {
        console.error('Translation error:', error);
        // Return original text if translation fails
        return text;
    }
}

/**
 * Translate an object/array recursively
 */
export async function translateObject(data: any, targetLang: 'en' | 'ja' = 'ja'): Promise<any> {
    if (targetLang === 'en') {
        return data;
    }

    if (typeof data === 'string') {
        return await translateText(data, targetLang);
    }

    if (Array.isArray(data)) {
        const translated = [];
        for (const item of data) {
            if (typeof item === 'string') {
                translated.push(await translateText(item, targetLang));
            } else if (typeof item === 'object' && item !== null) {
                translated.push(await translateObject(item, targetLang));
            } else {
                translated.push(item);
            }
        }
        return translated;
    }

    if (typeof data === 'object' && data !== null) {
        const translated: any = {};
        for (const [key, value] of Object.entries(data)) {
            // Don't translate certain keys
            if (['icon', 'href', 'style', 'number', 'qty', 'price', 'perCap', 'amount'].includes(key)) {
                translated[key] = value;
            } else if (typeof value === 'string') {
                translated[key] = await translateText(value as string, targetLang);
            } else if (typeof value === 'object' && value !== null) {
                translated[key] = await translateObject(value, targetLang);
            } else {
                translated[key] = value;
            }
        }
        return translated;
    }

    return data;
}

/**
 * Translate content using batch API (more efficient for complex objects)
 * Auto-detects source language and translates to target
 */
export async function translateContent(content: any, targetLang: 'en' | 'ja' = 'ja', sourceLang: 'en' | 'ja' | 'auto' = 'auto'): Promise<any> {
    if (!content) {
        return content;
    }

    // Ensure content is an object/array (not null or undefined)
    if (content === null || content === undefined) {
        return content;
    }

    try {
        const response = await axios.post('/api/translate/batch', {
            data: content,
            target: targetLang,
            source: sourceLang,
        });

        return response.data.translated;
    } catch (error: any) {
        console.error('Batch translation error:', error);
        // Log the actual error response if available
        if (error.response) {
            console.error('Error response:', error.response.data);
        }
        // Fallback to original content
        return content;
    }
}

