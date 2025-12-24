<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import ProductCarousel from '@/components/ProductCarousel.vue';
import HeroCarousel from '@/components/HeroCarousel.vue';
import PublicNav from '@/components/PublicNav.vue';
import CartSidebarWrapper from '@/components/CartSidebarWrapper.vue';
import { CheckCircle, Shield, Award, Package } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateContent, translateText } from '@/composables/useTranslation';

const { language } = useLanguage();

// Reactive translated sections
const translatedSectionsData = ref<Map<number, any>>(new Map());
// Reactive translated carousel slides
const translatedCarouselSlides = ref<Map<number, any>>(new Map());
// Reactive translated products
const translatedProducts = ref<Map<number, any>>(new Map());

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

const props = defineProps<{
    carouselSlides: any[];
    homepageSections?: any[];
    bestSellers: any[];
    newArrivals: any[];
    customerFavorites: any[];
    multiProductMode: boolean;
    featuredProduct?: any;
}>();

// Helper to get icon component by name
const getIcon = (iconName: string) => {
    const icons: { [key: string]: any } = {
        CheckCircle,
        Shield,
        Award,
        Package,
    };
    return icons[iconName] || CheckCircle;
};

// Translate all sections, carousel slides, and products when language changes (bidirectional)
watch(language, async () => {
    translatedSectionsData.value.clear();
    translatedCarouselSlides.value.clear();
    translatedProducts.value.clear();
    
    // Always translate based on current language (bidirectional)
    // Translate homepage sections (including static sections)
    if (props.homepageSections) {
        for (const section of props.homepageSections) {
            const translated = {
                ...section,
                title: section.title ? await translateText(section.title, language.value, 'auto') : section.title,
                subtitle: section.subtitle ? await translateText(section.subtitle, language.value, 'auto') : section.subtitle,
                content: section.content ? await translateContent(section.content, language.value, 'auto') : section.content,
            };
            // Store by ID for regular sections, and also by type for static sections
            translatedSectionsData.value.set(section.id, translated);
            if (['carousel', 'best_sellers', 'new_arrivals', 'customer_favorites', 'featured_product'].includes(section.type)) {
                translatedSectionsData.value.set(section.type, translated);
            }
        }
    }
    
    // Translate carousel slides
    if (props.carouselSlides) {
        for (const slide of props.carouselSlides) {
            const translated = {
                ...slide,
                title: slide.title ? await translateText(slide.title, language.value, 'auto') : slide.title,
                subtitle: slide.subtitle ? await translateText(slide.subtitle, language.value, 'auto') : slide.subtitle,
                button_text: slide.button_text ? await translateText(slide.button_text, language.value, 'auto') : slide.button_text,
            };
            translatedCarouselSlides.value.set(slide.id, translated);
        }
    }
    
    // Translate products
    const allProducts = [
        ...(props.bestSellers || []),
        ...(props.newArrivals || []),
        ...(props.customerFavorites || []),
        ...(props.featuredProduct ? [props.featuredProduct] : []),
    ];
    
    for (const product of allProducts) {
        if (product && product.id) {
            const translated = {
                ...product,
                name: product.name ? await translateText(product.name, language.value, 'auto') : product.name,
                short_description: product.short_description ? await translateText(product.short_description, language.value, 'auto') : product.short_description,
                description: product.description ? await translateText(product.description, language.value, 'auto') : product.description,
                ingredients: product.ingredients ? await translateText(product.ingredients, language.value, 'auto') : product.ingredients,
                directions: product.directions ? await translateText(product.directions, language.value, 'auto') : product.directions,
                warnings: product.warnings ? await translateText(product.warnings, language.value, 'auto') : product.warnings,
                supplement_facts: product.supplement_facts ? await translateText(product.supplement_facts, language.value, 'auto') : product.supplement_facts,
                brand: product.brand ? { ...product.brand, name: await translateText(product.brand.name, language.value, 'auto') } : product.brand,
                category: product.category ? { ...product.category, name: await translateText(product.category.name, language.value, 'auto') } : product.category,
            };
            translatedProducts.value.set(product.id, translated);
        }
    }
    
    // Translate static carousel titles
    const keys = Object.keys(carouselTitles.value);
    for (const key of keys) {
        try {
            translatedCarouselTitles.value[key] = await translateText(carouselTitles.value[key], language.value, 'auto');
        } catch (error) {
            translatedCarouselTitles.value[key] = carouselTitles.value[key];
        }
    }
}, { immediate: true });

