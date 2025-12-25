<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { X, ShoppingCart, Plus, Minus, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    isOpen: boolean;
    cartItems?: any[];
    subtotal?: number;
    tax?: number;
    shipping?: number;
    total?: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const page = usePage();
const isLoading = ref(false);
const localCartItems = ref<any[]>(props.cartItems || []);
const localSubtotal = ref(props.subtotal || 0);
const localTax = ref(props.tax || 0);
const localShipping = ref(props.shipping || 0);
const localTotal = ref(props.total || 0);
const editingQuantities = ref<{ [key: number]: number }>({});
const errorMessages = ref<{ [key: number]: string }>({});

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
    
    // Don't override shipping if it's already set from props
    // Shipping should be calculated by backend and provided via props
    // Only calculate locally if props.shipping is not available
    if (props.shipping === undefined || props.shipping === null) {
        // Fallback: use the same logic as backend (will be updated when props refresh)
        localShipping.value = subtotal >= 10000 ? 0 : 500;
    } else {
        // Use shipping from props (calculated by backend with proper settings)
        localShipping.value = props.shipping;
    }
    
    localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
    
    console.log('Totals recalculated:', { subtotal: localSubtotal.value, shipping: localShipping.value, total: localTotal.value, propsShipping: props.shipping });
};

// Watch for prop changes (including shipping)
watch([() => props.cartItems, () => props.shipping, () => props.subtotal, () => props.total], ([newItems, newShipping, newSubtotal, newTotal]) => {
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
            // Update totals from props (including shipping from backend)
            localSubtotal.value = props.subtotal || 0;
            localTax.value = props.tax || 0;
            localShipping.value = props.shipping !== undefined && props.shipping !== null ? props.shipping : 0;
            localTotal.value = props.total || 0;
            console.log('Cart items updated from props:', { items: localCartItems.value, shipping: localShipping.value });
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
            // Recalculate totals after merge, but preserve shipping from props
            const currentSubtotal = localCartItems.value.reduce((sum: number, item: any) => {
                let itemPrice = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
                let itemQuantity = 1;
                if (item.variant && item.variant.type === 'bottle' && item.variant.tier) {
                    itemPrice = typeof item.variant.tier.price === 'string' 
                        ? parseFloat(item.variant.tier.price) 
                        : item.variant.tier.price;
                    itemQuantity = item.variant.bottles || 1;
                } else {
                    itemQuantity = item.quantity || 1;
                }
                return sum + (itemPrice * itemQuantity);
            }, 0);
            localSubtotal.value = currentSubtotal;
            // Preserve shipping from props if available
            if (props.shipping !== undefined && props.shipping !== null) {
                localShipping.value = props.shipping;
            } else {
                localShipping.value = currentSubtotal >= 10000 ? 0 : 500;
            }
            localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
            console.log('Cart items merged (preserving local updates):', { items: localCartItems.value, shipping: localShipping.value, propsShipping: props.shipping });
        }
    }
}, { deep: true, immediate: true });

// Watch for shipping and tax changes separately to ensure they update
watch(() => props.shipping, (newShipping) => {
    if (newShipping !== undefined && newShipping !== null) {
        localShipping.value = newShipping;
        localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
        console.log('Shipping updated from props:', newShipping);
    }
}, { immediate: true });

watch(() => props.tax, (newTax) => {
    if (newTax !== undefined && newTax !== null) {
        localTax.value = newTax;
        localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
        console.log('Tax updated from props:', newTax);
    }
}, { immediate: true });

watch(() => props.isOpen, async (isOpen) => {
    if (isOpen) {
        // Fetch latest cart data when sidebar opens
        await fetchCartData();
    }
});

const fetchCartData = async () => {
    isLoading.value = true;
    try {
        router.reload({
            only: ['cart'],
            preserveState: false, // Don't preserve state to get fresh data
            preserveScroll: true,
            onSuccess: (page) => {
                // Update local state from page props if available
                const props = page.props as any;
                if (props.cart && props.cart.items) {
                    // Deep clone and ensure variant is properly parsed
                    const items = props.cart.items.map((item: any) => ({
                        ...item,
                        variant: item.variant ? (typeof item.variant === 'string' ? JSON.parse(item.variant) : item.variant) : null,
                    }));
                    localCartItems.value = items;
                    localSubtotal.value = props.cart.subtotal || 0;
                    localTax.value = props.cart.tax || 0;
                    localShipping.value = props.cart.shipping || 0;
                    localTotal.value = props.cart.total || 0;
                    console.log('Cart data refreshed:', localCartItems.value);
                }
            },
        });
    } catch (error) {
        console.error('Error fetching cart:', error);
    } finally {
        isLoading.value = false;
    }
};

