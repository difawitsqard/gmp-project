<template>
    <div class="main-wrapper">
        <LoadingOverlay />
        <layout-header />
        <layout-sidebar />

        <slot />
    </div>
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
