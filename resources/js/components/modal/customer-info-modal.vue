<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-dialog-centered custom-modal-two">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="content">
                        <div class="modal-header border-0 custom-modal-header">
                            <div class="page-title">
                                <h4>Edit Informasi Pelanggan</h4>
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
                                    <label class="form-label">Nama</label>
                                    <input
                                        type="text"
                                        v-model="model.name"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.name }"
                                        placeholder="Masukkan nama lengkap"
                                    />
                                    <div
                                        v-if="errors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.name[0] }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input
                                        type="email"
                                        v-model="model.email"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.email }"
                                        placeholder="contoh@email.com"
                                    />
                                    <div
                                        v-if="errors.email"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.email[0] }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nomor HP</label>
                                    <input
                                        type="text"
                                        v-model="model.phone"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.phone }"
                                        placeholder="Contoh: 08123456789"
                                    />
                                    <div
                                        v-if="errors.phone"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.phone[0] }}
                                    </div>
                                </div>

                                <div class="mb-3 input-blocks">
                                    <label class="form-label">Alamat</label>
                                    <textarea
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errors.address,
                                        }"
                                        v-model="model.address"
                                        placeholder="Masukkan alamat lengkap"
                                    ></textarea>
                                    <div
                                        v-if="errors.address"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.address[0] }}
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
                                        Simpan Perubahan
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
        customerInfo: {
            type: Object,
            default: () => ({
                id: null,
                name: "",
                email: "",
                phone: "",
                address: "",
            }),
        },
        backendErrors: {
            type: Object,
            default: () => ({}),
        },
        modalId: {
            type: String,
            default: "customer-info-modal",
        },
    },
    data() {
        return {
            errors: {},
            modalInstance: null,
            model: {
                id: null,
                name: "",
                email: "",
                phone: "",
                address: "",
            },
        };
    },
    watch: {
        customerInfo: {
            immediate: true,
            handler(newInfo) {
                if (newInfo) {
                    this.model = { ...newInfo };
                }
            },
        },
        backendErrors: {
            handler(newErrors) {
                if (newErrors && Object.keys(newErrors).length > 0) {
                    // Transform backend errors format if needed
                    const transformedErrors = {};

                    Object.keys(newErrors).forEach((key) => {
                        // Check if error is already in array format
                        if (Array.isArray(newErrors[key])) {
                            transformedErrors[key] = newErrors[key];
                        } else {
                            // Convert to array format
                            transformedErrors[key] = [newErrors[key]];
                        }
                    });

                    this.errors = transformedErrors;
                }
            },
            immediate: true,
            deep: true,
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
            // Load customer information from props
            this.model = { ...this.customerInfo };
            this.modalInstance?.show();
        },

        closeModal() {
            this.resetForm();
            this.modalInstance?.hide();
        },

        resetForm() {
            this.errors = {};
            this.model = { ...this.customerInfo };
        },

        validateForm() {
            this.errors = {};
            let isValid = true;

            if (!this.model.name) {
                this.errors.name = ["Nama pelanggan harus diisi"];
                isValid = false;
            }

            if (
                this.model.email &&
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.model.email)
            ) {
                this.errors.email = ["Format email tidak valid"];
                isValid = false;
            }

            return isValid;
        },

        setErrors(errors) {
            this.errors = errors;
        },

        submitForm() {
            if (!this.validateForm()) {
                // Emit event with validation status
                this.$emit("validation-failed", this.errors);
                return;
            }

            // Emit event to parent component with updated customer info
            this.$emit("update-customer-info", { ...this.model });

            // Close modal
            this.closeModal();

            // Show success notification
            this.$swal({
                title: "Berhasil!",
                text: "Informasi pelanggan berhasil diperbarui",
                icon: "success",
                confirmButtonText: "OK",
            });
        },
    },
};
</script>