const updateQuantity = (cartItem: any, newQuantity: number) => {
    if (newQuantity < 1) {
        removeItem(cartItem.id);
        return;
    }
    
    // Frontend stock validation
    const product = cartItem.product;
    if (product && product.stock_quantity !== null) {
        if (cartItem.variant && cartItem.variant.type === 'bottle') {
            const capsulesPerBottle = cartItem.variant.tier?.capsules || product.capsules_per_bottle || 50;
            const requestedCapsules = newQuantity * capsulesPerBottle;
            if (requestedCapsules > product.stock_quantity) {
                const availableBottles = Math.floor(product.stock_quantity / capsulesPerBottle);
                errorMessages.value[cartItem.id] = `Only ${product.stock_quantity} capsules (${availableBottles} ${availableBottles === 1 ? 'bottle' : 'bottles'}) available in stock.`;
                setTimeout(() => {
                    delete errorMessages.value[cartItem.id];
                }, 5000);
                return;
            }
        } else {
            if (newQuantity > product.stock_quantity) {
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
    
    // For bottle-based products, newQuantity is the number of bottles
    // For regular products, newQuantity is the item count
    const quantityToSend = cartItem.variant && cartItem.variant.type === 'bottle' 
        ? newQuantity  // This is bottles for bottle-based products
        : newQuantity; // This is item count for regular products
    
    router.put(`/cart/${cartItem.id}`, {
        quantity: quantityToSend,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            fetchCartData();
        },
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
            fetchCartData();
        },
    });
};

const startEditingQuantity = (item: any) => {
    // For bottle-based products, edit bottles; for regular products, edit quantity
    editingQuantities.value[item.id] = item.variant && item.variant.type === 'bottle' 
        ? item.variant.bottles 
        : item.quantity;
};

const handleQuantityInput = (item: any, event: Event) => {
    const input = event.target as HTMLInputElement;
    const value = parseInt(input.value) || 1;
    editingQuantities.value[item.id] = Math.max(1, value);
};

const handleQuantityBlur = (item: any) => {
    const newQuantity = editingQuantities.value[item.id];
    if (newQuantity && newQuantity !== item.quantity) {
        updateQuantity(item, newQuantity);
    }
    delete editingQuantities.value[item.id];
};

const handleQuantityKeydown = (item: any, event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        const input = event.target as HTMLInputElement;
        input.blur();
    }
};