// Helper to get section with translation
const getSection = (section: any) => {
    // For static sections (string IDs), try to get translation by type
    if (typeof section.id === 'string') {
        const translated = translatedSectionsData.value.get(section.type);
        if (translated) {
            return { ...section, ...translated };
        }
        return section;
    }
    // For regular sections, get by ID
    const translated = translatedSectionsData.value.get(section.id);
    return translated || section;
};

// Helper to get content based on language
const getSectionContent = (section: any) => {
    const s = getSection(section);
    return s?.content;
};

// Helper to get title based on language
const getSectionTitle = (section: any) => {
    const s = getSection(section);
    return s?.title;
};

// Helper to get subtitle based on language
const getSectionSubtitle = (section: any) => {
    const s = getSection(section);
    return s?.subtitle;
};

// Helper to get translated product
const getTranslatedProduct = (product: any) => {
    if (!product) {
        return product;
    }
    return translatedProducts.value.get(product.id) || product;
};

// Translated carousel titles
const carouselTitles = ref({
    bestSellers: 'Our Best Sellers',
    customerFavorites: "Customer's Favourite",
    newArrivals: 'New arrivals',
});

const translatedCarouselTitles = ref<Record<string, string>>({});

// Translate carousel titles
watch(language, async () => {
    if (language.value === 'en') {
        translatedCarouselTitles.value = { ...carouselTitles.value };
        return;
    }
    
    const keys = Object.keys(carouselTitles.value);
    for (const key of keys) {
        try {
            translatedCarouselTitles.value[key] = await translateText(carouselTitles.value[key], 'ja');
        } catch (error) {
            translatedCarouselTitles.value[key] = carouselTitles.value[key];
        }
    }
}, { immediate: true });

// Computed for translated product lists
const translatedBestSellers = computed(() => {
    return props.bestSellers.map(p => getTranslatedProduct(p));
});

const translatedNewArrivals = computed(() => {
    return props.newArrivals.map(p => getTranslatedProduct(p));
});

const translatedCustomerFavorites = computed(() => {
    return props.customerFavorites.map(p => getTranslatedProduct(p));
});

const translatedFeaturedProduct = computed(() => {
    return props.featuredProduct ? getTranslatedProduct(props.featuredProduct) : null;
});

// Helper to get translated carousel slide
const getTranslatedSlide = (slide: any) => {
    return translatedCarouselSlides.value.get(slide.id) || slide;
};

