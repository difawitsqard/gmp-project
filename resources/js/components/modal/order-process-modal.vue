<template>
    <div class="modal fade" :id="modalId" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>Tandai pesanan telah diproses</h4>
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
                            <form @submit.prevent="submitProcess">
                                <div class="mb-3">
                                    <label class="form-label"
                                        >Catatan (Opsional)</label
                                    >
                                    <textarea
                                        v-model="note"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Masukkan catatan tambahan jika diperlukan"
                                    ></textarea>
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
                                        class="btn btn-secondary me-2"
                                        @click="closeModal"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Ya, Tandai Telah Diproses
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
        modalId: { type: String, default: "order-process-modal" },
        initialStatus: { type: String, default: "waiting_processing" },
    },
    data() {
        return {
            processStatus: this.initialStatus,
            note: "",
            errors: {},
        };
    },
    methods: {
        showModal() {
            this.errors = {};
            this.processStatus = this.initialStatus;
            this.note = "";
            const el = document.getElementById(this.modalId);
            if (el) window.bootstrap.Modal.getOrCreateInstance(el).show();
        },
        closeModal() {
            const el = document.getElementById(this.modalId);
            if (el) window.bootstrap.Modal.getOrCreateInstance(el).hide();
        },
        async submitProcess() {
            this.errors = {};
            if (!this.processStatus) {
                this.errors.status = "Status pesanan harus dipilih.";
                return;
            }
            try {
                await this.$inertia.post(
                    this.route("orders.process", { id: this.orderId }),
                    { status: this.processStatus, note: this.note },
                    {
                        onSuccess: () => {
                            this.closeModal();
                            this.$swal.fire({
                                icon: "success",
                                title: "Pesanan diproses!",
                                text: "Status pesanan berhasil diperbarui.",
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
    },
};
</script>
