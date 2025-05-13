<template>
    <vue-multiselect
        v-model="selected"
        :options="options"
        :multiple="true"
        :searchable="true"
        :loading="isLoading"
        :internal-search="false"
        :clear-on-select="false"
        :close-on-select="false"
        :preserve-search="true"
        placeholder="Cari Produk..."
        label="name"
        track-by="id"
        @search-change="searchProducts"
        :custom-label="customLabel"
    >
        <template #option="{ option }">
            <div class="option-container">
                <img
                    :src="option.image_url || placeholderImage"
                    alt="Product Image"
                    class="option-image"
                />
                <div class="option-text">
                    <div class="option-name">{{ option.name }}</div>
                    <div class="option-description">
                        {{ option.sku }} - {{ option.category }}
                    </div>
                </div>
            </div>
        </template>
    </vue-multiselect>
</template>

<script>
import { debounce } from "lodash";
export default {
    name: "AsyncProductSelect",
    props: {
        modelValue: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            selected: this.modelValue,
            options: [],
            isLoading: false,
            placeholderImage: "/uploads/images/placeholder-image.webp", // URL gambar placeholder
        };
    },
    watch: {
        modelValue(newVal) {
            this.selected = newVal;
        },
        selected(newVal) {
            this.$emit("update:modelValue", newVal);
        },
    },
    methods: {
        searchProducts: debounce(function (query) {
            if (!query || query.length < 2) {
                this.options = [];
                return;
            }

            this.isLoading = true;

            axios
                .get("/products/search", {
                    params: { q: query },
                })
                .then((response) => {
                    this.options = response.data;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }, 300), // Tunggu 300ms setelah pengguna berhenti mengetik
    },
};
</script>

<style>
.option-container {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.option-image {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.option-text {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.option-name {
    font-weight: bold;
    font-size: 14px;
    color: #5b6670;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.multiselect__option:hover .option-name {
    color: #ffffff;
}

.option-name:hover {
    color: #ffffff;
}

.option-description {
    font-size: 12px;
    color: #ebebeb;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.select2-container {
    z-index: 1 !important;
}
</style>
