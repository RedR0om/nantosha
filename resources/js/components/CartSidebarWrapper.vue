<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import CartSidebar from './CartSidebar.vue';

const page = usePage();
const isOpen = ref(false);

const cartData = computed(() => {
    const cart = (page.props as any).cart;
    return {
        items: cart?.items || [],
        subtotal: cart?.subtotal || 0,
        tax: cart?.tax || 0,
        shipping: cart?.shipping || 0,
        total: cart?.total || 0,
    };
});

const openCartSidebar = () => {
    isOpen.value = true;
};

const closeCartSidebar = () => {
    isOpen.value = false;
};

const handleOpen = () => openCartSidebar();
const handleClose = () => closeCartSidebar();

onMounted(() => {
    window.addEventListener('openCartSidebar', handleOpen);
    window.addEventListener('closeCartSidebar', handleClose);
});

onUnmounted(() => {
    window.removeEventListener('openCartSidebar', handleOpen);
    window.removeEventListener('closeCartSidebar', handleClose);
});
</script>

<template>
    <CartSidebar
        :is-open="isOpen"
        :cart-items="cartData.items"
        :subtotal="cartData.subtotal"
        :tax="cartData.tax"
        :shipping="cartData.shipping"
        :total="cartData.total"
        @close="closeCartSidebar"
    />
</template>

