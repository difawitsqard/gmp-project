<script setup>
import { ref, reactive, nextTick, onMounted, onBeforeUnmount } from "vue";

const emit = defineEmits(["confirmed"]);

const props = defineProps({
    title: {
        type: String,
        default: "Konfirmasi Password",
    },
    content: {
        type: String,
        default:
            "Untuk keamanan Anda, silakan konfirmasi password Anda untuk melanjutkan.",
    },
    button: {
        type: String,
        default: "Konfirmasi",
    },
    modalId: {
        type: String,
        default: null,
    },
});

// Buat ID modal unik jika tidak disediakan
const uniqueModalId = ref(
    props.modalId ||
        `confirm-password-modal-${Date.now()}-${Math.floor(
            Math.random() * 1000
        )}`
);

const confirmingPassword = ref(false);
const modalInstance = ref(null);
const form = reactive({
    password: "",
    error: "",
    processing: false,
});

const passwordInput = ref(null);

// Inisialisasi modal saat komponen di-mount
onMounted(() => {
    const el = document.getElementById(uniqueModalId.value);
    if (el && typeof bootstrap !== "undefined") {
        modalInstance.value = new bootstrap.Modal(el);
    }
});

// Bersihkan saat unmount
onBeforeUnmount(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
    }
});

const startConfirmingPassword = () => {
    axios.get(route("password.confirmation")).then((response) => {
        if (response.data.confirmed) {
            emit("confirmed");
        } else {
            confirmingPassword.value = true;

            // Tunggu sampai DOM diupdate
            nextTick(() => {
                // Tampilkan modal
                if (modalInstance.value) {
                    modalInstance.value.show();
                } else {
                    // Re-inisialisasi jika perlu
                    const el = document.getElementById(uniqueModalId.value);
                    if (el && typeof bootstrap !== "undefined") {
                        modalInstance.value = new bootstrap.Modal(el);
                        modalInstance.value.show();
                    }
                }

                // Focus ke input password setelah modal ditampilkan
                setTimeout(() => {
                    if (passwordInput.value) {
                        passwordInput.value.focus();
                    }
                }, 250);
            });
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios
        .post(route("password.confirm"), {
            password: form.password,
        })
        .then(() => {
            form.processing = false;
            closeModal();
            nextTick().then(() => emit("confirmed"));
        })
        .catch((error) => {
            form.processing = false;
            form.error = error.response.data.errors.password[0];
            if (passwordInput.value) {
                passwordInput.value.focus();
            }
        });
};

const closeModal = () => {
    // Sembunyikan modal menggunakan instance yang disimpan
    if (modalInstance.value) {
        modalInstance.value.hide();
    }

    confirmingPassword.value = false;
    form.password = "";
    form.error = "";
};
</script>

<template>
    <span @click="startConfirmingPassword">
        <slot />
    </span>

    <teleport to="body">
        <div class="modal fade" :id="uniqueModalId">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="closeModal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ content }}</p>

                        <div class="mt-3">
                            <div class="input-blocks">
                                <label class="form-label">Password</label>
                                <input
                                    :id="`password-${uniqueModalId}`"
                                    ref="passwordInput"
                                    v-model="form.password"
                                    type="password"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.error,
                                    }"
                                    placeholder="Password"
                                    autocomplete="current-password"
                                    @keyup.enter="confirmPassword"
                                />
                                <div v-if="form.error" class="text-danger mt-1">
                                    {{ form.error }}
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
                            class="btn btn-primary"
                            :disabled="form.processing"
                            @click="confirmPassword"
                        >
                            <span
                                v-if="form.processing"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            {{ button }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>
