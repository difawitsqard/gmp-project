<template>
    <div class="modal fade" :id="modalId" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>Konfirmasi Pesanan</h4>
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
                                    <label class="form-label"
                                        >Biaya Pengiriman</label
                                    >
                                    <input
                                        type="number"
                                        v-model="shippingFee"
                                        class="form-control"
                                        min="0"
                                        required
                                        :class="{
                                            'is-invalid': errors.shipping_fee,
                                        }"
                                        placeholder="Masukkan biaya pengiriman"
                                    />
                                    <div
                                        v-if="errors.shipping_fee"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.shipping_fee }}
                                    </div>
                                </div>
                                <div
                                    v-if="errors.general"
                                    class="alert alert-danger"
                                >
                                    {{ errors.general }}
                                </div>
                                <div class="modal-footer-btn mt-4">
                                    <button
                                        type="button"
                                        class="btn btn-danger me-2"
                                        @click="cancelOrder"
                                    >
                                        <div
                                            class="d-flex align-items-center p-0"
                                        >
                                            <vue-feather
                                                type="x"
                                                class="me-2"
                                            />
                                            Batalkan
                                        </div>
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-submit"
                                    >
                                        <div
                                            class="d-flex align-items-center p-0"
                                        >
                                            <vue-feather
                                                type="check"
                                                class="me-2"
                                            />
                                            Konfirmasi
                                        </div>
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
export default {
    props: {
        orderId: { type: String, required: true },
        modalId: { type: String, default: "order-confirmation-modal" },
        initialShippingFee: { type: Number, default: 0 },
    },
    data() {
        return {
            shippingFee: this.initialShippingFee,
            errors: {},
        };
    },
    methods: {
        showModal() {
            this.errors = {};
            this.shippingFee = this.initialShippingFee;
            const el = document.getElementById(this.modalId);
            if (el) window.bootstrap.Modal.getOrCreateInstance(el).show();
        },
        closeModal() {
            const el = document.getElementById(this.modalId);
            if (el) window.bootstrap.Modal.getOrCreateInstance(el).hide();
        },
        async submitForm() {
            this.errors = {};
            if (this.shippingFee === null || this.shippingFee < 0) {
                this.errors.shipping_fee =
                    "Biaya pengiriman harus diisi dan tidak boleh negatif.";
                return;
            }
            try {
                await this.$inertia.post(
                    this.route("orders.confirm", { id: this.orderId }),
                    { shipping_fee: this.shippingFee },
                    {
                        onSuccess: () => {
                            this.closeModal();
                            this.$swal.fire({
                                icon: "success",
                                title: "Pesanan dikonfirmasi!",
                                text: "Pesanan siap untuk pembayaran.",
                            });
                        },
                        onError: (err) => {
                            this.errors = err;
                        },
                    }
                );
            } catch (e) {
                this.errors.general = "Terjadi kesalahan, silakan coba lagi.";
            }
        },
        async cancelOrder() {
            this.errors = {};
            this.closeModal();
            const confirmed = await this.$swal.fire({
                title: "Batalkan Pesanan",
                text: "Apakah Anda yakin ingin membatalkan pesanan ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, batalkan!",
                cancelButtonText: "Batal",
            });
            if (confirmed.isConfirmed) {
                await this.$inertia.post(
                    this.route("orders.cancel", this.orderId),
                    {},
                    {
                        onSuccess: () => {
                            this.closeModal();
                            this.$swal.fire({
                                icon: "success",
                                title: "Pesanan dibatalkan!",
                                text: "Pesanan telah dibatalkan.",
                            });
                        },
                        onError: (err) => {
                            this.errors.general =
                                err.error || "Gagal membatalkan pesanan.";
                        },
                    }
                );
            } else {
                this.showModal();
            }
        },
    },
};
</script>
