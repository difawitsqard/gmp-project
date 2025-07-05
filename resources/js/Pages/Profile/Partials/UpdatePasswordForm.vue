<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("user-password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title-head">
                <h6>
                    <span>
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
                            class="feather feather-lock"
                        >
                            <rect
                                x="3"
                                y="11"
                                width="18"
                                height="11"
                                rx="2"
                                ry="2"
                            ></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    Perbarui Password
                </h6>
            </div>

            <div
                v-show="form.recentlySuccessful"
                class="alert alert-solid-success alert-dismissible fade show"
            >
                Password berhasil diperbarui.
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                >
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <form @submit.prevent="updatePassword">
                <div class="row">
                    <div class="col-lg-4 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Password Saat Ini</label>
                            <input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="form-control"
                                autocomplete="current-password"
                                placeholder="Masukkan password saat ini"
                            />
                            <div
                                v-if="form.errors.current_password"
                                class="text-danger mt-1"
                            >
                                {{ form.errors.current_password }}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Password Baru</label>
                            <input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                autocomplete="new-password"
                                placeholder="Masukkan password baru"
                            />
                            <div
                                v-if="form.errors.password"
                                class="text-danger mt-1"
                            >
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label"
                                >Konfirmasi Password</label
                            >
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="form-control"
                                autocomplete="new-password"
                                placeholder="Konfirmasi password baru"
                            />
                            <div
                                v-if="form.errors.password_confirmation"
                                class="text-danger mt-1"
                            >
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button
                            type="submit"
                            class="btn btn-submit me-2"
                            :disabled="form.processing"
                        >
                            Simpan Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
