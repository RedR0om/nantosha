<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import PublicNav from '@/components/PublicNav.vue';

const props = defineProps<{
    cartItems: any[];
    subtotal: number;
    tax: number;
    shipping: number;
    total: number;
}>();

const errorMessages = ref<{ [key: number]: string }>({});

// Local state for cart items (like sidebar)
const localCartItems = ref<any[]>([]);
const localSubtotal = ref(props.subtotal || 0);
const localTax = ref(props.tax || 0);
const localShipping = ref(props.shipping || 0);
const localTotal = ref(props.total || 0);

// Track if we're currently updating an item to prevent props from overwriting local changes
const updatingItems = ref<Set<number>>(new Set());

// Recalculate totals based on current cart items
const recalculateTotals = () => {
    let subtotal = 0;
    localCartItems.value.forEach((item: any) => {
        let itemPrice = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
        let itemQuantity = 1;
        
        // For bottle-based products, use tier price and number of bottles
        if (item.variant && item.variant.type === 'bottle' && item.variant.tier) {
            itemPrice = typeof item.variant.tier.price === 'string' 
                ? parseFloat(item.variant.tier.price) 
                : item.variant.tier.price;
            itemQuantity = item.variant.bottles || 1;
        } else {
            // For regular products, use item price and quantity
            itemQuantity = item.quantity || 1;
        }
        
        subtotal += itemPrice * itemQuantity;
    });
    
    localSubtotal.value = subtotal;
    // Use tax from props (calculated by backend)
    localTax.value = props.tax !== undefined && props.tax !== null ? props.tax : 0;
    // Shipping will be calculated by backend based on settings
    // For now, use the same logic: free shipping over 10000, otherwise 500
    // This will be updated from props when backend responds
    localShipping.value = props.shipping !== undefined && props.shipping !== null ? props.shipping : (subtotal >= 10000 ? 0 : 500);
    localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
    
    console.log('Totals recalculated:', { subtotal: localSubtotal.value, shipping: localShipping.value, total: localTotal.value });
};

// Watch for prop changes
watch(() => props.cartItems, (newItems) => {
    if (newItems) {
        // Deep clone and ensure variant is properly parsed
        const items = newItems.map((item: any) => ({
            ...item,
            variant: item.variant ? (typeof item.variant === 'string' ? JSON.parse(item.variant) : item.variant) : null,
        }));
        
        // Only update items that aren't currently being updated
        // This prevents props from overwriting local state changes
        if (updatingItems.value.size === 0) {
            localCartItems.value = items;
            // Update totals from props
            localSubtotal.value = props.subtotal || 0;
            localTax.value = props.tax || 0;
            localShipping.value = props.shipping || 0;
            localTotal.value = props.total || 0;
            console.log('Cart items updated from props:', localCartItems.value);
        } else {
            // Merge updates: keep local changes for items being updated, use props for others
            items.forEach((newItem: any) => {
                if (!updatingItems.value.has(newItem.id)) {
                    const index = localCartItems.value.findIndex((i: any) => i.id === newItem.id);
                    if (index !== -1) {
                        localCartItems.value[index] = newItem;
                    } else {
                        localCartItems.value.push(newItem);
                    }
                } else {
                    // Item is being updated - check if server has processed the update
                    const localIndex = localCartItems.value.findIndex((i: any) => i.id === newItem.id);
                    if (localIndex !== -1) {
                        const localItem = localCartItems.value[localIndex];
                        const serverPrice = typeof newItem.price === 'string' ? parseFloat(newItem.price) : newItem.price;
                        const localPrice = typeof localItem.price === 'string' ? parseFloat(localItem.price) : localItem.price;
                        
                        // If server price matches our expected price, server has processed the update
                        if (Math.abs(serverPrice - localPrice) < 0.01) {
                            // Prices match, server has our update - use server data and remove from updating set
                            localCartItems.value[localIndex] = newItem;
                            updatingItems.value.delete(newItem.id);
                            console.log('Updated item from server (price confirmed, removed from updating):', newItem);
                        } else {
                            // Prices don't match - keep local state, server hasn't updated yet
                            console.log('Keeping local state, server price differs:', { local: localPrice, server: serverPrice, itemId: newItem.id });
                        }
                    }
                }
            });
            // Recalculate totals after merge
            recalculateTotals();
            console.log('Cart items merged (preserving local updates):', localCartItems.value);
        }
    }
}, { deep: true, immediate: true });

