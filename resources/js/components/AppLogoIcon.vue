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
        <path 
            fill="currentColor" 
            d="M4 3h16c.55 0 1 .45 1 1v17h-2v-2H5v2H3V4c0-.55.45-1 1-1Zm2 2v3h2V5H6Zm4 0v3h2V5h-2Zm4 0v3h2V5h-2ZM6 10v3h2v-3H6Zm4 0v3h2v-3h-2Zm4 0v3h2v-3h-2ZM6 15v3h12v-3H6Z" 
        />
    </svg>
</template>
