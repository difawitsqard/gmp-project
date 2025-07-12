<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Modal } from "bootstrap";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const modalRef = ref(null);
let modalInstance = null;

const form = useForm({
    password: "",
});

onMounted(() => {
    // Inisialisasi modal menggunakan Bootstrap JS
    if (modalRef.value) {
        modalInstance = new Modal(modalRef.value);
    }
});

onUnmounted(() => {
    // Bersihkan modal saat komponen di-unmount
    if (modalInstance) {
        modalInstance.dispose();
    }
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    // Tampilkan modal menggunakan API Bootstrap
    if (modalInstance) {
        modalInstance.show();
    }

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route("current-user.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    // Sembunyikan modal menggunakan API Bootstrap
    if (modalInstance) {
        modalInstance.hide();
    }

    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title-head">
                <h6 class="pb-3">
                    <span class="me-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="feather feather-trash-2"
                        >
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path
                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                            ></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </span>
                    Hapus Akun
                </h6>
            </div>

            <div class="mt-3">
                <p>
                    Setelah akun Anda dihapus, semua sumber daya dan data akan
                    dihapus secara permanen. Sebelum menghapus akun Anda, harap
                    unduh data atau informasi apa pun yang ingin Anda
                    pertahankan.
                </p>
            </div>

            <div class="mt-4 text-end">
                <button @click="confirmUserDeletion" class="btn btn-danger">
                    Hapus Akun
                </button>
            </div>

            <!-- Modal Konfirmasi Hapus Akun - Menggunakan Bootstrap API -->
            <div
                ref="modalRef"
                class="modal fade"
                tabindex="-1"
                aria-labelledby="deleteUserModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">
                                Hapus Akun
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="closeModal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Apakah Anda yakin ingin menghapus akun Anda?
                                Setelah akun Anda dihapus, semua sumber daya dan
                                data akan dihapus secara permanen. Silakan
                                masukkan password Anda untuk mengonfirmasi bahwa
                                Anda ingin menghapus akun Anda secara permanen.
                            </p>

                            <div class="mt-3">
                                <div class="input-blocks">
                                    <label
                                        for="delete-user-password"
                                        class="form-label"
                                        >Password</label
                                    >
                                    <input
                                        id="delete-user-password"
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors.password,
                                        }"
                                        placeholder="Password"
                                        autocomplete="current-password"
                                        @keyup.enter="deleteUser"
                                    />
                                    <div
                                        v-if="form.errors.password"
                                        class="invalid-feedback"
                                    >
                                        {{ form.errors.password }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="closeModal"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger"
                                :disabled="form.processing"
                                @click="deleteUser"
                            >
                                <span
                                    v-if="form.processing"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>
                                Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
