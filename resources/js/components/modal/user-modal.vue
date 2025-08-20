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
                                            ? "Edit " +
                                              (route().current("orders.create")
                                                  ? "Pelanggan"
                                                  : "Pengguna")
                                            : "Tambah " +
                                              (route().current("orders.create")
                                                  ? "Pelanggan"
                                                  : "Pengguna") +
                                              " Baru"
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
                                    <label class="form-label"
                                        >Email
                                        <span
                                            v-show="isEdit"
                                            class="text-muted small"
                                        >
                                            (<span
                                                :class="
                                                    !model.email_verified_at
                                                        ? 'text-danger'
                                                        : 'text-success'
                                                "
                                            >
                                                {{
                                                    model.email_verified_at
                                                        ? "Terverifikasi"
                                                        : "Belum Terverifikasi"
                                                }} </span
                                            >)
                                        </span>
                                    </label>
                                    <input
                                        type="email"
                                        v-model="model.email"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.email }"
                                        placeholder="contoh@email.com"
                                        :readonly="
                                            $page.props.auth.user.id !==
                                                model.id && isEdit
                                        "
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

                                <div
                                    class="mb-3"
                                    v-if="!route().current('orders.create')"
                                >
                                    <label class="form-label">Role</label>
                                    <vue-select
                                        v-model="model.role"
                                        :options="roles"
                                        :class="{ 'is-invalid': errors.role }"
                                        placeholder="Pilih role pengguna"
                                    />
                                    <div
                                        v-if="errors.role"
                                        class="invalid-feedback d-block"
                                    >
                                        {{ errors.role[0] }}
                                    </div>
                                </div>

                                <div
                                    class="mb-3"
                                    v-if="
                                        isEdit && route().current('users.index')
                                    "
                                >
                                    <div
                                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                                    >
                                        <span class="status-label"
                                            >Status Aktif</span
                                        >
                                        <input
                                            type="checkbox"
                                            :id="'status-' + modalId"
                                            class="check"
                                            v-model="model.is_active"
                                            :checked="model.is_active ?? true"
                                        />
                                        <label
                                            :for="'status-' + modalId"
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
                                                ? "Update Pengguna"
                                                : "Tambah Pengguna"
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
        userId: {
            // Ganti model dengan userId
            type: [Number, String],
            default: null,
        },
        modalId: {
            type: String,
            default: "user-modal",
        },
        defaultRole: {
            type: String,
            default: "Pelanggan",
        },
        setErrors: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            errors: {},
            modalInstance: null,
            model: {
                id: "",
                name: "",
                email: "",
                password: "",
                phone: "",
                address: "",
                role: this.defaultRole,
                is_active: true,
            },
            isLoading: false,
        };
    },
    computed: {
        roles() {
            const currentUserRole =
                this.$page.props.auth?.user?.roles?.[0] || "";

            switch (currentUserRole) {
                case "Manajer Stok":
                    return [
                        { value: "Staff Gudang", label: "Staff Gudang" },
                        { value: "Manajer Stok", label: "Manajer Stok" },
                    ];
                case "Admin":
                    return [{ value: "Pelanggan", label: "Pelanggan" }];
                default:
                    return [{ value: "Pelanggan", label: "Pelanggan" }];
            }
        },
    },
    watch: {
        userId: {
            immediate: true,
            handler(newId) {
                if (newId && this.isEdit) {
                    this.fetchUserData(newId);
                } else if (!newId) {
                    // Reset model ketika tidak ada ID
                    this.resetModel();
                }
            },
        },
        setErrors: {
            immediate: true,
            handler(newErrors) {
                //console.log("Received errors:", newErrors);
                this.errors = newErrors;
            },
        },
    },
    mounted() {
        const el = document.getElementById(this.modalId);
        if (el) {
            this.modalInstance = new bootstrap.Modal(el);
        }
    },
    methods: {
        fetchUserData(id) {
            this.isLoading = true;

            axios
                .get(`/users/${id}`, {
                    params: { json: true },
                })
                .then((response) => {
                    // jika ada emeail_verified_at null maka arahkan ke route verifikasi email

                    if (
                        response.data.email_verified_at == null &&
                        this.$page.props.auth.user.id == id
                    ) {
                        this.$router.push({
                            name: "email-verification",
                            params: { userId: id },
                        });
                        return;
                    }

                    this.model = {
                        ...response.data,
                        phone: String(response.data.phone ?? ""),
                        role: response.data.roles?.[0] || this.defaultRole,
                    };
                })
                .catch((error) => {
                    //console.error("Error fetching user data:", error);
                    this.$swal({
                        title: "Error!",
                        text: "Gagal memuat data pengguna",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        showModal() {
            this.errors = {};
            if (this.isEdit && this.userId && !this.model.id) {
                this.fetchUserData(this.userId);
            } else if (!this.isEdit) {
                this.resetModel();
            }
            this.modalInstance?.show();
        },

        closeModal() {
            this.resetForm();
            this.modalInstance?.hide();
        },

        resetForm() {
            this.errors = {};
            if (!this.isEdit) {
                this.resetModel();
            }
        },

        resetModel() {
            this.model = {
                id: "",
                name: "",
                email: "",
                password: "",
                phone: "",
                address: "",
                role: this.defaultRole,
                is_active: true,
            };
        },

        submitForm() {
            const url = this.isEdit
                ? route("users.update", { id: this.userId })
                : route("users.store");

            const method = this.isEdit ? "put" : "post";

            // Kirim request pakai axios
            axios[method](url, this.model, {
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => {
                    // Tampilkan notifikasi sukses
                    this.$swal({
                        title: "Berhasil!",
                        text: this.isEdit
                            ? "Pengguna berhasil diperbarui"
                            : "Pengguna baru berhasil ditambahkan",
                        icon: "success",
                        confirmButtonText: "OK",
                    });

                    // Kirim data user baru ke parent (hanya ID dan responsenya)
                    this.$emit("user-submit", response.data);

                    // Tutup modal
                    this.closeModal();
                })
                .catch((error) => {
                    // console.error(
                    //     "Error submitting form:",
                    //     error.response.data.errors
                    // );
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        // console.error("Unexpected Error:", error);
                        this.$swal({
                            title: "Error!",
                            text: "Terjadi kesalahan saat menyimpan data",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                });
        },
    },
};
</script>
