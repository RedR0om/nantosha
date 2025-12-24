<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    /**
     * Translate text bidirectionally (English ↔ Japanese)
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'target' => 'nullable|string|in:ja,en',
            'source' => 'nullable|string|in:ja,en,auto',
        ]);

        $text = $request->input('text');
        $target = $request->input('target', 'ja');
        $source = $request->input('source', 'auto'); // 'auto' will auto-detect

        // Create cache key including source and target
        $cacheKey = 'translation_' . md5($text . '_' . $source . '_' . $target);

        // Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return response()->json(['translated' => $cached]);
        }

        try {
            $translator = new GoogleTranslate();
            
            // Auto-detect source language if needed
            if ($source === 'auto') {
                $translator->setSource(null); // null = auto-detect
            } else {
                $translator->setSource($source);
            }
            
            $translator->setTarget($target);
            
            // Translate the text
            $translated = $translator->translate($text);
            
            // Cache the translation for 30 days
            Cache::put($cacheKey, $translated, now()->addDays(30));
            
            return response()->json(['translated' => $translated]);
        } catch (\Exception $e) {
            \Log::error('Translation failed: ' . $e->getMessage());
            // Return original text if translation fails
            return response()->json(['translated' => $text, 'error' => 'Translation service unavailable']);
        }
    }

    /**
     * Translate multiple texts at once (for arrays/objects)
     */
    public function translateBatch(Request $request)
    {
        // More flexible validation - accept any data type
        $request->validate([
            'data' => 'present', // Just check that the field is present
            'target' => 'nullable|string|in:ja,en',
            'source' => 'nullable|string|in:ja,en,auto',
        ]);

        $data = $request->input('data');
        $target = $request->input('target', 'ja');
        $source = $request->input('source', 'auto');

        // If data is null or empty, return as is
        if ($data === null || $data === '') {
            return response()->json(['translated' => $data]);
        }

        $translated = $this->translateRecursive($data, $target, $source);

        return response()->json(['translated' => $translated]);
    }

    /**
     * Recursively translate array/object data
     */
    private function translateRecursive($data, $target, $source = 'auto')
    {
        if (is_string($data)) {
            // Translate string
            $cacheKey = 'translation_' . md5($data . '_' . $source . '_' . $target);
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            try {
                $translator = new GoogleTranslate();
                
                // Auto-detect source language if needed
                if ($source === 'auto') {
                    $translator->setSource(null); // null = auto-detect
                } else {
                    $translator->setSource($source);
                }
                
                $translator->setTarget($target);
                $translated = $translator->translate($data);
                Cache::put($cacheKey, $translated, now()->addDays(30));
                return $translated;
            } catch (\Exception $e) {
                \Log::error('Translation failed: ' . $e->getMessage());
                return $data;
            }
        } elseif (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                // Don't translate keys like 'icon', 'href', 'style', 'number', 'qty', 'price', 'perCap'
                if (in_array($key, ['icon', 'href', 'style', 'number', 'qty', 'price', 'perCap', 'amount'])) {
                    $result[$key] = $value;
                } else {
                    $result[$key] = $this->translateRecursive($value, $target, $source);
                }
            }
            return $result;
        } else {
            // Return as-is for numbers, booleans, etc.
            return $data;
        }
    }
}
