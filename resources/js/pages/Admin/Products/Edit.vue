<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Upload, X, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    product: any;
    brands: any[];
    categories: any[];
}>();

const form = useForm({
    name: props.product.name,
    description: props.product.description || '',
    short_description: props.product.short_description || '',
    brand_id: props.product.brand_id,
    category_id: props.product.category_id || '',
    price: props.product.price,
    sale_price: props.product.sale_price || '',
    price_per_capsule: props.product.price_per_capsule || '',
    price_per_mg: props.product.price_per_mg || '',
    sku: props.product.sku || '',
    stock_quantity: props.product.stock_quantity || 0,
    in_stock: props.product.in_stock,
    image: null as File | null,
    images: [] as File[],
    is_featured: props.product.is_featured,
    is_best_seller: props.product.is_best_seller,
    is_new_arrival: props.product.is_new_arrival,
    is_customer_favorite: props.product.is_customer_favorite,
    is_active: props.product.is_active,
    sort_order: props.product.sort_order || 0,
    supplement_facts: props.product.supplement_facts || '',
    ingredients: props.product.ingredients || '',
    directions: props.product.directions || '',
    warnings: props.product.warnings || '',
    country_of_origin: props.product.country_of_origin || '',
    manufacturer: props.product.manufacturer || '',
});

const imagePreview = ref<string | null>(props.product.image);
const imagesPreview = ref<string[]>(props.product.images || []);

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
    imagePreview.value = props.product.image;
};

const removeImageFromArray = (index: number) => {
    // If it's an existing image (string URL), we need to handle it differently
    if (typeof imagesPreview.value[index] === 'string' && imagesPreview.value[index].startsWith('http')) {
        // This is an existing image, we'll need to track deletions separately
        imagesPreview.value.splice(index, 1);
    } else {
        // This is a new upload
        form.images.splice(index, 1);
        imagesPreview.value.splice(index, 1);
    }
};

const submit = () => {
    form.put(`/admin/products/${props.product.id}`, {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Edit Product - Admin" />
        
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Product</h1>
                    <p class="mt-1 text-sm text-gray-600">Update product information</p>
                </div>

                <form @submit.prevent="submit" class="bg-white dark:bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Short Description
                                        </label>
                                        <textarea
                                            v-model="form.short_description"
                                            rows="3"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                        ></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Brand *
                                            </label>
                                            <select
                                                v-model="form.brand_id"
                                                required
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Category
                                            </label>
                                            <select
                                                v-model="form.category_id"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                SKU
                                            </label>
                                            <input
                                                v-model="form.sku"
                                                type="text"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Overview</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Features</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ingredients</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Dosage/Suggested Use</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Warnings</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Details</h2>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
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

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Images</h2>
                                <div class="space-y-4">
                                    <!-- Main Image -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-50">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <Upload class="w-8 h-8 text-gray-400 mb-2" />
                                                <p class="text-sm text-gray-600">Click to upload new image</p>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-50">
                                            <div class="flex flex-col items-center justify-center">
                                                <Plus class="w-6 h-6 text-gray-400 mb-1" />
                                                <p class="text-xs text-gray-600">Add more images</p>
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors inline-block"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 text-sm font-medium text-white bg-black hover:bg-gray-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed uppercase tracking-wide"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

