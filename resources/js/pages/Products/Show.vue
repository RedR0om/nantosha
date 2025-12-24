<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import PublicNav from '@/components/PublicNav.vue';
import CartSidebarWrapper from '@/components/CartSidebarWrapper.vue';
import { CheckCircle } from 'lucide-vue-next';
import { useLanguage } from '@/composables/useLanguage';
import { translateText, translateContent } from '@/composables/useTranslation';

const { language } = useLanguage();

const props = defineProps<{
    product: any;
    relatedProducts: any[];
}>();

const page = usePage();
const quantity = ref(1);
const selectedPricingTier = ref<any>(null);
const isAdding = ref(false);
const selectedImageIndex = ref(0);
const activeTab = ref('overview');

// Get all images (main image + additional images)
const allImages = computed(() => {
    const product = translatedProduct.value || props.product;
    const images = [];
    if (product.image) {
        images.push(product.image);
    }
    if (product.images && Array.isArray(product.images)) {
        images.push(...product.images);
    }
    return images;
});

const currentImage = computed(() => {
    const product = translatedProduct.value || props.product;
    return allImages.value[selectedImageIndex.value] || product.image;
});

const selectImage = (index: number) => {
    selectedImageIndex.value = index;
};

const tabs = ref([
    { id: 'overview', label: 'Product Overview' },
    { id: 'features', label: 'Product Features' },
    { id: 'ingredients', label: 'Ingredients' },
    { id: 'dosage', label: 'Dosage/Suggested Use' },
    { id: 'warnings', label: 'Warnings' },
]);

// Translated product data
const translatedProduct = ref<any>(null);
const translatedRelatedProducts = ref<any[]>([]);

// Static text translations
const texts = ref({
    lowestPrice: 'Lowest Price:',
    perCapsule: 'per capsule',
    price: 'Price:',
    verified: 'Verified by Japanese research institutions',
    noOverview: 'No product overview available.',
    brand: 'Brand',
    countryOfOrigin: 'Country of Origin',
    manufacturer: 'Manufacturer',
    category: 'Category',
    noIngredients: 'No ingredients information available.',
    noDosage: 'No dosage information available.',
    pricing: 'Pricing',
    quantity: 'Quantity',
    subtotal: 'Subtotal:',
    inStock: 'in stock',
    adding: 'Adding...',
    addToCart: 'Add to cart',
    soldOut: 'Sold out',
    save: 'Save',
    relatedProducts: 'Related Products',
    noImage: 'No Image',
});

const translated = ref<Record<string, string>>({});
const translatedTabs = ref<Array<{id: string, label: string}>>([]);

// Translate product data (bidirectional)
const translateProductData = async () => {
    // Translate product (bidirectional)
    if (props.product) {
        translatedProduct.value = {
            ...props.product,
            name: props.product.name ? await translateText(props.product.name, language.value, 'auto') : props.product.name,
            short_description: props.product.short_description ? await translateText(props.product.short_description, language.value, 'auto') : props.product.short_description,
            description: props.product.description ? await translateText(props.product.description, language.value, 'auto') : props.product.description,
            ingredients: props.product.ingredients ? await translateText(props.product.ingredients, language.value, 'auto') : props.product.ingredients,
            directions: props.product.directions ? await translateText(props.product.directions, language.value, 'auto') : props.product.directions,
            warnings: props.product.warnings ? await translateText(props.product.warnings, language.value, 'auto') : props.product.warnings,
            supplement_facts: props.product.supplement_facts ? await translateText(props.product.supplement_facts, language.value, 'auto') : props.product.supplement_facts,
            brand: props.product.brand ? { ...props.product.brand, name: await translateText(props.product.brand.name, language.value, 'auto') } : props.product.brand,
            category: props.product.category ? { ...props.product.category, name: await translateText(props.product.category.name, language.value, 'auto') } : props.product.category,
        };
    }

    // Translate related products
    translatedRelatedProducts.value = await Promise.all(
        props.relatedProducts.map(async (product) => ({
            ...product,
            name: product.name ? await translateText(product.name, language.value, 'auto') : product.name,
            short_description: product.short_description ? await translateText(product.short_description, language.value, 'auto') : product.short_description,
            brand: product.brand ? { ...product.brand, name: await translateText(product.brand.name, language.value, 'auto') } : product.brand,
        }))
    );

    // Translate tabs
    translatedTabs.value = await Promise.all(
        tabs.value.map(async (tab) => ({
            ...tab,
            label: await translateText(tab.label, language.value, 'auto'),
        }))
    );

    // Translate static text
    const keys = Object.keys(texts.value);
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            translated.value[key] = texts.value[key];
        }
    }
};

