<template>
    <Transition>
        <div v-show="visible" id="global-loader">
            <div class="whirly-loader"></div>
        </div>
    </Transition>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>

<script setup>
defineProps({ visible: Boolean });
</script>
