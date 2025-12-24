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

// Custom section data
const customContent = ref({
    html_content: '',
    image_url: '',
    image_alt: '',
    text_blocks: [] as Array<{title: string, content: string, alignment: string}>,
    buttons: [] as Array<{text: string, href: string, style: string, icon: string}>,
    lists: [] as Array<{title: string, items: string[], style: string}>,
    grid_items: [] as Array<{title: string, description: string, image_url: string, link: string, icon: string}>,
    badges: [] as Array<{text: string, icon: string}>,
    features: [] as Array<{icon: string, title: string, description: string}>,
    ingredients: [] as Array<{name: string, amount: string}>,
    details: [] as Array<{label: string, value: string}>,
    pricing_tiers: [] as Array<{qty: number, price: number, perCap: number}>,
    pricing_note: '',
    steps: [] as Array<{number: number, title: string, description: string}>,
    step_link_text: '',
    step_link_href: '',
    custom_css: '',
    spacing: 'normal',
    max_width: 'full',
    text_alignment: 'left'
});

const iconOptions = [
    'CheckCircle', 'Shield', 'Award', 'Package', 'Star', 'Heart', 
    'Check', 'X', 'AlertCircle', 'Info', 'TrendingUp', 'Zap', 
    'Truck', 'Clock', 'Globe', 'Lock', 'Unlock', 'Settings', 
    'Bell', 'Mail', 'Phone', 'MapPin', 'Calendar', 'FileText', 
    'ShoppingCart', 'CreditCard', 'DollarSign', 'ThumbsUp', 
    'ThumbsDown', 'Eye', 'EyeOff', 'Search', 'Filter', 'Menu', 
    'ArrowRight', 'ArrowLeft', 'ArrowUp', 'ArrowDown', 'ChevronRight', 
    'ChevronLeft', 'Plus', 'Minus', 'Edit', 'Trash', 'Save', 
    'Download', 'Upload', 'Share', 'Copy', 'Link', 'ExternalLink',
    'Home', 'User', 'Users', 'MessageSquare', 'Send', 'Image',
    'Video', 'Music', 'Book', 'BookOpen', 'GraduationCap', 'Briefcase',
    'Activity', 'BarChart', 'PieChart', 'LineChart', 'Database',
    'Server', 'Cloud', 'Wifi', 'Bluetooth', 'Battery', 'Power'
];

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
        case 'custom':
            return customContent.value;
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