// Parse variants from string to object for cart items (use localCartItems)
const parsedCartItems = computed(() => {
    return localCartItems.value;
});

const updateQuantity = (cartItem: any, quantity: number) => {
    if (quantity < 1) return;
    
    // Frontend stock validation
    const product = cartItem.product;
    if (product && product.stock_quantity !== null) {
        if (cartItem.variant && cartItem.variant.type === 'bottle') {
            const capsulesPerBottle = cartItem.variant.tier?.capsules || product.capsules_per_bottle || 50;
            const requestedCapsules = quantity * capsulesPerBottle;
            if (requestedCapsules > product.stock_quantity) {
                const availableBottles = Math.floor(product.stock_quantity / capsulesPerBottle);
                errorMessages.value[cartItem.id] = `Only ${product.stock_quantity} capsules (${availableBottles} ${availableBottles === 1 ? 'bottle' : 'bottles'}) available in stock.`;
                setTimeout(() => {
                    delete errorMessages.value[cartItem.id];
                }, 5000);
                return;
            }
        } else {
            if (quantity > product.stock_quantity) {
                errorMessages.value[cartItem.id] = `Only ${product.stock_quantity} items available in stock.`;
                setTimeout(() => {
                    delete errorMessages.value[cartItem.id];
                }, 5000);
                return;
            }
        }
    }
    
    // Clear error for this item
    delete errorMessages.value[cartItem.id];
    
    // For bottle-based products, quantity is the number of bottles
    // For regular products, quantity is the item count
    const quantityToSend = cartItem.variant && cartItem.variant.type === 'bottle' 
        ? quantity  // This is bottles for bottle-based products
        : quantity; // This is item count for regular products
    
    router.put(`/cart/${cartItem.id}`, {
        quantity: quantityToSend,
    }, {
        preserveScroll: true,
        onError: (errors) => {
            if (errors && typeof errors === 'object') {
                const errorKeys = Object.keys(errors);
                if (errorKeys.length > 0) {
                    errorMessages.value[cartItem.id] = errors[errorKeys[0]] || 'Failed to update quantity.';
                    setTimeout(() => {
                        delete errorMessages.value[cartItem.id];
                    }, 5000);
                }
            }
        },
    });
};

const removeItem = (cartItemId: number) => {
    router.delete(`/cart/${cartItemId}`, {
        preserveScroll: true,
    });
};

