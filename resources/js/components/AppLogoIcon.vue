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
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :class="className" v-bind="$attrs">
        <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2" fill="grey" />
        <polyline points="4,12 8,12 10,8 12,16 14,10 16,12 20,12" stroke="white" stroke-width="2" fill="grey" />
    </svg>
</template>