const removeItem = (cartItemId: number) => {
    router.delete(`/cart/${cartItemId}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            fetchCartData();
        },
    });
};

const updateTier = (item: any, newTier: any, event?: Event) => {
    // Prevent any event bubbling that might close the sidebar
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    console.log('updateTier called:', { 
        itemId: item.id, 
        newTier, 
        hasVariant: !!item.variant, 
        variantType: item.variant?.type,
        isBottleBased: item.product?.is_bottle_based,
        productId: item.product?.id
    });
    
    if (!newTier) {
        console.error('No tier provided');
        return;
    }
    
    if (!item.product?.is_bottle_based) {
        console.error('Product is not bottle-based');
        return;
    }
    
    // Get current bottles - if variant doesn't exist yet, default to 1
    // Also handle case where variant exists but doesn't have bottles property
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
        
        // Recalculate subtotal immediately, but preserve shipping from props
        const currentSubtotal = localCartItems.value.reduce((sum: number, item: any) => {
            let itemPrice = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
            let itemQuantity = 1;
            if (item.variant && item.variant.type === 'bottle' && item.variant.tier) {
                itemPrice = typeof item.variant.tier.price === 'string' 
                    ? parseFloat(item.variant.tier.price) 
                    : item.variant.tier.price;
                itemQuantity = item.variant.bottles || 1;
            } else {
                itemQuantity = item.quantity || 1;
            }
            return sum + (itemPrice * itemQuantity);
        }, 0);
        localSubtotal.value = currentSubtotal;
        // Preserve shipping from props if available
        if (props.shipping !== undefined && props.shipping !== null) {
            localShipping.value = props.shipping;
        } else {
            localShipping.value = currentSubtotal >= 10000 ? 0 : 500;
        }
        localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
        
        console.log('Updated item in local state immediately:', { item: updatedItem, shipping: localShipping.value, propsShipping: props.shipping });
    }
    
    const requestData = {
        quantity: currentBottles, // Send bottles, backend will convert to capsules
        variant: updatedVariant,
        price: tierPrice, // Explicitly send price to ensure it's saved
    };
    
    console.log('Updating cart item on server:', { 
        itemId: item.id, 
        currentBottles, 
        newTotalCapsules, 
        updatedVariant,
        requestData,
        tierPrice,
        tierPriceType: typeof tierPrice,
    });
    
    // Update on server in background (preserve sidebar state)
    router.put(`/cart/${item.id}`, requestData, {
        preserveScroll: true,
        preserveState: true, // Preserve state to keep sidebar open
        // Don't use 'only' - let Inertia update all props naturally
        onSuccess: async (page) => {
            console.log('Tier updated successfully on server');
            
            // Update subtotal immediately from local state, but preserve shipping from props
            const currentSubtotal = localCartItems.value.reduce((sum: number, item: any) => {
                let itemPrice = typeof item.price === 'string' ? parseFloat(item.price) : item.price;
                let itemQuantity = 1;
                if (item.variant && item.variant.type === 'bottle' && item.variant.tier) {
                    itemPrice = typeof item.variant.tier.price === 'string' 
                        ? parseFloat(item.variant.tier.price) 
                        : item.variant.tier.price;
                    itemQuantity = item.variant.bottles || 1;
                } else {
                    itemQuantity = item.quantity || 1;
                }
                return sum + (itemPrice * itemQuantity);
            }, 0);
            localSubtotal.value = currentSubtotal;
            // Preserve shipping from props if available (will be updated when props refresh)
            if (props.shipping !== undefined && props.shipping !== null) {
                localShipping.value = props.shipping;
            } else {
                localShipping.value = currentSubtotal >= 10000 ? 0 : 500;
            }
            localTotal.value = localSubtotal.value + localTax.value + localShipping.value;
            
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
    if (item.variant && item.variant.tier && item.variant.tier.capsules === tier.capsules) {
        return true;
    }
    return false;
};

const closeSidebar = () => {
    emit('close');
};

// Close on escape key
onMounted(() => {
    const handleEscape = (e: KeyboardEvent) => {
        if (e.key === 'Escape' && props.isOpen) {
            closeSidebar();
        }
    };
    window.addEventListener('keydown', handleEscape);
    
    return () => {
        window.removeEventListener('keydown', handleEscape);
    };
});
</script>

<template>
    <!-- Backdrop -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            @click="closeSidebar"
            class="fixed inset-0 bg-black/50 z-50"
        ></div>
    </Transition>

    <!-- Sidebar -->
    <Transition
        enter-active-class="transition-transform duration-300 ease-out"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-300 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div
            v-if="isOpen"
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl z-50 flex flex-col"
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <ShoppingCart class="w-5 h-5 text-gray-900" />
                    <h2 class="text-lg font-semibold text-gray-900 tracking-tight">
                        Your Cart
                    </h2>
                    <span v-if="localCartItems.length > 0" class="text-sm text-gray-600">
                        ({{ localCartItems.length }})
                    </span>
                </div>
                <button
                    @click="closeSidebar"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    aria-label="Close cart"
                >
                    <X class="w-5 h-5 text-gray-600" />
                </button>
            </div>

            <!-- Cart Content -->
            <div class="flex-1 overflow-y-auto">
                <div v-if="isLoading" class="flex items-center justify-center h-64">
                    <div class="text-gray-600">Loading...</div>
                </div>
                
                <div v-else-if="localCartItems.length === 0" class="flex flex-col items-center justify-center h-full p-6">
                    <ShoppingCart class="w-16 h-16 text-gray-300 mb-4" />
                    <p class="text-gray-600 text-lg mb-2">Your cart is empty</p>
                    <p class="text-gray-500 text-sm mb-6 text-center">
                        Continue shopping to add items to your cart
                    </p>
                    <Link
                        href="/products"
                        @click="closeSidebar"
                        class="bg-gray-900 text-white px-6 py-3 text-sm font-medium hover:bg-gray-800 transition-colors uppercase tracking-wide rounded-lg"
                    >
                        Continue Shopping
                    </Link>
                </div>

                <div v-else class="p-6 space-y-4">
                    <div
                        v-for="item in localCartItems"
                        :key="`cart-item-${item.id}-${item.variant?.tier?.capsules || 'no-tier'}`"
                        class="flex gap-4 p-4 border border-gray-200 rounded-lg relative z-0"
                    >
                        <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img
                                v-if="item.product?.image"
                                :src="item.product.image"
                                :alt="item.product.name"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center text-xs text-gray-400"
                            >
                                No Image
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mb-2 text-sm line-clamp-2">
                                {{ item.product?.name }}
                            </h3>
                            
                            <!-- Bottle-based pricing tier selection -->
                            <div v-if="item.product?.is_bottle_based && item.product?.bottle_pricing_tiers && item.product.bottle_pricing_tiers.length > 0" class="mb-3" @click.stop>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Select Pricing Tier</label>
                                <div class="space-y-1.5">
                                    <button
                                        v-for="(tier, index) in item.product.bottle_pricing_tiers"
                                        :key="`tier-${item.id}-${index}`"
                                        type="button"
                                        @click.stop.prevent="updateTier(item, tier, $event)"
                                        :class="[
                                            'w-full text-left p-2.5 border-2 rounded-lg transition-all text-xs cursor-pointer select-none',
                                            isTierSelected(item, tier)
                                                ? 'border-gray-900 bg-gray-50'
                                                : 'border-gray-200 hover:border-gray-400'
                                        ]"
                                        style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ tier.capsules }} caps</div>
                                                <div class="text-xs text-gray-500">¥{{ Math.round(getPricePerCapsule(tier)).toLocaleString() }}/cap</div>
                                            </div>
                                            <div class="text-sm font-bold text-gray-900">¥{{ tier.price.toLocaleString() }}</div>
                                        </div>
                                    </button>
                                </div>
                                <p v-if="item.variant && item.variant.bottles" class="text-xs text-gray-600 mt-2">
                                    {{ item.variant.bottles }} {{ item.variant.bottles === 1 ? 'bottle' : 'bottles' }} 
                                    ({{ item.variant.total_capsules }} capsules total)
                                </p>
                            </div>
                            
                            <!-- Regular price display for non-bottle products -->
                            <p v-else class="text-xs text-gray-600 mb-3">
                                {{ formatPrice(item.price) }} each
                            </p>
                            
                            <!-- Error message for this item -->
                            <div v-if="errorMessages[item.id]" class="mb-3 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-800">
                                {{ errorMessages[item.id] }}
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="updateQuantity(item, (item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity) - 1)"
                                        class="w-7 h-7 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50 transition-colors"
                                        aria-label="Decrease quantity"
                                    >
                                        <Minus class="w-3 h-3 text-gray-900" />
                                    </button>
                                    <input
                                        v-if="editingQuantities.hasOwnProperty(item.id)"
                                        v-model.number="editingQuantities[item.id]"
                                        @input="handleQuantityInput(item, $event)"
                                        @blur="handleQuantityBlur(item)"
                                        @keydown="handleQuantityKeydown(item, $event)"
                                        type="number"
                                        min="1"
                                        :max="item.variant && item.variant.type === 'bottle' && item.product?.stock_quantity 
                                            ? Math.floor(item.product.stock_quantity / (item.variant.tier?.capsules || item.product.capsules_per_bottle || 50))
                                            : (item.product?.stock_quantity || 999)"
                                        class="w-12 h-7 border border-gray-300 rounded text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        @focus="$event.target.select()"
                                    />
                                    <span
                                        v-else
                                        @click="startEditingQuantity(item)"
                                        class="w-12 h-7 flex items-center justify-center text-center text-sm font-medium text-gray-900 cursor-pointer border border-transparent hover:border-gray-300 rounded"
                                        title="Click to edit quantity"
                                    >
                                        {{ item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity }}
                                    </span>
                                    <button
                                        @click="updateQuantity(item, (item.variant && item.variant.type === 'bottle' ? item.variant.bottles : item.quantity) + 1)"
                                        class="w-7 h-7 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50 transition-colors"
                                        aria-label="Increase quantity"
                                    >
                                        <Plus class="w-3 h-3 text-gray-900" />
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-gray-900 text-sm">
                                        {{ formatPrice(item.variant && item.variant.type === 'bottle' && item.variant.tier 
                                            ? item.variant.tier.price * item.variant.bottles 
                                            : item.price * item.quantity) }}
                                    </span>
                                    <button
                                        @click="removeItem(item.id)"
                                        class="p-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors"
                                        aria-label="Remove item"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with Summary and Checkout -->
            <div v-if="localCartItems.length > 0" class="border-t border-gray-200 p-6 bg-gray-50">
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ formatPrice(localSubtotal) }}</span>
                    </div>
                    <div v-if="localTax > 0" class="flex justify-between text-sm text-gray-600">
                        <span>Tax (included)</span>
                        <span>{{ formatPrice(localTax) }}</span>
                    </div>
                    <div v-if="localShipping > 0" class="flex justify-between text-sm text-gray-600">
                        <span>Shipping</span>
                        <span>{{ formatPrice(localShipping) }}</span>
                    </div>
                    <div v-else-if="localSubtotal > 0" class="flex justify-between text-xs text-gray-500">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-base font-bold text-gray-900">
                        <span>Total</span>
                        <span>{{ formatPrice(localTotal) }}</span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <Link
                        href="/checkout"
                        @click="closeSidebar"
                        class="block w-full bg-gray-900 text-white py-3 text-sm font-semibold text-center hover:bg-gray-800 transition-colors uppercase tracking-wide rounded-lg"
                    >
                        Check out
                    </Link>
                    <Link
                        href="/cart"
                        @click="closeSidebar"
                        class="block w-full bg-white border border-gray-300 text-gray-900 py-3 text-sm font-medium text-center hover:bg-gray-50 transition-colors rounded-lg"
                    >
                        View Cart
                    </Link>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

