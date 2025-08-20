<template>
    <a
        ref="collapseHeader"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        :title="title"
        :class="{ active: isActive }"
        @click="toggleCollapse"
    >
        <slot>
            <vue-feather
                :type="isActive ? 'chevron-down' : 'chevron-up'"
            ></vue-feather>
        </slot>
    </a>
</template>

<script>
export default {
    name: "CollapseHeaderToggle",
    props: {
        title: {
            type: String,
            default: "Collapse",
        },
        initialActive: {
            type: Boolean,
            default: false,
        },
        storageKey: {
            type: String,
            default: "header-collapse-state",
        },
    },
    data() {
        return {
            isActive: this.initialActive,
        };
    },
    mounted() {
        // Load saved state from localStorage
        const savedState = localStorage.getItem(this.storageKey);

        if (savedState !== null) {
            this.isActive = savedState === "true";

            // Apply initial state to DOM
            if (this.isActive) {
                document.body.classList.add("header-collapse");
                if (this.$refs.collapseHeader) {
                    this.$refs.collapseHeader.classList.add("active");
                }
            }
        }
    },
    methods: {
        toggleCollapse() {
            // Toggle active state
            this.isActive = !this.isActive;

            // Update DOM
            const collapseHeader = this.$refs.collapseHeader;
            if (collapseHeader) {
                collapseHeader.classList.toggle("active");
            }
            document.body.classList.toggle("header-collapse");

            // Store state in localStorage
            localStorage.setItem(this.storageKey, this.isActive.toString());

            // Emit event
            this.$emit("collapse-toggled", this.isActive);
        },
    },
};
</script>
