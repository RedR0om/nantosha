<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import PublicNav from '@/components/PublicNav.vue';
import CartSidebarWrapper from '@/components/CartSidebarWrapper.vue';
import { CheckCircle } from 'lucide-vue-next';

const props = defineProps<{
    product: any;
    relatedProducts: any[];
}>();

const page = usePage();
const quantity = ref(1);
const isAdding = ref(false);
const selectedImageIndex = ref(0);
const activeTab = ref('overview');

// Get all images (main image + additional images)
const allImages = computed(() => {
    const images = [];
    if (props.product.image) {
        images.push(props.product.image);
    }
    if (props.product.images && Array.isArray(props.product.images)) {
        images.push(...props.product.images);
    }
    return images;
});

const currentImage = computed(() => {
    return allImages.value[selectedImageIndex.value] || props.product.image;
});

const selectImage = (index: number) => {
    selectedImageIndex.value = index;
};

const tabs = [
    { id: 'overview', label: 'Product Overview' },
    { id: 'features', label: 'Product Features' },
    { id: 'ingredients', label: 'Ingredients' },
    { id: 'dosage', label: 'Dosage/Suggested Use' },
    { id: 'warnings', label: 'Warnings' },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

const addToCart = () => {
    if (!props.product.in_stock || quantity.value <= 0) {
        return;
    }

    isAdding.value = true;

    router.post('/cart', {
        product_id: props.product.id,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isAdding.value = false;
            // Open cart sidebar
            window.dispatchEvent(new CustomEvent('openCartSidebar'));
        },
        onError: (errors) => {
            isAdding.value = false;
            console.error('Error adding to cart:', errors);
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
    <Head :title="`${product.name} - Nantosha Import & Export Division`" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <CartSidebarWrapper />
        <div class="py-8 md:py-12">
        <div class="container mx-auto px-4 max-w-7xl">
            <!-- Product Header -->
            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-3 tracking-tight">
                    {{ product.name }}
                </h1>
                <p v-if="product.price_per_capsule || product.price_per_mg" class="text-base md:text-lg text-gray-600 mb-4">
                    <span v-if="product.price_per_capsule">
                        Lowest Price: {{ formatPrice(product.price_per_capsule) }} per capsule
                    </span>
                    <span v-if="product.price_per_capsule && product.price_per_mg"> (</span>
                    <span v-if="product.price_per_mg">
                        {{ formatPrice(product.price_per_mg) }} / mg
                    </span>
                    <span v-if="product.price_per_capsule && product.price_per_mg">)</span>
                </p>
                <p v-else-if="product.short_description" class="text-base md:text-lg text-gray-600 mb-4">
                    {{ product.short_description }}
                </p>
                <p v-else-if="product.price" class="text-base md:text-lg text-gray-600 mb-4">
                    Price: {{ formatPrice(product.sale_price || product.price) }}
                </p>
                <div class="flex items-center gap-2 text-gray-700">
                    <CheckCircle class="w-4 h-4" />
                    <span class="text-sm font-medium">Verified by Japanese research institutions</span>
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
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"
                            >
                                No Image
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
                                    :alt="`${product.name} - Image ${index + 1}`"
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
                                    v-for="tab in tabs"
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
                                <div v-if="product.description" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ product.description }}
                                </div>
                                <div v-else-if="product.short_description" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ product.short_description }}
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    No product overview available.
                                </div>
                            </div>

                            <!-- Product Features -->
                            <div v-if="activeTab === 'features'">
                                <div v-if="product.supplement_facts" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ product.supplement_facts }}
                                </div>
                                <div v-else class="space-y-4">
                                    <div v-if="product.brand" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Brand</h4>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.brand.name }}</p>
                                        <p v-if="product.brand.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            {{ product.brand.description }}
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div v-if="product.country_of_origin" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">Country of Origin</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.country_of_origin }}</p>
                                        </div>
                                        <div v-if="product.manufacturer" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">Manufacturer</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.manufacturer }}</p>
                                        </div>
                                        <div v-if="product.sku" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">SKU</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.sku }}</p>
                                        </div>
                                        <div v-if="product.category" class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1 text-xs">Category</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.category.name }}</p>
                                        </div>
                                    </div>
                                    <div class="pt-4">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <CheckCircle class="w-3 h-3 text-gray-700 dark:text-gray-300" />
                                            Verified by Japanese research institutions
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingredients -->
                            <div v-if="activeTab === 'ingredients'">
                                <div v-if="product.ingredients" class="space-y-3">
                                    <div
                                        v-for="(line, index) in product.ingredients.split('\n').filter((l: string) => l.trim())"
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
                                            Verified by Japanese research institutions
                                        </p>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    No ingredients information available.
                                </div>
                            </div>

                            <!-- Dosage/Suggested Use -->
                            <div v-if="activeTab === 'dosage'">
                                <div v-if="product.directions" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ product.directions }}
                                </div>
                                <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    No dosage information available.
                                </div>
                            </div>

                            <!-- Warnings -->
                            <div v-if="activeTab === 'warnings'">
                                <div v-if="product.warnings" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ product.warnings }}
                                </div>
                                <div v-else-if="product.supplement_facts" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ product.supplement_facts }}
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
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 tracking-tight">Pricing</h2>
                        
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
                                Save {{ formatPrice(product.price - product.sale_price) }}
                            </p>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input
                                v-model.number="quantity"
                                type="number"
                                min="1"
                                :max="product.stock_quantity || 999"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                            />
                        </div>

                        <div class="bg-gray-50 border border-gray-200 p-4 mb-6">
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span class="font-semibold">{{ formatPrice((product.sale_price || product.price) * quantity) }}</span>
                                </div>
                                <div v-if="product.stock_quantity" class="text-xs text-gray-500">
                                    {{ product.stock_quantity }} in stock
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
                            :disabled="!product.in_stock || quantity <= 0 || isAdding"
                            class="w-full bg-black text-white py-3 text-sm font-medium hover:bg-gray-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed disabled:hover:bg-gray-300 mb-4 uppercase tracking-wide"
                        >
                            {{ isAdding ? 'Adding...' : (product.in_stock ? 'Add to cart' : 'Sold out') }}
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <div
                v-if="relatedProducts.length > 0"
                class="mt-12 pt-12 border-t border-gray-200"
            >
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6 tracking-tight">
                    Related Products
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <ProductCard
                        v-for="relatedProduct in relatedProducts"
                        :key="relatedProduct.id"
                        :product="relatedProduct"
                    />
                </div>
            </div>
        </div>
        </div>
    </div>
</template>
