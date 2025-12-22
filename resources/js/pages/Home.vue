<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import ProductCarousel from '@/components/ProductCarousel.vue';
import HeroCarousel from '@/components/HeroCarousel.vue';
import PublicNav from '@/components/PublicNav.vue';
import CartSidebarWrapper from '@/components/CartSidebarWrapper.vue';
import { CheckCircle, Shield, Award, Package } from 'lucide-vue-next';

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

const props = defineProps<{
    carouselSlides: any[];
    bestSellers: any[];
    newArrivals: any[];
    customerFavorites: any[];
    multiProductMode: boolean;
    featuredProduct?: any;
}>();

// Transform carousel slides from database to component format
const heroSlides = computed(() => {
    return props.carouselSlides.map(slide => ({
        id: slide.id,
        image: slide.image,
        video: slide.video,
        media_type: slide.media_type || 'image',
        title: slide.title,
        subtitle: slide.subtitle,
        link: slide.link,
        buttonText: slide.button_text,
    }));
});
</script>

<template>
    <Head title="Nantosha Import & Export Division - 南東舎輸出入部 | Genuine Ivermectin Capsules" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <CartSidebarWrapper />
        
        <!-- Hero Carousel -->
        <section v-if="heroSlides.length > 0" class="w-full">
            <HeroCarousel :slides="heroSlides" :auto-play-interval="5000" />
        </section>

        <!-- Hero Section -->
        <section class="bg-white py-12 md:py-16">
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

        <!-- Best Sellers - Multiple Products Mode -->
        <section
            v-if="multiProductMode && bestSellers.length > 0"
            class="py-12 md:py-16 bg-white"
        >
            <div class="container mx-auto px-4">
                <ProductCarousel
                    title="Our Best Sellers"
                    :items="bestSellers"
                    :items-per-view="4"
                />
            </div>
        </section>

        <!-- Single Featured Product - Single Product Mode -->
        <section
            v-if="!multiProductMode && featuredProduct"
            class="py-12 md:py-16 bg-white"
        >
            <div class="container mx-auto px-4 max-w-6xl">
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                        <!-- Large Product Image -->
                        <div class="lg:col-span-1">
                            <div class="aspect-square bg-gray-50 relative overflow-hidden">
                                <img
                                    v-if="featuredProduct.image"
                                    :src="featuredProduct.image"
                                    :alt="featuredProduct.name"
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
                            <div v-if="featuredProduct.brand" class="text-xs text-gray-500 uppercase tracking-wide mb-2">
                                {{ featuredProduct.brand.name }}
                            </div>
                            <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 mb-4 tracking-tight">
                                {{ featuredProduct.name }}
                            </h2>
                            <p v-if="featuredProduct.short_description" class="text-gray-600 mb-6 leading-relaxed">
                                {{ featuredProduct.short_description }}
                            </p>
                            
                            <!-- Price -->
                            <div class="mb-6">
                                <div class="flex items-baseline gap-3 mb-2">
                                    <span
                                        v-if="featuredProduct.sale_price"
                                        class="text-3xl font-bold text-gray-900"
                                    >
                                        {{ formatPrice(featuredProduct.sale_price) }}
                                    </span>
                                    <span
                                        :class="[
                                            'text-3xl font-bold',
                                            featuredProduct.sale_price ? 'text-gray-400 line-through text-2xl' : 'text-gray-900'
                                        ]"
                                    >
                                        {{ formatPrice(featuredProduct.price) }}
                                    </span>
                                </div>
                                <p v-if="featuredProduct.sale_price" class="text-sm text-green-600 font-medium">
                                    Save {{ formatPrice(featuredProduct.price - featuredProduct.sale_price) }}
                                </p>
                            </div>
                            
                            <!-- Stock Status -->
                            <div class="mb-6">
                                <span
                                    :class="[
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                        featuredProduct.in_stock
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ featuredProduct.in_stock ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                            
                            <!-- CTA Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <Link
                                    :href="`/products/${featuredProduct.slug}`"
                                    class="inline-block bg-gray-900 text-white px-8 py-4 text-base font-semibold hover:bg-gray-800 transition-colors uppercase tracking-wide text-center"
                                >
                                    View Product Details
                                </Link>
                                <Link
                                    v-if="featuredProduct.in_stock"
                                    :href="`/products/${featuredProduct.slug}`"
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

        <!-- Key Features -->
        <section class="py-12 md:py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-10 text-center tracking-tight">
                    Why Choose Our Ivermectin?
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Shield class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Verified Quality</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            99%+ purity verified by Japanese research institutions. Quality reports available upon request.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Package class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Best Pricing</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Lowest price: 140 JPY per capsule (9.333 JPY / mg). Volume discounts available.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Award class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Trusted Manufacturer</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Manufactured by Dr. Allan Landrito, award-winning physician and co-author of "Ivermectin: Testimony from the World's Clinicians".
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Highlights -->
        <section class="py-12 md:py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                        Product Information
                    </h2>
                    <div class="bg-white border border-gray-200 p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Ingredients per Capsule (15mg)</h3>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li>• Ivermectin B1a: 14.85mg</li>
                                    <li>• Ivermectin B1b: 0.15mg</li>
                                    <li>• Excipient: Anhydrous Dextrose 485mg</li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Product Details</h3>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li>• Country of Origin: Philippines</li>
                                    <li>• Manufacturer: Dr. Allan Landrito</li>
                                    <li>• Purity: 99%+ (Japanese lab verified)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">Approved Indications</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Strongyloidiasis, Scabies (per MHLW/PMDA). Global research suggests potential antiviral and anti-tumor effects, though not officially recognized by the Japanese MHLW.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Tiers -->
        <section class="py-12 md:py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                    Pricing (Excluding Tax)
                </h2>
                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div
                            v-for="tier in [
                                { qty: 50, price: 12800, perCap: 256 },
                                { qty: 100, price: 19800, perCap: 198 },
                                { qty: 200, price: 36800, perCap: 184 },
                                { qty: 300, price: 51000, perCap: 170 },
                                { qty: 500, price: 80000, perCap: 160 },
                                { qty: 1000, price: 150000, perCap: 150 },
                            ]"
                            :key="tier.qty"
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
                    <p class="text-center text-xs text-gray-600 mt-6">
                        <strong>Note:</strong> Doctors purchasing for patient prescriptions require "Yakkan Shoumei" (Medicine Import Confirmation).
                    </p>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-12 md:py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 text-center tracking-tight">
                    How Personal Import Works
                </h2>
                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center mx-auto mb-4 font-semibold text-sm">
                                1
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Select Quantity</h3>
                            <p class="text-xs text-gray-600">Choose your package size</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center mx-auto mb-4 font-semibold text-sm">
                                2
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Enter Info</h3>
                            <p class="text-xs text-gray-600">Shipping & Health Check Sheet</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center mx-auto mb-4 font-semibold text-sm">
                                3
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Bank Transfer</h3>
                            <p class="text-xs text-gray-600">Complete payment</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center mx-auto mb-4 font-semibold text-sm">
                                4
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Receive Order</h3>
                            <p class="text-xs text-gray-600">3-10 days delivery</p>
                        </div>
                    </div>
                    <div class="mt-8 text-center">
                        <Link
                            href="/how-to-order"
                            class="inline-block text-sm text-gray-900 font-medium hover:text-gray-700 transition-colors"
                        >
                            Learn more about the ordering process →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

            <!-- Customer Favorites - Multiple Products Mode -->
            <section
                v-if="multiProductMode && customerFavorites.length > 0"
                class="py-12 md:py-16 bg-gray-50"
            >
                <div class="container mx-auto px-4">
                    <ProductCarousel
                        title="Customer's Favourite"
                        :items="customerFavorites"
                        :items-per-view="4"
                    />
                </div>
            </section>

            <!-- New Arrivals - Multiple Products Mode -->
            <section
                v-if="multiProductMode && newArrivals.length > 0"
                class="py-12 md:py-16 bg-white"
            >
                <div class="container mx-auto px-4">
                    <ProductCarousel
                        title="New arrivals"
                        :items="newArrivals"
                        :items-per-view="4"
                    />
                </div>
            </section>
    </div>
</template>
