<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Plus, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    type: 'hero',
    title: '',
    subtitle: '',
    content: null as any,
    background_color: 'white',
    sort_order: 0,
    is_active: true,
});

// Hero section data
const heroBadges = ref<Array<{text: string, icon: string}>>([]);
const heroButtons = ref<Array<{text: string, href: string, style: string}>>([]);

// Features section data
const featuresItems = ref<Array<{icon: string, title: string, description: string}>>([]);

// Product info section data
const ingredients = ref<Array<{name: string, amount: string}>>([]);
const details = ref<Array<{label: string, value: string}>>([]);
const indications = ref('');

// Pricing section data
const pricingTiers = ref<Array<{qty: number, price: number, perCap: number}>>([]);
const pricingNote = ref('');

// How it works section data
const steps = ref<Array<{number: number, title: string, description: string}>>([]);
const linkText = ref('');
const linkHref = ref('');

const iconOptions = ['CheckCircle', 'Shield', 'Award', 'Package'];

// Build content object from form data
const buildContent = () => {
    switch (form.type) {
        case 'hero':
            return {
                badges: heroBadges.value,
                buttons: heroButtons.value
            };
        case 'features':
            return {
                items: featuresItems.value
            };
        case 'product_info':
            return {
                ingredients: ingredients.value,
                details: details.value,
                indications: indications.value
            };
        case 'pricing':
            return {
                tiers: pricingTiers.value,
                note: pricingNote.value
            };
        case 'how_it_works':
            return {
                steps: steps.value,
                linkText: linkText.value,
                linkHref: linkHref.value
            };
        default:
            return {};
    }
};


// Add/Remove functions
const addHeroBadge = () => heroBadges.value.push({ text: '', icon: 'CheckCircle' });
const removeHeroBadge = (index: number) => heroBadges.value.splice(index, 1);
const addHeroButton = () => heroButtons.value.push({ text: '', href: '', style: 'primary' });
const removeHeroButton = (index: number) => heroButtons.value.splice(index, 1);

const addFeature = () => featuresItems.value.push({ icon: 'Shield', title: '', description: '' });
const removeFeature = (index: number) => featuresItems.value.splice(index, 1);

const addIngredient = () => ingredients.value.push({ name: '', amount: '' });
const removeIngredient = (index: number) => ingredients.value.splice(index, 1);
const addDetail = () => details.value.push({ label: '', value: '' });
const removeDetail = (index: number) => details.value.splice(index, 1);

const addPricingTier = () => pricingTiers.value.push({ qty: 0, price: 0, perCap: 0 });
const removePricingTier = (index: number) => pricingTiers.value.splice(index, 1);

const addStep = () => steps.value.push({ number: steps.value.length + 1, title: '', description: '' });
const removeStep = (index: number) => {
    steps.value.splice(index, 1);
    steps.value.forEach((step, idx) => step.number = idx + 1);
};

// Reset data when section type changes
watch(() => form.type, () => {
    heroBadges.value = [];
    heroButtons.value = [];
    featuresItems.value = [];
    ingredients.value = [];
    details.value = [];
    indications.value = '';
    pricingTiers.value = [];
    pricingNote.value = '';
    steps.value = [];
    linkText.value = '';
    linkHref.value = '';
});

