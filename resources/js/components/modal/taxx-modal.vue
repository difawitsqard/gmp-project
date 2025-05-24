<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>
                                    {{
                                        isEdit
                                            ? "Edit Tarif Pajak"
                                            : "Tambah Tarif Pajak Baru"
                                    }}
                                </h4>
                            </div>
                            <button
                                type="button"
                                class="close d-flex ms-auto"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body custom-modal-body">
                            <form @submit.prevent="submitForm">
                                <div class="mb-3">
                                    <label class="form-label">Nama Pajak</label>
                                    <input
                                        type="text"
                                        v-model="model.name"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.name }"
                                        placeholder="e.g. Elektronik, Furnitur"
                                    />
                                    <div
                                        v-if="errors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"
                                        >Persentase (%)</label
                                    >
                                    <div class="input-group mb-1">
                                        <div class="input-group-text">
                                            <input
                                                type="checkbox"
                                                id="percentEnabled"
                                                class="check"
                                                v-model="percentEnabled"
                                                :checked="percentEnabled"
                                                @change="togglePercent"
                                                aria-label="Aktifkan input persentase"
                                            />
                                            <label
                                                for="percentEnabled"
                                                class="checktoggle"
                                            >
                                            </label>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="model.percent"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.percent,
                                            }"
                                            placeholder="e.g. 10"
                                            min="0"
                                            step="0.01"
                                            :disabled="!percentEnabled"
                                            aria-label="Input persentase"
                                        />
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div
                                        v-if="errors.percent"
                                        class="text-danger small"
                                    >
                                        {{ errors.percent[0] }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"
                                        >Jumlah Tetap ( Rp )</label
                                    >
                                    <div class="input-group mb-1">
                                        <div class="input-group-text">
                                            <input
                                                type="checkbox"
                                                id="fixedAmountEnabled"
                                                class="check"
                                                v-model="fixedAmountEnabled"
                                                :checked="fixedAmountEnabled"
                                                @change="toggleFixedAmount"
                                                aria-label="Aktifkan input jumlah tetap"
                                            />
                                            <label
                                                for="fixedAmountEnabled"
                                                class="checktoggle"
                                            >
                                            </label>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="model.fixed_amount"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.fixed_amount,
                                            }"
                                            placeholder="e.g. 10000"
                                            min="0"
                                            step="0.01"
                                            :disabled="!fixedAmountEnabled"
                                            aria-label="Input jumlah tetap"
                                        />
                                    </div>
                                    <div
                                        v-if="errors.fixed_amount"
                                        class="text-danger small"
                                    >
                                        {{ errors.fixed_amount[0] }}
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <div
                                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                                    >
                                        <span class="status-label">Status</span>
                                        <input
                                            type="checkbox"
                                            id="status"
                                            class="check"
                                            v-model="model.status"
                                            :checked="model.status ?? true"
                                        />
                                        <label
                                            for="status"
                                            class="checktoggle"
                                        ></label>
                                    </div>
                                </div>
                                <div class="modal-footer-btn">
                                    <button
                                        type="button"
                                        class="btn btn-cancel me-2"
                                        data-bs-dismiss="modal"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-submit"
                                    >
                                        {{
                                            isEdit
                                                ? "Update Tarif Pajak"
                                                : "Buat Tarif Pajak"
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    props: {
        isEdit: Boolean,
        model: {
            type: Object,
            default: () => ({
                id: "",
                name: "",
                percent: "",
                fixed_amount: "",
                status: true,
            }),
        },
        modalId: {
            type: String,
            default: "tax-modal",
        },
    },
    data() {
        return {
            errors: {},
            modalInstance: null,
            percentEnabled: true,
            fixedAmountEnabled: false,
        };
    },
    watch: {
        model: {
            handler(newVal) {
                // Jika percent dan fixed_amount kosong, default percentEnabled true
                if (!newVal.percent && !newVal.fixed_amount) {
                    this.percentEnabled = true;
                    this.fixedAmountEnabled = false;
                } else {
                    this.percentEnabled = !!newVal.percent;
                    this.fixedAmountEnabled = !!newVal.fixed_amount;
                }
            },
            immediate: true,
            deep: true,
        },
        percentEnabled(val) {
            if (val) this.fixedAmountEnabled = false;
            if (!val) this.model.percent = "";
        },
        fixedAmountEnabled(val) {
            if (val) this.percentEnabled = false;
            if (!val) this.model.fixed_amount = "";
        },
    },
    mounted() {
        const el = document.getElementById(this.modalId);
        if (el) {
            this.modalInstance = new bootstrap.Modal(el);
        }
    },
    methods: {
        showModal() {
            this.errors = {};
            this.modalInstance?.show();
        },
        closeModal() {
            this.resetForm();
            this.modalInstance?.hide();
        },
        resetForm() {
            this.errors = {};
        },
        submitForm() {
            const url = this.isEdit
                ? route("taxes.update", { id: this.model.id })
                : route("taxes.store");

            const method = this.isEdit ? "put" : "post";

            // Kirim request pakai axios
            axios[method](url, this.model)
                .then((response) => {
                    // Kirim data unit baru ke parent (bisa untuk update list)
                    this.$emit("tax-submit", response.data);

                    // Tutup modal
                    this.closeModal();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.error("Unexpected Error:", error);
                    }
                });
        },
    },
};
</script>