watch(language, translateProductData, { immediate: true });
watch(() => props.product, () => {
    translateProductData();
    initializePricingTier();
}, { deep: true });
watch(() => props.relatedProducts, translateProductData, { deep: true });
onMounted(() => {
    translateProductData();
    initializePricingTier();
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

// Computed property for bottle-based products
const isBottleBased = computed(() => {
    const product = translatedProduct.value || props.product;
    return product.is_bottle_based && product.bottle_pricing_tiers && product.bottle_pricing_tiers.length > 0;
});

// Computed property to check if only bottles are allowed
const isBottlesOnly = computed(() => {
    const product = translatedProduct.value || props.product;
    return product.bottles_only && isBottleBased.value;
});

// Initialize selected pricing tier
const initializePricingTier = () => {
    const product = translatedProduct.value || props.product;
    if (isBottleBased.value && product.bottle_pricing_tiers && product.bottle_pricing_tiers.length > 0) {
        selectedPricingTier.value = product.bottle_pricing_tiers[0];
        quantity.value = 1; // Start with 1 bottle
    }
};

// Calculate total price for bottle-based products
const totalPrice = computed(() => {
    const product = translatedProduct.value || props.product;
    if (isBottleBased.value && selectedPricingTier.value) {
        return selectedPricingTier.value.price * quantity.value;
    }
    return (product.sale_price || product.price) * quantity.value;
});

// Calculate total capsules for bottle-based products
const totalCapsules = computed(() => {
    const product = translatedProduct.value || props.product;
    if (isBottleBased.value && selectedPricingTier.value) {
        return selectedPricingTier.value.capsules * quantity.value;
    }
    return quantity.value;
});

const addToCart = () => {
    const product = translatedProduct.value || props.product;
    if (!product.in_stock || quantity.value <= 0) {
        return;
    }

    isAdding.value = true;

    const cartData: any = {
        product_id: product.id,
        quantity: quantity.value,
    };

    // For bottle-based products, include tier information in variant
    if (isBottleBased.value && selectedPricingTier.value) {
        cartData.variant = {
            type: 'bottle',
            tier: selectedPricingTier.value,
            total_capsules: totalCapsules.value,
            bottles: quantity.value,
        };
        // Send total capsules as quantity for cart tracking
        cartData.quantity = totalCapsules.value;
    }

    router.post('/cart', cartData, {
        preserveScroll: true,
        onSuccess: () => {
            isAdding.value = false;
            // Open cart sidebar
            window.dispatchEvent(new CustomEvent('openCartSidebar'));
        },
        onError: (errors) => {
            isAdding.value = false;
            console.error('Error adding to cart:', errors);
            // Still open cart sidebar so user can see their current cart
            // The error message will be displayed on the page
            window.dispatchEvent(new CustomEvent('openCartSidebar'));
        },
        onFinish: () => {
            isAdding.value = false;
        },
    });
};

const successMessage = computed(() => {
    const flash = page.props.flash as any;
    return flash?.success || null;
});
const errorMessage = computed(() => {
    const errors = page.props.errors as any;
    if (errors && Object.keys(errors).length > 0) {
        return Object.values(errors)[0] as string;
    }
    return null;
});
</script>

<template>
    <Head :title="`${(translatedProduct || product).name} - Nantosha Import & Export Division`" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <CartSidebarWrapper />
        <div class="py-8 md:py-12">
        <div class="container mx-auto px-4 max-w-7xl">
            <!-- Product Header -->
            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-3 tracking-tight">
                    {{ (translatedProduct || product).name }}
                </h1>
                <p v-if="product.price_per_capsule || product.price_per_mg" class="text-base md:text-lg text-gray-600 mb-4">
                    <span v-if="product.price_per_capsule">
                        {{ translated.lowestPrice || texts.lowestPrice }} {{ formatPrice(product.price_per_capsule) }} {{ translated.perCapsule || texts.perCapsule }}
                    </span>
                    <span v-if="product.price_per_capsule && product.price_per_mg"> (</span>
                    <span v-if="product.price_per_mg">
                        {{ formatPrice(product.price_per_mg) }} / mg
                    </span>
                    <span v-if="product.price_per_capsule && product.price_per_mg">)</span>
                </p>
                <p v-else-if="(translatedProduct || product).short_description" class="text-base md:text-lg text-gray-600 mb-4">
                    {{ (translatedProduct || product).short_description }}
                </p>
                <p v-else-if="product.price" class="text-base md:text-lg text-gray-600 mb-4">
                    {{ translated.price || texts.price }} {{ formatPrice(product.sale_price || product.price) }}
                </p>
                <div class="flex items-center gap-2 text-gray-700">
                    <CheckCircle class="w-4 h-4" />
                    <span class="text-sm font-medium">{{ translated.verified || texts.verified }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Main Product Info -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Product Image Gallery -->
                    <div>
                        <!-- Main Image -->
                        <div class="bg-gray-50 aspect-square overflow-hidden border border-gray-200 mb-4">
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                :alt="(translatedProduct || product).name"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"
                            >
                                {{ translated.noImage || texts.noImage }}
                            </div>
                        </div>
                        
                        <!-- Thumbnail Gallery -->
                        <div
                            v-if="allImages.length > 1"
                            class="flex gap-2 overflow-x-auto pb-2"
                        >
                            <button
                                v-for="(image, index) in allImages"
                                :key="index"
                                @click="selectImage(index)"
                                :class="[
                                    'flex-shrink-0 w-20 h-20 border-2 overflow-hidden rounded-lg transition-all',
                                    selectedImageIndex === index
                                        ? 'border-gray-900 ring-2 ring-gray-900'
                                        : 'border-gray-200 hover:border-gray-400'
                                ]"
                            >
                                <img
                                    :src="image"
                                    :alt="`${(translatedProduct || product).name} - Image ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Product Information Tabs -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <div class="flex overflow-x-auto">
                                <button
                                    v-for="tab in (translatedTabs.length > 0 ? translatedTabs : tabs)"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                                        activeTab === tab.id
                                            ? 'border-gray-900 dark:border-gray-100 text-gray-900 dark:text-white'
                                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300 dark:hover:border-gray-500'
                                    ]"
                                >
                                    {{ tab.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6">
                            <!-- Product Overview -->
                            <div v-if="activeTab === 'overview'">
                                <div v-if="(translatedProduct || product).description" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ (translatedProduct || product).description }}
                                </div>
                                <div v-else-if="(translatedProduct || product).short_description" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ (translatedProduct || product).short_description }}
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    {{ translated.noOverview || texts.noOverview }}
                                </div>
                            </div>

                            <!-- Product Features -->
                            <div v-if="activeTab === 'features'">
                                <div v-if="(translatedProduct || product).supplement_facts" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ (translatedProduct || product).supplement_facts }}
                                </div>
                                <div v-else class="space-y-4">
                                    <div v-if="product.brand" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ translated.brand || texts.brand }}</h4>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.brand.name }}</p>
                                        <p v-if="product.brand.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            {{ product.brand.description }}
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div v-if="product.country_of_origin" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">{{ translated.countryOfOrigin || texts.countryOfOrigin }}</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.country_of_origin }}</p>
                                        </div>
                                        <div v-if="product.manufacturer" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">{{ translated.manufacturer || texts.manufacturer }}</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.manufacturer }}</p>
                                        </div>
                                        <div v-if="product.sku" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">SKU</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.sku }}</p>
                                        </div>
                                        <div v-if="product.category" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">{{ translated.category || texts.category }}</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.category.name }}</p>
                                        </div>
                                    </div>
                                    <div class="pt-4">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <CheckCircle class="w-3 h-3 text-gray-700 dark:text-gray-300" />
                                            {{ translated.verified || texts.verified }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingredients -->
                            <div v-if="activeTab === 'ingredients'">
                                <div v-if="(translatedProduct || product).ingredients" class="space-y-3">
                                    <div
                                        v-for="(line, index) in (translatedProduct || product).ingredients.split('\n').filter((l: string) => l.trim())"
                                        :key="index"
                                        class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                    >
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ line.split(':')[0] }}</span>
                                        <span v-if="line.includes(':')" class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ line.split(':')[1].trim() }}
                                        </span>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <CheckCircle class="w-3 h-3 text-gray-700 dark:text-gray-300" />
                                            {{ translated.verified || texts.verified }}
                                        </p>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    {{ translated.noIngredients || texts.noIngredients }}
                                </div>
                            </div>

                            <!-- Dosage/Suggested Use -->
                            <div v-if="activeTab === 'dosage'">
                                <div v-if="(translatedProduct || product).directions" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ (translatedProduct || product).directions }}
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    {{ translated.noDosage || texts.noDosage }}
                                </div>
                            </div>

                            <!-- Warnings -->
                            <div v-if="activeTab === 'warnings'">
                                <div v-if="(translatedProduct || product).warnings" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ (translatedProduct || product).warnings }}
                                </div>
                                <div v-else-if="(translatedProduct || product).supplement_facts" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ (translatedProduct || product).supplement_facts }}
                                </div>
                                <div v-else class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p class="mb-4">
                                        This product is not intended for pregnant or nursing women or by persons under 18. 
                                        Consult your healthcare professional prior to use if you have or suspect a medical condition or are taking prescription drugs. 
                                        Discontinue use and seek advice of a doctor if any adverse reactions occur.
                                    </p>
                                    <p class="mb-4">
                                        <strong>Important:</strong> Resale or transfer to third parties is strictly prohibited. 
                                        Only doctors with proper "Yakkan Shoumei" (Medicine Import Confirmation) permits can prescribe to others.
                                    </p>
                                    <p>
                                        <strong>Note:</strong> MHLW only approves Ivermectin for two diseases: Strongyloidiasis and Scabies. 
                                        Global research suggests potential antiviral and anti-tumor effects, though these are not officially recognized by the Japanese MHLW.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Pricing & Order -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 p-6 sticky top-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 tracking-tight">{{ translated.pricing || texts.pricing }}</h2>
                        
                        <!-- Price Display -->
                        <div class="mb-6">
                            <div class="flex items-baseline gap-2 mb-2">
                                <span v-if="product.sale_price" class="text-2xl font-bold text-gray-900">
                                    {{ formatPrice(product.sale_price) }}
                                </span>
                                <span :class="[
                                    'text-2xl font-bold',
                                    product.sale_price ? 'text-gray-400 line-through text-lg' : 'text-gray-900'
                                ]">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </div>
                            <p v-if="product.sale_price" class="text-sm text-green-600 font-medium">
                                {{ translated.save || texts.save }} {{ formatPrice(product.price - product.sale_price) }}
                            </p>
                        </div>

                        <!-- Bottle-Based Pricing Selection -->
                        <div v-if="isBottleBased" class="mb-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Select Quantity</label>
                                <div class="space-y-2">
                                    <button
                                        v-for="(tier, index) in (translatedProduct || product).bottle_pricing_tiers"
                                        :key="index"
                                        @click="selectedPricingTier = tier; quantity = 1"
                                        :class="[
                                            'w-full text-left p-4 border-2 rounded-lg transition-all',
                                            selectedPricingTier && selectedPricingTier.capsules === tier.capsules
                                                ? 'border-gray-900 bg-gray-50'
                                                : 'border-gray-200 hover:border-gray-400'
                                        ]"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ tier.capsules }} caps</div>
                                                <div class="text-xs text-gray-500">¥{{ tier.price_per_capsule?.toLocaleString() }}/cap</div>
                                            </div>
                                            <div class="text-lg font-bold text-gray-900">¥{{ tier.price.toLocaleString() }}</div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            
                            <div v-if="selectedPricingTier">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Bottles</label>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    :max="product.stock_quantity ? Math.floor(product.stock_quantity / selectedPricingTier.capsules) : 999"
                                    class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ product.capsules_per_bottle || 50 }} capsules per bottle
                                </p>
                            </div>
                        </div>

                        <!-- Regular Quantity Selector (only shown if bottles_only is false) -->
                        <div v-else-if="!isBottlesOnly" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ translated.quantity || texts.quantity }}</label>
                            <input
                                v-model.number="quantity"
                                type="number"
                                min="1"
                                :max="product.stock_quantity || 999"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                            />
                        </div>
                        
                        <!-- Message when bottles_only is enabled but bottle-based pricing is not set up -->
                        <div v-else-if="isBottlesOnly && !isBottleBased" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                This product is only available for purchase in bottle quantities. Please contact us for more information.
                            </p>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 p-4 mb-6">
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>{{ translated.subtotal || texts.subtotal }}</span>
                                    <span class="font-semibold">{{ formatPrice(totalPrice) }}</span>
                                </div>
                                <div v-if="isBottleBased && selectedPricingTier" class="text-xs text-gray-500">
                                    {{ totalCapsules }} capsules total ({{ quantity }} {{ quantity === 1 ? 'bottle' : 'bottles' }})
                                </div>
                                <div v-else-if="product.stock_quantity" class="text-xs text-gray-500">
                                    {{ product.stock_quantity }} {{ translated.inStock || texts.inStock }}
                                </div>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <div v-if="successMessage" class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded">
                            {{ successMessage }}
                        </div>
                        <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded">
                            {{ errorMessage }}
                        </div>

                        <!-- Add to Cart -->
                        <button
                            @click="addToCart"
                            :disabled="!product.in_stock || quantity <= 0 || isAdding || (isBottleBased && !selectedPricingTier) || (isBottlesOnly && !isBottleBased)"
                            class="w-full bg-black text-white py-3 text-sm font-medium hover:bg-gray-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:bg-gray-300 mb-4 uppercase tracking-wide"
                        >
                            {{ isAdding ? (translated.adding || texts.adding) : (product.in_stock ? (translated.addToCart || texts.addToCart) : (translated.soldOut || texts.soldOut)) }}
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <div
                v-if="(translatedRelatedProducts.length > 0 ? translatedRelatedProducts : relatedProducts).length > 0"
                class="mt-12 pt-12 border-t border-gray-200"
            >
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6 tracking-tight">
                    {{ translated.relatedProducts || texts.relatedProducts }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <ProductCard
                        v-for="relatedProduct in (translatedRelatedProducts.length > 0 ? translatedRelatedProducts : relatedProducts)"
                        :key="relatedProduct.id"
                        :product="relatedProduct"
                    />
                </div>
            </div>
        </div>
        </div>
    </div>
</template>