const submit = () => {
    form.content = buildContent();
    
    form.post('/admin/homepage-sections', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Create Homepage Section - Admin" />
        
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Create Homepage Section</h1>
                            <p class="text-gray-600">Add a new content section to your homepage</p>
                        </div>
                        <Link
                            href="/admin/homepage-sections"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            ← Back to Sections
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                    <div class="space-y-6">
                        <!-- Section Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Section Type *
                            </label>
                            <select
                                v-model="form.type"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                            >
                                <optgroup label="Content Sections">
                                    <option value="hero">Hero</option>
                                    <option value="features">Features</option>
                                    <option value="product_info">Product Info</option>
                                    <option value="pricing">Pricing</option>
                                    <option value="how_it_works">How It Works</option>
                                    <option value="custom">Custom</option>
                                </optgroup>
                                <optgroup label="Static Sections">
                                    <option value="carousel">Carousel</option>
                                    <option value="best_sellers">Best Sellers</option>
                                    <option value="new_arrivals">New Arrivals</option>
                                    <option value="customer_favorites">Customer Favorites</option>
                                    <option value="featured_product">Featured Product</option>
                                </optgroup>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                <span v-if="['carousel', 'best_sellers', 'new_arrivals', 'customer_favorites', 'featured_product'].includes(form.type)">
                                    Static sections are automatically populated with content. You can customize the title and sort order.
                                </span>
                                <span v-else>
                                    Content sections allow you to create custom content with full control.
                                </span>
                            </p>
                            <p v-if="form.errors.type" class="mt-1 text-xs text-red-600">
                                {{ form.errors.type }}
                            </p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., Nantosha Import & Export Division"
                            />
                            <p class="mt-1 text-xs text-gray-500">Content will be automatically translated to Japanese when viewed</p>
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Subtitle
                            </label>
                            <textarea
                                v-model="form.subtitle"
                                rows="3"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                                placeholder="e.g., Genuine Ivermectin Capsules (15mg)"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">Content will be automatically translated to Japanese when viewed</p>
                            <p v-if="form.errors.subtitle" class="mt-1 text-xs text-red-600">
                                {{ form.errors.subtitle }}
                            </p>
                        </div>

                        <!-- Hero Section Content -->
                        <template v-if="form.type === 'hero'">
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hero Content</h3>
                                <p class="text-xs text-gray-500 mb-4">Content will be automatically translated to Japanese when viewed</p>
                                
                                <!-- Badges -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Badges</label>
                                        <button type="button" @click="addHeroBadge" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                            <Plus class="w-4 h-4" />
                                            Add Badge
                                        </button>
                                    </div>
                                    <div v-for="(badge, index) in heroBadges" :key="index" class="flex gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <input
                                                v-model="badge.text"
                                                type="text"
                                                placeholder="Badge text"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg mb-2"
                                            />
                                            <select v-model="badge.icon" class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg">
                                                <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeHeroBadge(index)" class="self-start mt-1 text-red-600 hover:text-red-800">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Buttons</label>
                                        <button type="button" @click="addHeroButton" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                            <Plus class="w-4 h-4" />
                                            Add Button
                                        </button>
                                    </div>
                                    <div v-for="(button, index) in heroButtons" :key="index" class="flex gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1 space-y-2">
                                            <input
                                                v-model="button.text"
                                                type="text"
                                                placeholder="Button text"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                            />
                                            <input
                                                v-model="button.href"
                                                type="text"
                                                placeholder="Link URL (e.g., /products)"
                                                class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                            />
                                            <select v-model="button.style" class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg">
                                                <option value="primary">Primary (Black)</option>
                                                <option value="secondary">Secondary (White)</option>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeHeroButton(index)" class="self-start text-red-600 hover:text-red-800">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </template>

                        <!-- Features Section Content -->
                        <template v-if="form.type === 'features'">
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Features</h3>
                                <p class="text-xs text-gray-500 mb-4">Content will be automatically translated to Japanese when viewed</p>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-gray-700">Feature Items</label>
                                    <button type="button" @click="addFeature" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                        <Plus class="w-4 h-4" />
                                        Add Feature
                                    </button>
                                </div>
                                <div v-for="(item, index) in featuresItems" :key="index" class="flex gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1 space-y-2">
                                        <select v-model="item.icon" class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg">
                                            <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                        </select>
                                        <input
                                            v-model="item.title"
                                            type="text"
                                            placeholder="Feature title"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <textarea
                                            v-model="item.description"
                                            rows="2"
                                            placeholder="Feature description"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        ></textarea>
                                    </div>
                                    <button type="button" @click="removeFeature(index)" class="self-start text-red-600 hover:text-red-800">
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                        </template>

                        <!-- Product Info Section Content -->
                        <template v-if="form.type === 'product_info'">
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Information</h3>
                                <p class="text-xs text-gray-500 mb-4">Content will be automatically translated to Japanese when viewed</p>
                                
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Ingredients</label>
                                        <button type="button" @click="addIngredient" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                            <Plus class="w-4 h-4" />
                                            Add Ingredient
                                        </button>
                                    </div>
                                    <div v-for="(ing, index) in ingredients" :key="index" class="flex gap-3 mb-2">
                                        <input
                                            v-model="ing.name"
                                            type="text"
                                            placeholder="Ingredient name"
                                            class="flex-1 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <input
                                            v-model="ing.amount"
                                            type="text"
                                            placeholder="Amount (e.g., 14.85mg)"
                                            class="w-32 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <button type="button" @click="removeIngredient(index)" class="text-red-600 hover:text-red-800">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Details</label>
                                        <button type="button" @click="addDetail" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                            <Plus class="w-4 h-4" />
                                            Add Detail
                                        </button>
                                    </div>
                                    <div v-for="(detail, index) in details" :key="index" class="flex gap-3 mb-2">
                                        <input
                                            v-model="detail.label"
                                            type="text"
                                            placeholder="Label (e.g., Country of Origin)"
                                            class="flex-1 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <input
                                            v-model="detail.value"
                                            type="text"
                                            placeholder="Value"
                                            class="flex-1 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <button type="button" @click="removeDetail(index)" class="text-red-600 hover:text-red-800">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Approved Indications</label>
                                    <textarea
                                        v-model="indications"
                                        rows="3"
                                        class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        placeholder="e.g., Strongyloidiasis, Scabies..."
                                    ></textarea>
                                </div>
                            </div>

                        </template>

                        <!-- Pricing Section Content -->
                        <template v-if="form.type === 'pricing'">
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Tiers</h3>
                                <p class="text-xs text-gray-500 mb-4">Content will be automatically translated to Japanese when viewed</p>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-gray-700">Pricing Tiers</label>
                                    <button type="button" @click="addPricingTier" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                        <Plus class="w-4 h-4" />
                                        Add Tier
                                    </button>
                                </div>
                                <div v-for="(tier, index) in pricingTiers" :key="index" class="flex gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                                    <input
                                        v-model.number="tier.qty"
                                        type="number"
                                        placeholder="Quantity"
                                        class="w-24 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                    />
                                    <input
                                        v-model.number="tier.price"
                                        type="number"
                                        placeholder="Total Price (JPY)"
                                        class="flex-1 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                    />
                                    <input
                                        v-model.number="tier.perCap"
                                        type="number"
                                        placeholder="Per Cap (JPY)"
                                        class="w-32 border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                    />
                                    <button type="button" @click="removePricingTier(index)" class="text-red-600 hover:text-red-800">
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                                    <textarea
                                        v-model="pricingNote"
                                        rows="2"
                                        class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        placeholder="Additional note about pricing"
                                    ></textarea>
                                </div>
                            </div>

                        </template>

                        <!-- How It Works Section Content -->
                        <template v-if="form.type === 'how_it_works'">
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Steps</h3>
                                <p class="text-xs text-gray-500 mb-4">Content will be automatically translated to Japanese when viewed</p>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-sm font-medium text-gray-700">Steps</label>
                                    <button type="button" @click="addStep" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800">
                                        <Plus class="w-4 h-4" />
                                        Add Step
                                    </button>
                                </div>
                                <div v-for="(step, index) in steps" :key="index" class="flex gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1 space-y-2">
                                        <div class="text-sm font-medium text-gray-700">Step {{ step.number }}</div>
                                        <input
                                            v-model="step.title"
                                            type="text"
                                            placeholder="Step title"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                        <textarea
                                            v-model="step.description"
                                            rows="2"
                                            placeholder="Step description"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        ></textarea>
                                    </div>
                                    <button type="button" @click="removeStep(index)" class="self-start text-red-600 hover:text-red-800">
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                                <div class="mt-4 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Learn More Link Text</label>
                                        <input
                                            v-model="linkText"
                                            type="text"
                                            placeholder="e.g., Learn more about the ordering process"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Learn More Link URL</label>
                                        <input
                                            v-model="linkHref"
                                            type="text"
                                            placeholder="e.g., /how-to-order"
                                            class="w-full border border-gray-300 px-3 py-2 text-sm rounded-lg"
                                        />
                                    </div>
                                </div>
                            </div>

                        </template>

                        <!-- Background Color -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Background Color
                            </label>
                            <select
                                v-model="form.background_color"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                            >
                                <option value="white">White</option>
                                <option value="gray-50">Gray (Light)</option>
                            </select>
                            <p v-if="form.errors.background_color" class="mt-1 text-xs text-red-600">
                                {{ form.errors.background_color }}
                            </p>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sort Order
                            </label>
                            <input
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="w-full border border-gray-300 px-3 py-2 text-sm text-gray-900 bg-white focus:outline-none focus:border-gray-900 rounded-lg"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">
                                {{ form.errors.sort_order }}
                            </p>
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-gray-900"
                                />
                                <span class="ml-2 text-sm text-gray-700">Active (visible on homepage)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end gap-4 border-t border-gray-200 pt-6">
                        <Link
                            href="/admin/homepage-sections"
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors rounded-lg"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed rounded-lg uppercase tracking-wide"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Section' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