// Create virtual static sections for carousel and product sections
// These will be merged with database sections and sorted by sort_order
const staticSections = computed(() => {
    const sections: any[] = [];
    
    // Carousel section (if slides exist)
    if (heroSlides.value.length > 0) {
        const carouselSection = props.homepageSections?.find(s => s.type === 'carousel');
        sections.push({
            id: 'carousel',
            type: 'carousel',
            title: carouselSection?.title || null,
            subtitle: carouselSection?.subtitle || null,
            sort_order: carouselSection?.sort_order ?? 0,
            is_active: carouselSection?.is_active ?? true,
            background_color: carouselSection?.background_color || 'white',
        });
    }
    
    // Best Sellers section (if in multi-product mode and products exist)
    if (props.multiProductMode && props.bestSellers.length > 0) {
        const bestSellersSection = props.homepageSections?.find(s => s.type === 'best_sellers');
        const translatedSection = language.value === 'ja' ? translatedSectionsData.value.get('best_sellers') : null;
        sections.push({
            id: 'best_sellers',
            type: 'best_sellers',
            title: translatedSection?.title || bestSellersSection?.title || carouselTitles.value.bestSellers,
            subtitle: translatedSection?.subtitle || bestSellersSection?.subtitle || null,
            sort_order: bestSellersSection?.sort_order ?? 2,
            is_active: bestSellersSection?.is_active ?? true,
            background_color: bestSellersSection?.background_color || 'white',
        });
    }
    
    // Featured Product section (if in single product mode)
    if (!props.multiProductMode && props.featuredProduct) {
        const featuredSection = props.homepageSections?.find(s => s.type === 'featured_product');
        sections.push({
            id: 'featured_product',
            type: 'featured_product',
            title: featuredSection?.title || null,
            subtitle: featuredSection?.subtitle || null,
            sort_order: featuredSection?.sort_order ?? 2,
            is_active: featuredSection?.is_active ?? true,
            background_color: featuredSection?.background_color || 'white',
        });
    }
    
    // Customer Favorites section
    if (props.multiProductMode && props.customerFavorites.length > 0) {
        const favoritesSection = props.homepageSections?.find(s => s.type === 'customer_favorites');
        const translatedSection = language.value === 'ja' ? translatedSectionsData.value.get('customer_favorites') : null;
        sections.push({
            id: 'customer_favorites',
            type: 'customer_favorites',
            title: translatedSection?.title || favoritesSection?.title || carouselTitles.value.customerFavorites,
            subtitle: translatedSection?.subtitle || favoritesSection?.subtitle || null,
            sort_order: favoritesSection?.sort_order ?? 3,
            is_active: favoritesSection?.is_active ?? true,
            background_color: favoritesSection?.background_color || 'gray-50',
        });
    }
    
    // New Arrivals section
    if (props.multiProductMode && props.newArrivals.length > 0) {
        const newArrivalsSection = props.homepageSections?.find(s => s.type === 'new_arrivals');
        const translatedSection = language.value === 'ja' ? translatedSectionsData.value.get('new_arrivals') : null;
        sections.push({
            id: 'new_arrivals',
            type: 'new_arrivals',
            title: translatedSection?.title || newArrivalsSection?.title || carouselTitles.value.newArrivals,
            subtitle: translatedSection?.subtitle || newArrivalsSection?.subtitle || null,
            sort_order: newArrivalsSection?.sort_order ?? 4,
            is_active: newArrivalsSection?.is_active ?? true,
            background_color: newArrivalsSection?.background_color || 'white',
        });
    }
    
    return sections;
});

// Merge database sections with static sections and sort by sort_order
const allSections = computed(() => {
    const dbSections = (props.homepageSections || []).filter(s => 
        s.type !== 'carousel' && 
        s.type !== 'best_sellers' && 
        s.type !== 'new_arrivals' && 
        s.type !== 'customer_favorites' && 
        s.type !== 'featured_product'
    );
    
    const merged = [...dbSections, ...staticSections.value];
    return merged
        .filter(s => s.is_active)
        .sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
});

// Transform carousel slides from database to component format
const heroSlides = computed(() => {
    return props.carouselSlides.map(slide => {
        const translated = getTranslatedSlide(slide);
        return {
            id: translated.id,
            image: translated.image,
            video: translated.video,
            media_type: translated.media_type || 'image',
            title: translated.title,
            subtitle: translated.subtitle,
            link: translated.link,
            buttonText: translated.button_text,
        };
    });
});
</script>

