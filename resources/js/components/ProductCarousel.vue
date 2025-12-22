<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import ProductCard from './ProductCard.vue';

interface Props {
    title: string;
    items: any[];
    itemsPerView?: number;
    gap?: number;
}

const props = withDefaults(defineProps<Props>(), {
    itemsPerView: 4,
    gap: 24,
});

const carouselRef = ref<HTMLElement | null>(null);
const scrollPosition = ref(0);
const maxScroll = ref(0);
const canScrollLeft = ref(false);
const canScrollRight = ref(true);

// Touch/swipe handling
const touchStartX = ref(0);
const touchStartY = ref(0);
const touchStartScroll = ref(0);
const isSwipeActive = ref(false);
const isMouseDown = ref(false);

const updateScrollState = () => {
    if (!carouselRef.value) return;
    
    scrollPosition.value = carouselRef.value.scrollLeft;
    maxScroll.value = carouselRef.value.scrollWidth - carouselRef.value.clientWidth;
    canScrollLeft.value = scrollPosition.value > 10;
    canScrollRight.value = scrollPosition.value < maxScroll.value - 10;
};

const scroll = (direction: 'left' | 'right') => {
    if (!carouselRef.value) return;
    
    const container = carouselRef.value;
    const firstItem = container.querySelector('.carousel-item') as HTMLElement;
    if (!firstItem) return;
    
    const itemWidth = firstItem.offsetWidth;
    const scrollAmount = itemWidth + props.gap;
    
    if (direction === 'left') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

// Touch event handlers
let touchStartTime = 0;
let isHorizontalSwipe = false;

const handleTouchStart = (e: TouchEvent) => {
    if (!carouselRef.value) return;
    
    touchStartX.value = e.touches[0].clientX;
    touchStartY.value = e.touches[0].clientY;
    touchStartScroll.value = carouselRef.value.scrollLeft;
    isSwipeActive.value = true;
    isHorizontalSwipe = false;
    touchStartTime = Date.now();
};

const handleTouchMove = (e: TouchEvent) => {
    if (!isSwipeActive.value || !carouselRef.value) return;
    
    const currentX = e.touches[0].clientX;
    const currentY = e.touches[0].clientY;
    const diffX = Math.abs(touchStartX.value - currentX);
    const diffY = Math.abs(touchStartY.value - currentY);
    
    // Determine if this is a horizontal swipe
    if (diffX > diffY && diffX > 10) {
        isHorizontalSwipe = true;
    }
};

const handleTouchEnd = (e: TouchEvent) => {
    if (!isSwipeActive.value || !carouselRef.value) {
        isSwipeActive.value = false;
        return;
    }
    
    const touchEndX = e.changedTouches[0].clientX;
    const touchEndY = e.changedTouches[0].clientY;
    
    const diffX = touchStartX.value - touchEndX;
    const diffY = Math.abs(touchStartY.value - touchEndY);
    const minSwipeDistance = 50;
    const touchDuration = Date.now() - touchStartTime;
    
    // Only process if it was a horizontal swipe
    if (!isHorizontalSwipe || Math.abs(diffX) <= diffY) {
        isSwipeActive.value = false;
        return;
    }
    
    // Wait a bit for momentum scrolling to settle
    const waitTime = touchDuration < 200 ? 100 : 200;
    
    setTimeout(() => {
        if (!carouselRef.value) {
            isSwipeActive.value = false;
            return;
        }
        
        const container = carouselRef.value;
        const firstItem = container.querySelector('.carousel-item') as HTMLElement;
        if (!firstItem) {
            isSwipeActive.value = false;
            return;
        }
        
        const itemWidth = firstItem.offsetWidth;
        const scrollAmount = itemWidth + props.gap;
        const currentScroll = container.scrollLeft;
        const currentIndex = Math.round(currentScroll / scrollAmount);
        
        if (Math.abs(diffX) > minSwipeDistance) {
            if (diffX > 0) {
                // Swiped left - go to next item
                const nextIndex = Math.min(
                    currentIndex + 1,
                    Math.floor((container.scrollWidth - container.clientWidth) / scrollAmount)
                );
                container.scrollTo({
                    left: nextIndex * scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                // Swiped right - go to previous item
                const prevIndex = Math.max(0, currentIndex - 1);
                container.scrollTo({
                    left: prevIndex * scrollAmount,
                    behavior: 'smooth'
                });
            }
        } else {
            // Small swipe - snap to nearest
            container.scrollTo({
                left: currentIndex * scrollAmount,
                behavior: 'smooth'
            });
        }
        
        isSwipeActive.value = false;
        isHorizontalSwipe = false;
    }, waitTime);
};

// Mouse drag support for desktop
const handleMouseDown = (e: MouseEvent) => {
    if (!carouselRef.value) return;
    
    touchStartX.value = e.clientX;
    touchStartY.value = e.clientY;
    touchStartScroll.value = carouselRef.value.scrollLeft;
    isMouseDown.value = true;
    touchStartTime = Date.now();
    isHorizontalSwipe = false;
    
    e.preventDefault();
};

const handleMouseMove = (e: MouseEvent) => {
    if (!isMouseDown.value || !carouselRef.value) return;
    
    const diffX = Math.abs(touchStartX.value - e.clientX);
    const diffY = Math.abs(touchStartY.value - e.clientY);
    
    if (diffX > diffY && diffX > 10) {
        isHorizontalSwipe = true;
        // Update scroll position while dragging
        const scrollDiff = touchStartX.value - e.clientX;
        carouselRef.value.scrollLeft = touchStartScroll.value + scrollDiff;
    }
};

const handleMouseUp = (e: MouseEvent) => {
    if (!isMouseDown.value) {
        isMouseDown.value = false;
        return;
    }
    
    if (!carouselRef.value) {
        isMouseDown.value = false;
        return;
    }
    
    const diffX = touchStartX.value - e.clientX;
    const diffY = Math.abs(touchStartY.value - e.clientY);
    const minSwipeDistance = 50;
    
    if (!isHorizontalSwipe || Math.abs(diffX) <= diffY) {
        isMouseDown.value = false;
        return;
    }
    
    const container = carouselRef.value;
    const firstItem = container.querySelector('.carousel-item') as HTMLElement;
    if (!firstItem) {
        isMouseDown.value = false;
        return;
    }
    
    const itemWidth = firstItem.offsetWidth;
    const scrollAmount = itemWidth + props.gap;
    const currentScroll = container.scrollLeft;
    const currentIndex = Math.round(currentScroll / scrollAmount);
    
    if (Math.abs(diffX) > minSwipeDistance) {
        if (diffX > 0) {
            // Dragged left - go to next
            const nextIndex = Math.min(
                currentIndex + 1,
                Math.floor((container.scrollWidth - container.clientWidth) / scrollAmount)
            );
            container.scrollTo({
                left: nextIndex * scrollAmount,
                behavior: 'smooth'
            });
        } else {
            // Dragged right - go to previous
            const prevIndex = Math.max(0, currentIndex - 1);
            container.scrollTo({
                left: prevIndex * scrollAmount,
                behavior: 'smooth'
            });
        }
    } else {
        // Snap to nearest
        container.scrollTo({
            left: currentIndex * scrollAmount,
            behavior: 'smooth'
        });
    }
    
    isMouseDown.value = false;
    isHorizontalSwipe = false;
};

// Global mouse handlers for dragging outside the element
const handleGlobalMouseMove = (e: MouseEvent) => {
    if (isMouseDown.value) {
        handleMouseMove(e);
    }
};

const handleGlobalMouseUp = (e: MouseEvent) => {
    if (isMouseDown.value) {
        handleMouseUp(e);
    }
};

onMounted(() => {
    updateScrollState();
    if (carouselRef.value) {
        carouselRef.value.addEventListener('scroll', updateScrollState);
        window.addEventListener('resize', updateScrollState);
        window.addEventListener('mousemove', handleGlobalMouseMove);
        window.addEventListener('mouseup', handleGlobalMouseUp);
    }
});

onUnmounted(() => {
    if (carouselRef.value) {
        carouselRef.value.removeEventListener('scroll', updateScrollState);
        window.removeEventListener('resize', updateScrollState);
        window.removeEventListener('mousemove', handleGlobalMouseMove);
        window.removeEventListener('mouseup', handleGlobalMouseUp);
    }
});
</script>

<template>
    <div class="relative">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 tracking-tight">{{ title }}</h2>
            <div class="flex gap-2">
                <button
                    @click="scroll('left')"
                    :disabled="!canScrollLeft"
                    :class="[
                        'p-2 border border-gray-300 transition-colors',
                        canScrollLeft
                            ? 'bg-white hover:bg-gray-50 text-gray-900 cursor-pointer hover:border-gray-400'
                            : 'bg-gray-50 text-gray-300 cursor-not-allowed border-gray-200'
                    ]"
                    aria-label="Scroll left"
                >
                    <ChevronLeft class="w-4 h-4" />
                </button>
                <button
                    @click="scroll('right')"
                    :disabled="!canScrollRight"
                    :class="[
                        'p-2 border border-gray-300 transition-colors',
                        canScrollRight
                            ? 'bg-white hover:bg-gray-50 text-gray-900 cursor-pointer hover:border-gray-400'
                            : 'bg-gray-50 text-gray-300 cursor-not-allowed border-gray-200'
                    ]"
                    aria-label="Scroll right"
                >
                    <ChevronRight class="w-4 h-4" />
                </button>
            </div>
        </div>
        
        <div
            ref="carouselRef"
            class="flex overflow-x-auto scrollbar-hide gap-6 pb-4 snap-x snap-mandatory scroll-smooth cursor-grab active:cursor-grabbing"
            :style="{ scrollbarWidth: 'none', msOverflowStyle: 'none', touchAction: 'pan-x' }"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
            @mousedown="handleMouseDown"
        >
            <div
                v-for="item in items"
                :key="item.id"
                class="flex-shrink-0 carousel-item snap-start"
                :style="{
                    width: itemsPerView === 4 
                        ? 'calc((100% - 72px) / 4)' 
                        : itemsPerView === 3
                        ? 'calc((100% - 48px) / 3)'
                        : itemsPerView === 2
                        ? 'calc((100% - 24px) / 2)'
                        : '100%',
                    minWidth: '280px',
                }"
            >
                <slot :item="item">
                    <ProductCard :product="item" />
                </slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.carousel-item {
    min-width: 0;
}

@media (max-width: 640px) {
    .carousel-item {
        width: calc(100% - 12px) !important;
        min-width: 280px;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .carousel-item {
        width: calc(50% - 12px) !important;
        min-width: 280px;
    }
}
</style>

