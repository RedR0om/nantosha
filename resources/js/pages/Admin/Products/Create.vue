<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Upload, X, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    brands: any[];
    categories: any[];
}>();

const form = useForm({
    name: '',
    description: '',
    short_description: '',
    brand_id: '',
    category_id: '',
    price: '',
    sale_price: '',
    price_per_capsule: '',
    price_per_mg: '',
    is_bottle_based: false,
    capsules_per_bottle: 50,
    bottle_pricing_tiers: [] as Array<{capsules: number, price: number, price_per_capsule: number}>,
    bottles_only: false,
    sku: '',
    stock_quantity: 0,
    in_stock: true,
    image: null as File | null,
    images: [] as File[],
    is_featured: false,
    is_best_seller: false,
    is_new_arrival: false,
    is_customer_favorite: false,
    is_active: true,
    sort_order: 0,
    supplement_facts: '',
    ingredients: '',
    directions: '',
    warnings: '',
    country_of_origin: '',
    manufacturer: '',
});

const imagePreview = ref<string | null>(null);
const imagesPreview = ref<string[]>([]);

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const handleImagesChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        form.images = files;
        
        imagesPreview.value = [];
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagesPreview.value.push(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const removeImageFromArray = (index: number) => {
    form.images.splice(index, 1);
    imagesPreview.value.splice(index, 1);
};

const addPricingTier = () => {
    form.bottle_pricing_tiers.push({
        capsules: 0,
        price: 0,
        price_per_capsule: 0,
    });
};

const removePricingTier = (index: number) => {
    form.bottle_pricing_tiers.splice(index, 1);
};

const calculatePricePerCapsule = (tierIndex: number) => {
    const tier = form.bottle_pricing_tiers[tierIndex];
    if (tier.capsules > 0 && tier.price > 0) {
        tier.price_per_capsule = Math.round((tier.price / tier.capsules) * 100) / 100;
    }
};

const submit = () => {
    form.post('/admin/products', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            imagesPreview.value = [];
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Create Product - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Create New Product</h1>
                            <p class="text-gray-600">Add a new product to your store catalog</p>
                        </div>
                        <Link
                            href="/admin/products"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            ← Back to Products
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="bg-white dark:bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Basic Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Product Name *
                                        </label>
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                        />
                                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Short Description
                                        </label>
                                        <textarea
                                            v-model="form.short_description"
                                            rows="3"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                        ></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Description
                                        </label>
                                        <textarea
                                            v-model="form.description"
                                            rows="6"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Brand *
                                            </label>
                                            <select
                                                v-model="form.brand_id"
                                                required
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 dark:text-gray-900 dark:bg-white"
                                            >
                                                <option value="" class="text-gray-900">Select Brand</option>
                                                <option
                                                    v-for="brand in brands"
                                                    :key="brand.id"
                                                    :value="brand.id"
                                                    class="text-gray-900"
                                                >
                                                    {{ brand.name }}
                                                </option>
                                            </select>
                                            <p v-if="form.errors.brand_id" class="mt-1 text-xs text-red-600">
                                                {{ form.errors.brand_id }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Category
                                            </label>
                                            <select
                                                v-model="form.category_id"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 dark:text-gray-900 dark:bg-white"
                                            >
                                                <option value="" class="text-gray-900">Select Category</option>
                                                <option
                                                    v-for="category in categories"
                                                    :key="category.id"
                                                    :value="category.id"
                                                    class="text-gray-900"
                                                >
                                                    {{ category.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Price *
                                            </label>
                                            <input
                                                v-model="form.price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                required
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            />
                                            <p v-if="form.errors.price" class="mt-1 text-xs text-red-600">
                                                {{ form.errors.price }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Sale Price
                                            </label>
                                            <input
                                                v-model="form.sale_price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                SKU
                                            </label>
                                            <input
                                                v-model="form.sku"
                                                type="text"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Stock Quantity
                                            </label>
                                            <input
                                                v-model="form.stock_quantity"
                                                type="number"
                                                min="0"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Overview -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Product Overview</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Description
                                        </label>
                                        <textarea
                                            v-model="form.description"
                                            rows="6"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="Enter product overview/description"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Features -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Product Features</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Supplement Facts
                                        </label>
                                        <textarea
                                            v-model="form.supplement_facts"
                                            rows="4"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="Enter supplement facts or product features"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingredients -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Ingredients</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Ingredients
                                        </label>
                                        <textarea
                                            v-model="form.ingredients"
                                            rows="6"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="e.g., Ivermectin B1a: 14.85mg&#10;Ivermectin B1b: 0.15mg&#10;Excipient: Anhydrous Dextrose 485mg"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">Enter each ingredient on a new line</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dosage/Suggested Use -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Dosage/Suggested Use</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Directions
                                        </label>
                                        <textarea
                                            v-model="form.directions"
                                            rows="4"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="Enter dosage instructions and suggested use"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Warnings -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Warnings</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Warnings
                                        </label>
                                        <textarea
                                            v-model="form.warnings"
                                            rows="6"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="Enter product warnings and safety information"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Product Details</h2>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Price per Capsule (JPY)
                                            </label>
                                            <input
                                                v-model="form.price_per_capsule"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                                placeholder="e.g., 140"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                Price per mg (JPY)
                                            </label>
                                            <input
                                                v-model="form.price_per_mg"
                                                type="number"
                                                step="0.0001"
                                                min="0"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                                placeholder="e.g., 9.333"
                                            />
                                        </div>
                                    </div>

                                    <!-- Bottle-Based Pricing -->
                                    <div class="border-t border-gray-200 pt-4 mt-4">
                                        <div class="flex items-center gap-3 mb-4">
                                            <input
                                                type="checkbox"
                                                id="is_bottle_based"
                                                v-model="form.is_bottle_based"
                                                class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                                            />
                                            <label for="is_bottle_based" class="text-sm font-medium text-gray-700 dark:text-gray-700">
                                                Enable Bottle-Based Pricing
                                            </label>
                                        </div>

                                        <div v-if="form.is_bottle_based" class="space-y-4">
                                            <div class="flex items-center gap-3 mb-2">
                                                <input
                                                    type="checkbox"
                                                    id="bottles_only"
                                                    v-model="form.bottles_only"
                                                    class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                                                />
                                                <label for="bottles_only" class="text-sm font-medium text-gray-700 dark:text-gray-700">
                                                    Only Allow Bottle Purchases (Disable Per-Capsule Buying)
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-4">
                                                When enabled, customers can only purchase bottles through pricing tiers, not individual capsules.
                                            </p>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                                    Capsules per Bottle
                                                </label>
                                                <input
                                                    v-model="form.capsules_per_bottle"
                                                    type="number"
                                                    min="1"
                                                    class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                                    placeholder="e.g., 50"
                                                />
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-700">
                                                        Pricing Tiers
                                                    </label>
                                                    <button
                                                        type="button"
                                                        @click="addPricingTier"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-gray-900 hover:bg-gray-800 rounded"
                                                    >
                                                        <Plus class="w-3 h-3" />
                                                        Add Tier
                                                    </button>
                                                </div>

                                                <div v-if="form.bottle_pricing_tiers.length === 0" class="text-sm text-gray-500 mb-2">
                                                    No pricing tiers added. Click "Add Tier" to create pricing tiers.
                                                </div>

                                                <div v-for="(tier, index) in form.bottle_pricing_tiers" :key="index" class="border border-gray-200 rounded-lg p-4 mb-3 bg-white">
                                                    <div class="flex items-start justify-between mb-3">
                                                        <h4 class="text-sm font-medium text-gray-900">Tier {{ index + 1 }}</h4>
                                                        <button
                                                            type="button"
                                                            @click="removePricingTier(index)"
                                                            class="text-red-600 hover:text-red-800"
                                                        >
                                                            <X class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                Total Capsules
                                                            </label>
                                                            <input
                                                                v-model.number="tier.capsules"
                                                                type="number"
                                                                min="1"
                                                                @input="calculatePricePerCapsule(index)"
                                                                class="w-full border border-gray-300 px-2 py-1.5 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                                                placeholder="e.g., 50"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                Total Price (JPY)
                                                            </label>
                                                            <input
                                                                v-model.number="tier.price"
                                                                type="number"
                                                                min="0"
                                                                step="0.01"
                                                                @input="calculatePricePerCapsule(index)"
                                                                class="w-full border border-gray-300 px-2 py-1.5 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                                                placeholder="e.g., 12800"
                                                            />
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                Price per Capsule (JPY)
                                                            </label>
                                                            <input
                                                                v-model.number="tier.price_per_capsule"
                                                                type="number"
                                                                step="0.01"
                                                                readonly
                                                                class="w-full border border-gray-300 px-2 py-1.5 text-sm text-gray-600 bg-gray-50"
                                                                placeholder="Auto-calculated"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Country of Origin
                                        </label>
                                        <input
                                            v-model="form.country_of_origin"
                                            type="text"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="e.g., Philippines"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Manufacturer
                                        </label>
                                        <textarea
                                            v-model="form.manufacturer"
                                            rows="3"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            placeholder="e.g., Dr. Allan Landrito (Director, Allan Landrito Molecular Nutrition Clinic / Co-author of &quot;Ivermectin: Testimony from the World's Clinicians&quot;)"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Images -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Images</h2>
                                <div class="space-y-4">
                                    <!-- Main Image -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Main Image
                                        </label>
                                        <div v-if="imagePreview" class="mb-2">
                                            <div class="relative inline-block">
                                                <img
                                                    :src="imagePreview"
                                                    alt="Preview"
                                                    class="w-32 h-32 object-cover border border-gray-200"
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeImage"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                >
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <Upload class="w-8 h-8 text-gray-400 mb-2" />
                                                <p class="text-sm text-gray-600">Click to upload</p>
                                                <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB</p>
                                            </div>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="handleImageChange"
                                                class="hidden"
                                            />
                                        </label>
                                        <p v-if="form.errors.image" class="mt-1 text-xs text-red-600">
                                            {{ form.errors.image }}
                                        </p>
                                    </div>

                                    <!-- Additional Images -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Additional Images
                                        </label>
                                        <div v-if="imagesPreview.length > 0" class="grid grid-cols-4 gap-2 mb-2">
                                            <div
                                                v-for="(preview, index) in imagesPreview"
                                                :key="index"
                                                class="relative"
                                            >
                                                <img
                                                    :src="preview"
                                                    alt="Preview"
                                                    class="w-full h-24 object-cover border border-gray-200"
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeImageFromArray(index)"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                >
                                                    <X class="w-3 h-3" />
                                                </button>
                                            </div>
                                        </div>
                                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                            <div class="flex flex-col items-center justify-center">
                                                <Plus class="w-6 h-6 text-gray-400 mb-1" />
                                                <p class="text-xs text-gray-600">Add multiple images</p>
                                            </div>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                multiple
                                                @change="handleImagesChange"
                                                class="hidden"
                                            />
                                        </label>
                                        <p v-if="form.errors.images" class="mt-1 text-xs text-red-600">
                                            {{ form.errors.images }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="bg-gray-50 dark:bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-900 mb-6 pb-3 border-b border-gray-200">Settings</h2>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.in_stock"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">In Stock</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_featured"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_best_seller"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_new_arrival"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">New Arrival</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_customer_favorite"
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Customer Favorite</span>
                                    </label>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-700 mb-2">
                                            Sort Order
                                        </label>
                                        <input
                                            v-model.number="form.sort_order"
                                            type="number"
                                            min="0"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end gap-4 border-t border-gray-200 pt-6">
                        <Link
                            href="/admin/products"
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed rounded-lg uppercase tracking-wide"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