const updateTier = (item: any, newTier: any) => {
    console.log('updateTier called:', { 
        itemId: item.id, 
        newTier, 
        hasVariant: !!item.variant, 
        variantType: item.variant?.type,
        isBottleBased: item.product?.is_bottle_based,
        productId: item.product?.id
    });
    
    if (!newTier || !item.product?.is_bottle_based) {
        console.log('Cannot update tier:', { newTier, isBottleBased: item.product?.is_bottle_based });
        return;
    }
    
    // Get current bottles - handle cases where variant doesn't exist yet
    let currentBottles = 1;
    if (item.variant && item.variant.bottles) {
        currentBottles = item.variant.bottles;
    } else if (item.variant && item.variant.tier && item.quantity) {
        // Calculate bottles from quantity (which is capsules) and tier capsules
        currentBottles = Math.floor(item.quantity / (item.variant.tier.capsules || newTier.capsules));
        if (currentBottles < 1) currentBottles = 1;
    }
    
    // Calculate new total capsules based on new tier's capsules per bottle
    const newTotalCapsules = newTier.capsules * currentBottles;
    
    // Frontend stock validation
    const product = item.product;
    if (product && product.stock_quantity !== null && newTotalCapsules > product.stock_quantity) {
        const availableBottles = Math.floor(product.stock_quantity / newTier.capsules);
        errorMessages.value[item.id] = `Only ${product.stock_quantity} capsules (${availableBottles} ${availableBottles === 1 ? 'bottle' : 'bottles'}) available in stock.`;
        setTimeout(() => {
            delete errorMessages.value[item.id];
        }, 5000);
        return;
    }
    
    // Clear error for this item
    delete errorMessages.value[item.id];
    
    // Ensure price and capsules are numbers
    const tierPrice = typeof newTier.price === 'string' ? parseFloat(newTier.price) : newTier.price;
    const tierCapsules = typeof newTier.capsules === 'string' ? parseInt(newTier.capsules) : newTier.capsules;
    const tierPricePerCapsule = newTier.price_per_capsule 
        ? (typeof newTier.price_per_capsule === 'string' ? parseFloat(newTier.price_per_capsule) : newTier.price_per_capsule)
        : (tierPrice / tierCapsules);
    
    // Prepare updated variant with new tier
    const updatedVariant = {
        type: 'bottle',
        tier: {
            ...newTier,
            price: tierPrice,
            capsules: tierCapsules,
            price_per_capsule: tierPricePerCapsule,
        },
        bottles: currentBottles,
        total_capsules: newTotalCapsules,
    };
    
    // Mark this item as being updated to prevent props from overwriting
    updatingItems.value.add(item.id);
    
    // Immediately update local state for instant UI feedback (like product page)
    const itemIndex = localCartItems.value.findIndex((i: any) => i.id === item.id);
    const originalItem = itemIndex !== -1 ? { ...localCartItems.value[itemIndex] } : item;
    
    if (itemIndex !== -1) {
        const updatedItem = {
            ...localCartItems.value[itemIndex],
            variant: { ...updatedVariant },
            price: tierPrice, // Use numeric price
            quantity: newTotalCapsules, // Update quantity to total capsules
        };
        localCartItems.value.splice(itemIndex, 1, updatedItem);
        
        // Recalculate subtotal and total immediately
        recalculateTotals();
        
        console.log('Updated item in local state immediately:', updatedItem);
    }
    
    console.log('Updating cart item on server:', { itemId: item.id, currentBottles, newTotalCapsules, updatedVariant });
    
    router.put(`/cart/${item.id}`, {
        quantity: currentBottles, // Send bottles, backend will convert to capsules
        variant: updatedVariant,
        price: tierPrice, // Explicitly send price to ensure it's saved
    }, {
        preserveScroll: true,
        preserveState: true, // Preserve state to avoid page refresh
        // Don't use 'only' - let Inertia update all props naturally
        onSuccess: async (page) => {
            console.log('Tier updated successfully on server');
            
            // Update totals immediately from local state (server will confirm later via props)
            recalculateTotals();
            
            // Don't remove from updating set immediately
            // The props watcher will remove it when it sees the server price matches our local price
            // This prevents props from overwriting our local state until server confirms the update
        },
        onError: (errors) => {
            console.error('Error updating tier:', errors);
            // Remove from updating set
            updatingItems.value.delete(item.id);
            // Revert local state on error
            if (itemIndex !== -1) {
                localCartItems.value.splice(itemIndex, 1, originalItem);
            }
            if (errors && typeof errors === 'object') {
                const errorKeys = Object.keys(errors);
                if (errorKeys.length > 0) {
                    errorMessages.value[item.id] = errors[errorKeys[0]] || 'Failed to update tier.';
                    setTimeout(() => {
                        delete errorMessages.value[item.id];
                    }, 5000);
                }
            } else {
                errorMessages.value[item.id] = 'Failed to update tier. Please try again.';
                setTimeout(() => {
                    delete errorMessages.value[item.id];
                }, 5000);
            }
        },
    });
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

// Helper to get price per capsule from tier data
const getPricePerCapsule = (tier: any) => {
    if (!tier) return 0;
    if (tier.price_per_capsule) {
        return tier.price_per_capsule;
    }
    if (tier.price && tier.capsules && tier.capsules > 0) {
        return tier.price / tier.capsules;
    }
    return 0;
};

// Helper to check if a tier is selected for an item
const isTierSelected = (item: any, tier: any) => {
    if (!item || !tier) return false;
    // Item should already have parsed variant from parsedCartItems computed
    const variant = item.variant;
    if (variant && variant.tier && variant.tier.capsules === tier.capsules) {
        return true;
    }
    return false;
};
</script>

<template>
    <Head title="Shopping Cart - Nantosha" />
    
    <div class="min-h-screen bg-gray-50">
        <PublicNav />
        <div class="py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-8 tracking-tight">Shopping Cart</h1>
            
            <div
                v-if="parsedCartItems.length === 0"
                class="bg-white border border-gray-200 rounded-lg p-12 text-center"
            >
                <p class="text-gray-600 text-lg mb-4">Your cart is empty</p>
                <Link
                    href="/products"
                    class="inline-block bg-gray-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition uppercase tracking-wide"
                >
                    Continue Shopping
                </Link>
            </div>
            
            <div
                v-else
                class="grid grid-cols-1 lg:grid-cols-3 gap-8"
            >
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        v-for="item in parsedCartItems"
                        :key="item.id"
                        class="bg-white border border-gray-200 rounded-lg p-6"
                    >
                        <div class="flex gap-4">
                            <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    :alt="item.product.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">
                                    {{ item.product?.name }}
                                </h3>
                                
                                <!-- Bottle-based pricing tier selection -->
                                <div v-if="item.product?.is_bottle_based && item.product?.bottle_pricing_tiers && item.product.bottle_pricing_tiers.length > 0" class="mb-4 relative z-10">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Pricing Tier</label>
                                    <div class="space-y-2 relative">
                                        <button
                                            v-for="(tier, index) in item.product.bottle_pricing_tiers"
                                            :key="index"
                                            type="button"
                                            @click.stop="updateTier(item, tier)"
                                            :class="[
                                                'w-full text-left p-3 border-2 rounded-lg transition-all cursor-pointer relative z-10 pointer-events-auto',
                                                isTierSelected(item, tier)
                                                    ? 'border-gray-900 bg-gray-50'
                                                    : 'border-gray-200 hover:border-gray-400'
                                            ]"
                                        >
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ tier.capsules }} caps</div>
                                                    <div class="text-xs text-gray-500">¥{{ Math.round(getPricePerCapsule(tier)).toLocaleString() }}/cap</div>
                                                </div>
                                                <div class="text-base font-bold text-gray-900">¥{{ tier.price.toLocaleString() }}</div>
                                            </div>
                                        </button>
                                    </div>
                                    <p v-if="item.variant && item.variant.bottles" class="text-xs text-gray-600 mt-2">
                                        {{ item.variant.bottles }} {{ item.variant.bottles === 1 ? 'bottle' : 'bottles' }} 
                                        ({{ item.variant.total_capsules }} capsules total)
                                    </p>
                                </div>
                                
                                <p v-else class="text-gray-600 mb-4">
                                    {{ formatPrice(item.price) }} each
                                </p>
                                
                                <!-- Error message for this item -->
                                <div v-if="errorMessages[item.id]" class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-800">
                                    {{ errorMessages[item.id] }}
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <button
                                            @click="updateQuantity(item, (item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity) - 1)"
                                            class="w-8 h-8 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                                        >
                                            -
                                        </button>
                                        <span class="w-12 text-center font-medium text-gray-900">
                                            {{ item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity }}
                                        </span>
                                        <button
                                            @click="updateQuantity(item, (item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity) + 1)"
                                            class="w-8 h-8 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                                        >
                                            +
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center gap-4">
                                        <span class="font-semibold text-gray-900">
                                            {{ formatPrice(item.variant && item.variant.type === 'bottle' && item.variant.tier 
                                                ? item.variant.tier.price * item.variant.bottles 
                                                : item.price * item.quantity) }}
                                        </span>
                                        <button
                                            @click="removeItem(item.id)"
                                            class="text-red-600 hover:text-red-700 text-sm font-medium"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 tracking-tight">
                            Order Summary
                        </h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(localSubtotal) }}</span>
                            </div>
                            <div v-if="localTax > 0" class="flex justify-between text-sm text-gray-600">
                                <span>Tax (included)</span>
                                <span>{{ formatPrice(localTax) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span>{{ localShipping === 0 ? 'Free' : formatPrice(localShipping) }}</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>{{ formatPrice(localTotal) }}</span>
                            </div>
                        </div>
                        
                        <Link
                            href="/checkout"
                            class="block w-full bg-gray-900 text-white py-3 rounded-lg font-semibold text-center hover:bg-gray-800 transition uppercase tracking-wide"
                        >
                            Proceed to Checkout
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

