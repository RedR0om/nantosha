<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Capsule/Pill Icon - Simple rounded rectangle -->
        <rect x="4" y="8" width="16" height="8" rx="4" fill="currentColor" />
    </svg>
</template>