// Custom section functions
const addTextBlock = () => customContent.value.text_blocks.push({ title: '', content: '', alignment: 'left' });
const removeTextBlock = (index: number) => customContent.value.text_blocks.splice(index, 1);
const addCustomButton = () => customContent.value.buttons.push({ text: '', href: '', style: 'primary', icon: '' });
const removeCustomButton = (index: number) => customContent.value.buttons.splice(index, 1);
const addList = () => customContent.value.lists.push({ title: '', items: [''], style: 'bullet' });
const removeList = (index: number) => customContent.value.lists.splice(index, 1);
const addListItem = (listIndex: number) => customContent.value.lists[listIndex].items.push('');
const removeListItem = (listIndex: number, itemIndex: number) => customContent.value.lists[listIndex].items.splice(itemIndex, 1);
const addGridItem = () => customContent.value.grid_items.push({ title: '', description: '', image_url: '', link: '', icon: '' });
const removeGridItem = (index: number) => customContent.value.grid_items.splice(index, 1);
const addCustomBadge = () => customContent.value.badges.push({ text: '', icon: 'CheckCircle' });
const removeCustomBadge = (index: number) => customContent.value.badges.splice(index, 1);
const addCustomFeature = () => customContent.value.features.push({ icon: 'Shield', title: '', description: '' });
const removeCustomFeature = (index: number) => customContent.value.features.splice(index, 1);
const addCustomIngredient = () => customContent.value.ingredients.push({ name: '', amount: '' });
const removeCustomIngredient = (index: number) => customContent.value.ingredients.splice(index, 1);
const addCustomDetail = () => customContent.value.details.push({ label: '', value: '' });
const removeCustomDetail = (index: number) => customContent.value.details.splice(index, 1);
const addCustomPricingTier = () => customContent.value.pricing_tiers.push({ qty: 0, price: 0, perCap: 0 });
const removeCustomPricingTier = (index: number) => customContent.value.pricing_tiers.splice(index, 1);
const addCustomStep = () => customContent.value.steps.push({ number: customContent.value.steps.length + 1, title: '', description: '' });
const removeCustomStep = (index: number) => {
    customContent.value.steps.splice(index, 1);
    customContent.value.steps.forEach((step, idx) => step.number = idx + 1);
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
    customContent.value = {
        html_content: '',
        image_url: '',
        image_alt: '',
        text_blocks: [],
        buttons: [],
        lists: [],
        grid_items: [],
        badges: [],
        features: [],
        ingredients: [],
        details: [],
        pricing_tiers: [],
        pricing_note: '',
        steps: [],
        step_link_text: '',
        step_link_href: '',
        custom_css: '',
        spacing: 'normal',
        max_width: 'full',
        text_alignment: 'left'
    };
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
                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-600 rounded-lg"
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
                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-600 rounded-lg"
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
                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-600 rounded-lg"
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
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2"
                                            />
                                            <select v-model="badge.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
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
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            />
                                            <input
                                                v-model="button.href"
                                                type="text"
                                                placeholder="Link URL (e.g., /products)"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
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
                                        <select v-model="item.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                            <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                        </select>
                                        <input
                                            v-model="item.title"
                                            type="text"
                                            placeholder="Feature title"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <textarea
                                            v-model="item.description"
                                            rows="2"
                                            placeholder="Feature description"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
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
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <textarea
                                            v-model="step.description"
                                            rows="2"
                                            placeholder="Step description"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
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
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Learn More Link URL</label>
                                        <input
                                            v-model="linkHref"
                                            type="text"
                                            placeholder="e.g., /how-to-order"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                    </div>
                                </div>
                            </div>

                        </template>

                        <!-- Custom Section Content -->
                        <template v-if="form.type === 'custom'">
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Custom Section Content</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Create fully customizable content with flexible layout options. Content will be automatically translated to Japanese when viewed.</p>
                                
                                <!-- Layout Settings -->
                                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Layout Settings</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Max Width</label>
                                            <select v-model="customContent.max_width" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                <option value="full">Full Width</option>
                                                <option value="7xl">7xl (1280px)</option>
                                                <option value="6xl">6xl (1152px)</option>
                                                <option value="5xl">5xl (1024px)</option>
                                                <option value="4xl">4xl (896px)</option>
                                                <option value="3xl">3xl (768px)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Text Alignment</label>
                                            <select v-model="customContent.text_alignment" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                <option value="left">Left</option>
                                                <option value="center">Center</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Spacing</label>
                                            <select v-model="customContent.spacing" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                <option value="tight">Tight</option>
                                                <option value="normal">Normal</option>
                                                <option value="loose">Loose</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Image -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Main Image (Optional)</label>
                                    <input
                                        v-model="customContent.image_url"
                                        type="text"
                                        placeholder="Image URL (e.g., https://example.com/image.jpg)"
                                        class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2"
                                    />
                                    <input
                                        v-model="customContent.image_alt"
                                        type="text"
                                        placeholder="Image Alt Text"
                                        class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                    />
                                </div>

                                <!-- HTML Content -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        HTML Content (Optional)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">- For advanced users</span>
                                    </label>
                                    <textarea
                                        v-model="customContent.html_content"
                                        rows="6"
                                        placeholder="Enter raw HTML content here..."
                                        class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg font-mono"
                                    ></textarea>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This will be rendered as-is. Use with caution.</p>
                                </div>

                                <!-- Text Blocks -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Text Blocks</label>
                                        <button type="button" @click="addTextBlock" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Text Block
                                        </button>
                                    </div>
                                    <div v-for="(block, index) in customContent.text_blocks" :key="index" class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Text Block {{ index + 1 }}</span>
                                            <button type="button" @click="removeTextBlock(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <input
                                            v-model="block.title"
                                            type="text"
                                            placeholder="Block Title (Optional)"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2"
                                        />
                                        <textarea
                                            v-model="block.content"
                                            rows="3"
                                            placeholder="Block content text..."
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2"
                                        ></textarea>
                                        <select v-model="block.alignment" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                            <option value="left">Left Aligned</option>
                                            <option value="center">Center Aligned</option>
                                            <option value="right">Right Aligned</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Buttons/CTAs -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Call-to-Action Buttons</label>
                                        <button type="button" @click="addCustomButton" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Button
                                        </button>
                                    </div>
                                    <div v-for="(button, index) in customContent.buttons" :key="index" class="mb-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex gap-3">
                                            <div class="flex-1 space-y-2">
                                                <input
                                                    v-model="button.text"
                                                    type="text"
                                                    placeholder="Button Text"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <input
                                                    v-model="button.href"
                                                    type="text"
                                                    placeholder="Link URL (e.g., /products or https://example.com)"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <div class="grid grid-cols-2 gap-2">
                                                    <select v-model="button.style" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                        <option value="primary">Primary (Black)</option>
                                                        <option value="secondary">Secondary (White)</option>
                                                        <option value="outline">Outline</option>
                                                        <option value="ghost">Ghost</option>
                                                    </select>
                                                    <select v-model="button.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                        <option value="">No Icon</option>
                                                        <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeCustomButton(index)" class="self-start text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lists -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lists</label>
                                        <button type="button" @click="addList" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add List
                                        </button>
                                    </div>
                                    <div v-for="(list, listIndex) in customContent.lists" :key="listIndex" class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">List {{ listIndex + 1 }}</span>
                                            <button type="button" @click="removeList(listIndex)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <input
                                            v-model="list.title"
                                            type="text"
                                            placeholder="List Title (Optional)"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2"
                                        />
                                        <select v-model="list.style" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg mb-2">
                                            <option value="bullet">Bullet Points</option>
                                            <option value="numbered">Numbered</option>
                                            <option value="check">Checkmarks</option>
                                        </select>
                                        <div class="space-y-2">
                                            <div v-for="(item, itemIndex) in list.items" :key="itemIndex" class="flex gap-2">
                                                <input
                                                    v-model="list.items[itemIndex]"
                                                    type="text"
                                                    :placeholder="`List item ${itemIndex + 1}`"
                                                    class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <button type="button" @click="removeListItem(listIndex, itemIndex)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <button type="button" @click="addListItem(listIndex)" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                + Add Item
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grid Items -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grid Items</label>
                                        <button type="button" @click="addGridItem" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Grid Item
                                        </button>
                                    </div>
                                    <div v-for="(item, index) in customContent.grid_items" :key="index" class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Grid Item {{ index + 1 }}</span>
                                            <button type="button" @click="removeGridItem(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            <input
                                                v-model="item.title"
                                                type="text"
                                                placeholder="Item Title"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            />
                                            <textarea
                                                v-model="item.description"
                                                rows="2"
                                                placeholder="Item Description"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            ></textarea>
                                            <input
                                                v-model="item.image_url"
                                                type="text"
                                                placeholder="Image URL (Optional)"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            />
                                            <div class="grid grid-cols-2 gap-2">
                                                <input
                                                    v-model="item.link"
                                                    type="text"
                                                    placeholder="Link URL (Optional)"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <select v-model="item.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                    <option value="">No Icon</option>
                                                    <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Badges -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Badges</label>
                                        <button type="button" @click="addCustomBadge" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Badge
                                        </button>
                                    </div>
                                    <div v-for="(badge, index) in customContent.badges" :key="index" class="mb-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex gap-3">
                                            <div class="flex-1 space-y-2">
                                                <input
                                                    v-model="badge.text"
                                                    type="text"
                                                    placeholder="Badge text"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <select v-model="badge.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                    <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                                </select>
                                            </div>
                                            <button type="button" @click="removeCustomBadge(index)" class="self-start text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Features</label>
                                        <button type="button" @click="addCustomFeature" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Feature
                                        </button>
                                    </div>
                                    <div v-for="(feature, index) in customContent.features" :key="index" class="mb-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex gap-3">
                                            <div class="flex-1 space-y-2">
                                                <select v-model="feature.icon" class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg">
                                                    <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                                                </select>
                                                <input
                                                    v-model="feature.title"
                                                    type="text"
                                                    placeholder="Feature title"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <textarea
                                                    v-model="feature.description"
                                                    rows="2"
                                                    placeholder="Feature description"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                ></textarea>
                                            </div>
                                            <button type="button" @click="removeCustomFeature(index)" class="self-start text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ingredients -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ingredients</label>
                                        <button type="button" @click="addCustomIngredient" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Ingredient
                                        </button>
                                    </div>
                                    <div v-for="(ingredient, index) in customContent.ingredients" :key="index" class="mb-2 flex gap-2">
                                        <input
                                            v-model="ingredient.name"
                                            type="text"
                                            placeholder="Ingredient name"
                                            class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <input
                                            v-model="ingredient.amount"
                                            type="text"
                                            placeholder="Amount (e.g., 14.85mg)"
                                            class="w-32 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <button type="button" @click="removeCustomIngredient(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Details</label>
                                        <button type="button" @click="addCustomDetail" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Detail
                                        </button>
                                    </div>
                                    <div v-for="(detail, index) in customContent.details" :key="index" class="mb-2 flex gap-2">
                                        <input
                                            v-model="detail.label"
                                            type="text"
                                            placeholder="Label (e.g., Country of Origin)"
                                            class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <input
                                            v-model="detail.value"
                                            type="text"
                                            placeholder="Value"
                                            class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <button type="button" @click="removeCustomDetail(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Pricing Tiers -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pricing Tiers</label>
                                        <button type="button" @click="addCustomPricingTier" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Tier
                                        </button>
                                    </div>
                                    <div v-for="(tier, index) in customContent.pricing_tiers" :key="index" class="mb-2 flex gap-2">
                                        <input
                                            v-model.number="tier.qty"
                                            type="number"
                                            placeholder="Quantity"
                                            class="w-24 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <input
                                            v-model.number="tier.price"
                                            type="number"
                                            placeholder="Total Price (JPY)"
                                            class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <input
                                            v-model.number="tier.perCap"
                                            type="number"
                                            placeholder="Per Cap (JPY)"
                                            class="w-32 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        />
                                        <button type="button" @click="removeCustomPricingTier(index)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pricing Note</label>
                                        <textarea
                                            v-model="customContent.pricing_note"
                                            rows="2"
                                            placeholder="Additional note about pricing"
                                            class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Steps (How It Works) -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Steps</label>
                                        <button type="button" @click="addCustomStep" class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <Plus class="w-4 h-4" />
                                            Add Step
                                        </button>
                                    </div>
                                    <div v-for="(step, index) in customContent.steps" :key="index" class="mb-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex gap-3">
                                            <div class="flex-1 space-y-2">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Step {{ step.number }}</div>
                                                <input
                                                    v-model="step.title"
                                                    type="text"
                                                    placeholder="Step title"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                />
                                                <textarea
                                                    v-model="step.description"
                                                    rows="2"
                                                    placeholder="Step description"
                                                    class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                                ></textarea>
                                            </div>
                                            <button type="button" @click="removeCustomStep(index)" class="self-start text-red-600 hover:text-red-800 dark:text-red-400">
                                                <X class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Learn More Link Text</label>
                                            <input
                                                v-model="customContent.step_link_text"
                                                type="text"
                                                placeholder="e.g., Learn more about the ordering process"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Learn More Link URL</label>
                                            <input
                                                v-model="customContent.step_link_href"
                                                type="text"
                                                placeholder="e.g., /how-to-order"
                                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom CSS -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Custom CSS (Optional)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">- For advanced styling</span>
                                    </label>
                                    <textarea
                                        v-model="customContent.custom_css"
                                        rows="4"
                                        placeholder=".custom-class { color: #000; }"
                                        class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg font-mono"
                                    ></textarea>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add custom CSS classes or inline styles. Use with caution.</p>
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
                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-600 rounded-lg"
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
                                class="w-full border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-600 rounded-lg"
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