<template>
    <Head title="Nantosha Import & Export Division - 南東舎輸出入部 | Genuine Ivermectin Capsules" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <CartSidebarWrapper />
        
        <!-- All Homepage Sections (Dynamic + Static, sorted by sort_order) -->
        <template v-for="section in allSections" :key="section.id || `static-${section.type}`">
            <!-- Carousel Section -->
            <section
                v-if="section.type === 'carousel' && heroSlides.length > 0"
                class="w-full"
            >
                <HeroCarousel :slides="heroSlides" :auto-play-interval="5000" />
            </section>
            <!-- Hero Section -->
            <section
                v-if="section.type === 'hero'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <div class="text-center max-w-4xl mx-auto">
                        <h1 v-if="getSectionTitle(section)" class="text-3xl md:text-4xl font-semibold text-gray-900 mb-3 tracking-tight">
                            {{ getSectionTitle(section) }}
                        </h1>
                        <p v-if="getSectionSubtitle(section)" class="text-base md:text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                            {{ getSectionSubtitle(section) }}
                        </p>
                        <div v-if="getSectionContent(section)?.badges" class="flex flex-wrap justify-center gap-3 mb-8">
                            <div
                                v-for="(badge, index) in getSectionContent(section)?.badges"
                                :key="index"
                                class="flex items-center gap-2 bg-gray-50 px-4 py-2 border border-gray-200"
                            >
                                <component :is="getIcon(badge.icon)" class="w-4 h-4 text-gray-700" />
                                <span class="text-sm font-medium text-gray-900">{{ badge.text }}</span>
                            </div>
                        </div>
                        <div v-if="getSectionContent(section)?.buttons" class="flex flex-col sm:flex-row gap-3 justify-center">
                            <Link
                                v-for="(button, index) in getSectionContent(section)?.buttons"
                                :key="index"
                                :href="button.href"
                                :class="[
                                    'inline-block px-6 py-3 text-sm font-medium transition-colors uppercase tracking-wide',
                                    button.style === 'primary'
                                        ? 'bg-black text-white hover:bg-gray-800'
                                        : 'bg-white text-gray-900 border border-gray-300 hover:bg-gray-50'
                                ]"
                            >
                                {{ button.text }}
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section
                v-else-if="section.type === 'features'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'white' ? 'bg-white' : 'bg-gray-50'
                ]"
            >
                <div class="container mx-auto px-4">
                    <h2 v-if="getSectionTitle(section)" class="text-2xl md:text-3xl font-semibold text-gray-900 mb-10 text-center tracking-tight">
                        {{ getSectionTitle(section) }}
                    </h2>
                    <div v-if="getSectionContent(section)?.items" class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                        <div
                            v-for="(item, index) in getSectionContent(section)?.items"
                            :key="index"
                            class="text-center"
                        >
                            <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <component :is="getIcon(item.icon)" class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ item.title }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Product Info Section -->
            <section
                v-else-if="section.type === 'product_info'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto">
                        <h2 v-if="getSectionTitle(section)" class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                            {{ getSectionTitle(section) }}
                        </h2>
                        <div class="bg-white border border-gray-200 p-6 md:p-8">
                            <div v-if="getSectionContent(section)?.ingredients || getSectionContent(section)?.details" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-if="getSectionContent(section)?.ingredients">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Ingredients per Capsule (15mg)</h3>
                                    <ul class="space-y-2 text-sm text-gray-600">
                                        <li v-for="(ing, index) in getSectionContent(section)?.ingredients" :key="index">
                                            • {{ ing.name }}: {{ ing.amount }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-if="getSectionContent(section)?.details">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Product Details</h3>
                                    <ul class="space-y-2 text-sm text-gray-600">
                                        <li v-for="(detail, index) in getSectionContent(section)?.details" :key="index">
                                            • {{ detail.label }}: {{ detail.value }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-if="getSectionContent(section)?.indications" class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">Approved Indications</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ getSectionContent(section)?.indications }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section
                v-else-if="section.type === 'pricing'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'white' ? 'bg-white' : 'bg-gray-50'
                ]"
            >
                <div class="container mx-auto px-4">
                    <h2 v-if="getSectionTitle(section)" class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                        {{ getSectionTitle(section) }}
                    </h2>
                    <div v-if="getSectionContent(section)?.tiers" class="max-w-4xl mx-auto">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                            <div
                                v-for="(tier, index) in getSectionContent(section)?.tiers"
                                :key="index"
                                class="bg-white border border-gray-200 p-4 text-center hover:border-gray-300 transition-colors"
                            >
                                <div class="text-sm font-semibold text-gray-900 mb-2">
                                    {{ tier.qty }} caps
                                </div>
                                <div class="text-lg font-semibold text-gray-900 mb-1">
                                    ¥{{ tier.price.toLocaleString() }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ¥{{ tier.perCap }}/cap
                                </div>
                            </div>
                        </div>
                        <p v-if="getSectionContent(section)?.note" class="text-center text-xs text-gray-600 mt-6">
                            <strong>Note:</strong> {{ getSectionContent(section)?.note }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section
                v-else-if="section.type === 'how_it_works'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <h2 v-if="getSectionTitle(section)" class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                        {{ getSectionTitle(section) }}
                    </h2>
                    <div v-if="getSectionContent(section)?.steps" class="max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div
                                v-for="(step, index) in getSectionContent(section)?.steps"
                                :key="index"
                                class="text-center"
                            >
                                <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center mx-auto mb-4 font-semibold text-sm">
                                    {{ step.number }}
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ step.title }}</h3>
                                <p class="text-xs text-gray-600">{{ step.description }}</p>
                            </div>
                        </div>
                        <div v-if="getSectionContent(section)?.linkHref" class="mt-8 text-center">
                            <Link
                                :href="getSectionContent(section)?.linkHref"
                                class="inline-block text-sm text-gray-900 font-medium hover:text-gray-700 transition-colors"
                            >
                                {{ getSectionContent(section)?.linkText || 'Learn more' }} →
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Custom Section -->
            <section
                v-else-if="section.type === 'custom'"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <h2 v-if="section.title" class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                        {{ section.title }}
                    </h2>
                    <p v-if="section.subtitle" class="text-base text-gray-600 mb-6 text-center">
                        {{ section.subtitle }}
                    </p>
                    <!-- Custom sections can be extended based on content structure -->
                    <div v-if="section.content" class="max-w-4xl mx-auto">
                        <pre class="text-sm text-gray-600">{{ JSON.stringify(section.content, null, 2) }}</pre>
                    </div>
                </div>
            </section>

            <!-- Best Sellers Section (Static) -->
            <section
                v-else-if="section.type === 'best_sellers' && multiProductMode && bestSellers.length > 0"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <ProductCarousel
                        :title="section.title || (translatedCarouselTitles.bestSellers || carouselTitles.bestSellers)"
                        :items="translatedBestSellers"
                        :items-per-view="4"
                    />
                </div>
            </section>

            <!-- Featured Product Section (Static) -->
            <section
                v-else-if="section.type === 'featured_product' && !multiProductMode && translatedFeaturedProduct"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4 max-w-6xl">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                            <!-- Large Product Image -->
                            <div class="lg:col-span-1">
                                <div class="aspect-square bg-gray-50 relative overflow-hidden">
                                    <img
                                        v-if="translatedFeaturedProduct.image"
                                        :src="translatedFeaturedProduct.image"
                                        :alt="translatedFeaturedProduct.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"
                                    >
                                        No Image
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="lg:col-span-1 p-8 md:p-12 flex flex-col justify-center">
                                <div v-if="translatedFeaturedProduct.brand" class="text-xs text-gray-500 uppercase tracking-wide mb-2">
                                    {{ translatedFeaturedProduct.brand.name }}
                                </div>
                                <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 mb-4 tracking-tight">
                                    {{ translatedFeaturedProduct.name }}
                                </h2>
                                <p v-if="translatedFeaturedProduct.short_description" class="text-gray-600 mb-6 leading-relaxed">
                                    {{ translatedFeaturedProduct.short_description }}
                                </p>
                                
                                <!-- Price -->
                                <div class="mb-6">
                                    <div class="flex items-baseline gap-3 mb-2">
                                        <span
                                            v-if="translatedFeaturedProduct.sale_price"
                                            class="text-3xl font-bold text-gray-900"
                                        >
                                            {{ formatPrice(translatedFeaturedProduct.sale_price) }}
                                        </span>
                                        <span
                                            :class="[
                                                'text-3xl font-bold',
                                                translatedFeaturedProduct.sale_price ? 'text-gray-400 line-through text-2xl' : 'text-gray-900'
                                            ]"
                                        >
                                            {{ formatPrice(translatedFeaturedProduct.price) }}
                                        </span>
                                    </div>
                                    <p v-if="translatedFeaturedProduct.sale_price" class="text-sm text-green-600 font-medium">
                                        Save {{ formatPrice(translatedFeaturedProduct.price - translatedFeaturedProduct.sale_price) }}
                                    </p>
                                </div>
                                
                                <!-- Stock Status -->
                                <div class="mb-6">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                            translatedFeaturedProduct.in_stock
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ translatedFeaturedProduct.in_stock ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>
                                
                                <!-- CTA Buttons -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <Link
                                        :href="`/products/${translatedFeaturedProduct.slug}`"
                                        class="inline-block bg-gray-900 text-white px-8 py-4 text-base font-semibold hover:bg-gray-800 transition-colors uppercase tracking-wide text-center"
                                    >
                                        View Product Details
                                    </Link>
                                    <Link
                                        v-if="translatedFeaturedProduct.in_stock"
                                        :href="`/products/${translatedFeaturedProduct.slug}`"
                                        class="inline-block bg-white text-gray-900 border-2 border-gray-900 px-8 py-4 text-base font-semibold hover:bg-gray-50 transition-colors uppercase tracking-wide text-center"
                                    >
                                        Add to Cart
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Customer Favorites Section (Static) -->
            <section
                v-else-if="section.type === 'customer_favorites' && multiProductMode && customerFavorites.length > 0"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <ProductCarousel
                        :title="section.title || (translatedCarouselTitles.customerFavorites || carouselTitles.customerFavorites)"
                        :items="translatedCustomerFavorites"
                        :items-per-view="4"
                    />
                </div>
            </section>

            <!-- New Arrivals Section (Static) -->
            <section
                v-else-if="section.type === 'new_arrivals' && multiProductMode && newArrivals.length > 0"
                :class="[
                    'py-12 md:py-16',
                    section.background_color === 'gray-50' ? 'bg-gray-50' : 'bg-white'
                ]"
            >
                <div class="container mx-auto px-4">
                    <ProductCarousel
                        :title="section.title || (translatedCarouselTitles.newArrivals || carouselTitles.newArrivals)"
                        :items="translatedNewArrivals"
                        :items-per-view="4"
                    />
                </div>
            </section>
        </template>

        <!-- Legacy Hero Section (for backward compatibility if no sections exist) -->
        <section v-if="allSections.length === 0" class="bg-white py-12 md:py-16">
            <div class="container mx-auto px-4">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 mb-3 tracking-tight">
                        Nantosha Import & Export Division
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 mb-6">南東舎輸出入部</p>
                    <p class="text-base md:text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Genuine Ivermectin Capsules (15mg) - Verified by Japanese Research Institutions
                    </p>
                    <div class="flex flex-wrap justify-center gap-3 mb-8">
                        <div class="flex items-center gap-2 bg-gray-50 px-4 py-2 border border-gray-200">
                            <CheckCircle class="w-4 h-4 text-gray-700" />
                            <span class="text-sm font-medium text-gray-900">99%+ Purity</span>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 px-4 py-2 border border-gray-200">
                            <Shield class="w-4 h-4 text-gray-700" />
                            <span class="text-sm font-medium text-gray-900">Japanese Lab Verified</span>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 px-4 py-2 border border-gray-200">
                            <Award class="w-4 h-4 text-gray-700" />
                            <span class="text-sm font-medium text-gray-900">From Dr. Allan Landrito</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <Link
                            href="/products"
                            class="inline-block bg-black text-white px-6 py-3 text-sm font-medium hover:bg-gray-800 transition-colors uppercase tracking-wide"
                        >
                            View Product Details
                        </Link>
                        <Link
                            href="/how-to-order"
                            class="inline-block bg-white text-gray-900 border border-gray-300 px-6 py-3 text-sm font-medium hover:bg-gray-50 transition-colors uppercase tracking-wide"
                        >
                            How to Order
                        </Link>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>
