<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Slide {
    id: number | string;
    image?: string;
    video?: string;
    media_type?: string;
    title?: string;
    subtitle?: string;
    link?: string;
    buttonText?: string;
}

interface Props {
    slides: Slide[];
    autoPlayInterval?: number;
}

const props = withDefaults(defineProps<Props>(), {
    autoPlayInterval: 5000, // 5 seconds default
});

const currentIndex = ref(0);
const autoPlayTimer = ref<NodeJS.Timeout | null>(null);
const isPaused = ref(false);

const goToSlide = (index: number) => {
    currentIndex.value = index;
    resetAutoPlay();
};

const nextSlide = () => {
    currentIndex.value = (currentIndex.value + 1) % props.slides.length;
    resetAutoPlay();
};

const prevSlide = () => {
    currentIndex.value = (currentIndex.value - 1 + props.slides.length) % props.slides.length;
    resetAutoPlay();
};

const startAutoPlay = () => {
    if (autoPlayTimer.value) {
        clearInterval(autoPlayTimer.value);
    }
    
    if (props.slides.length <= 1) return;
    
    autoPlayTimer.value = setInterval(() => {
        if (!isPaused.value) {
            nextSlide();
        }
    }, props.autoPlayInterval);
};

const resetAutoPlay = () => {
    startAutoPlay();
};

const pauseAutoPlay = () => {
    isPaused.value = true;
};

const resumeAutoPlay = () => {
    isPaused.value = false;
};

onMounted(() => {
    if (props.slides.length > 0) {
        startAutoPlay();
    }
});

onUnmounted(() => {
    if (autoPlayTimer.value) {
        clearInterval(autoPlayTimer.value);
    }
});
</script>

<template>
    <div
        class="relative w-full overflow-hidden"
        @mouseenter="pauseAutoPlay"
        @mouseleave="resumeAutoPlay"
    >
        <!-- Carousel Container -->
        <div class="relative h-[400px] md:h-[500px] lg:h-[600px]">
            <div
                v-for="(slide, index) in slides"
                :key="slide.id"
                class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                :class="{
                    'opacity-100 z-10': index === currentIndex,
                    'opacity-0 z-0': index !== currentIndex,
                }"
            >
                <div class="relative w-full h-full">
                    <!-- Video -->
                    <video
                        v-if="slide.media_type === 'video' && slide.video"
                        :src="slide.video"
                        autoplay
                        muted
                        loop
                        playsinline
                        class="w-full h-full object-cover"
                    ></video>
                    <!-- Image -->
                    <img
                        v-else-if="slide.image"
                        :src="slide.image"
                        :alt="slide.title || `Slide ${index + 1}`"
                        class="w-full h-full object-cover"
                    />
                    <!-- Overlay for better text readability -->
                    <div class="absolute inset-0 bg-black/20"></div>
                    
                    <!-- Content Overlay -->
                    <div
                        v-if="slide.title || slide.subtitle || slide.buttonText"
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <div class="text-center px-4 max-w-4xl mx-auto">
                            <h2
                                v-if="slide.title"
                                class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 drop-shadow-lg"
                            >
                                {{ slide.title }}
                            </h2>
                            <p
                                v-if="slide.subtitle"
                                class="text-lg md:text-xl lg:text-2xl text-white mb-6 drop-shadow-md"
                            >
                                {{ slide.subtitle }}
                            </p>
                            <Link
                                v-if="slide.link && slide.buttonText"
                                :href="slide.link"
                                class="inline-block bg-white text-gray-900 px-8 py-3 text-sm font-semibold hover:bg-gray-100 transition-colors uppercase tracking-wide"
                            >
                                {{ slide.buttonText }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button
            v-if="slides.length > 1"
            @click="prevSlide"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/80 hover:bg-white text-gray-900 p-3 rounded-full shadow-lg transition-all hover:scale-110"
            aria-label="Previous slide"
        >
            <ChevronLeft class="w-6 h-6" />
        </button>
        <button
            v-if="slides.length > 1"
            @click="nextSlide"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/80 hover:bg-white text-gray-900 p-3 rounded-full shadow-lg transition-all hover:scale-110"
            aria-label="Next slide"
        >
            <ChevronRight class="w-6 h-6" />
        </button>

        <!-- Dots Indicator -->
        <div
            v-if="slides.length > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2"
        >
            <button
                v-for="(slide, index) in slides"
                :key="slide.id"
                @click="goToSlide(index)"
                :class="[
                    'w-2 h-2 rounded-full transition-all',
                    index === currentIndex
                        ? 'bg-white w-8'
                        : 'bg-white/50 hover:bg-white/75'
                ]"
                :aria-label="`Go to slide ${index + 1}`"
            />
        </div>
    </div>
</template>

