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

// Watch for prop changes
watch(() => props.cartItems, (newItems) => {
    if (newItems) {
        localCartItems.value = newItems;
    }
}, { deep: true });

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
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                // Update local state from page props if available
                const props = page.props as any;
                if (props.cart) {
                    localCartItems.value = props.cart.items || [];
                    localSubtotal.value = props.cart.subtotal || 0;
                    localTax.value = props.cart.tax || 0;
                    localShipping.value = props.cart.shipping || 0;
                    localTotal.value = props.cart.total || 0;
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
    
    router.put(`/cart/${cartItem.id}`, {
        quantity: newQuantity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            fetchCartData();
        },
    });
};

const startEditingQuantity = (item: any) => {
    editingQuantities.value[item.id] = item.quantity;
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

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
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
                        :key="item.id"
                        class="flex gap-4 p-4 border border-gray-200 rounded-lg"
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
                            <h3 class="font-semibold text-gray-900 mb-1 text-sm line-clamp-2">
                                {{ item.product?.name }}
                            </h3>
                            <p class="text-xs text-gray-600 mb-3">
                                <span v-if="item.variant && item.variant.type === 'bottle'">
                                    {{ item.variant.bottles }} {{ item.variant.bottles === 1 ? 'bottle' : 'bottles' }} 
                                    ({{ item.variant.total_capsules }} capsules)
                                </span>
                                <span v-else>
                                    {{ formatPrice(item.price) }} each
                                </span>
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="updateQuantity(item, item.quantity - 1)"
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
                                        :max="item.product?.stock_quantity || 999"
                                        class="w-12 h-7 border border-gray-300 rounded text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                        @focus="$event.target.select()"
                                    />
                                    <span
                                        v-else
                                        @click="startEditingQuantity(item)"
                                        class="w-12 h-7 flex items-center justify-center text-center text-sm font-medium text-gray-900 cursor-pointer border border-transparent hover:border-gray-300 rounded"
                                        title="Click to edit quantity"
                                    >
                                        <span v-if="item.variant && item.variant.type === 'bottle'">
                                            {{ item.variant.bottles }}
                                        </span>
                                        <span v-else>
                                            {{ item.quantity }}
                                        </span>
                                    </span>
                                    <button
                                        @click="updateQuantity(item, item.quantity + 1)"
                                        class="w-7 h-7 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50 transition-colors"
                                        aria-label="Increase quantity"
                                    >
                                        <Plus class="w-3 h-3 text-gray-900" />
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-gray-900 text-sm">
                                        {{ formatPrice(item.price * item.quantity) }}
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
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tax (8%)</span>
                        <span>{{ formatPrice(localTax) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Shipping</span>
                        <span>{{ localShipping === 0 ? 'Free' : formatPrice(localShipping) }}</span>
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

