<template>
    <LoadingOverlay />
    <layout-header />
    <layout-sidebar />

    <slot />
</template>

<script>
import LoadingOverlay from "@/components/LoadingOverlay.vue";

export default {
    components: {
        LoadingOverlay,
    },
    props: {
        title: String,
    },
};
</script>
