<script setup>
import { ref, computed, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import ConfirmsPassword from "@/Components/modal/ConfirmsPassword.vue";

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: "",
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(
        route("two-factor.enable"),
        {},
        {
            preserveScroll: true,
            onSuccess: () =>
                Promise.all([
                    showQrCode(),
                    showSetupKey(),
                    showRecoveryCodes(),
                ]),
            onFinish: () => {
                enabling.value = false;
                confirming.value = props.requiresConfirmation;
            },
        }
    );
};

const showQrCode = () => {
    return axios.get(route("two-factor.qr-code")).then((response) => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route("two-factor.secret-key")).then((response) => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route("two-factor.recovery-codes")).then((response) => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route("two-factor.confirm"), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route("two-factor.recovery-codes"))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route("two-factor.disable"), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
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
                            class="feather feather-shield"
                        >
                            <path
                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                            ></path>
                        </svg>
                    </span>
                    Autentikasi Dua Faktor
                </h6>
            </div>

            <div class="mt-3">
                <h4
                    v-if="twoFactorEnabled && !confirming"
                    class="text-lg font-medium"
                >
                    Anda telah mengaktifkan autentikasi dua faktor.
                </h4>

                <h4
                    v-else-if="twoFactorEnabled && confirming"
                    class="text-lg font-medium"
                >
                    Selesaikan pengaktifan autentikasi dua faktor.
                </h4>

                <h4 v-else class="text-lg font-medium">
                    Anda belum mengaktifkan autentikasi dua faktor.
                </h4>

                <div class="mt-3">
                    <p>
                        Ketika autentikasi dua faktor diaktifkan, Anda akan
                        diminta untuk memasukkan token acak dan aman selama
                        autentikasi. Anda dapat memperoleh token ini dari
                        aplikasi Google Authenticator di ponsel Anda.
                    </p>
                </div>
            </div>

            <div v-if="twoFactorEnabled" class="mt-4">
                <div v-if="qrCode">
                    <div class="mt-4">
                        <p v-if="confirming" class="font-semibold">
                            Untuk menyelesaikan pengaktifan autentikasi dua
                            faktor, pindai kode QR berikut menggunakan aplikasi
                            autentikator ponsel Anda atau masukkan kunci
                            pengaturan dan berikan kode OTP yang dihasilkan.
                        </p>

                        <p v-else>
                            Autentikasi dua faktor sekarang aktif. Pindai kode
                            QR berikut menggunakan aplikasi autentikator ponsel
                            Anda atau masukkan kunci pengaturan.
                        </p>
                    </div>

                    <div
                        class="mt-4 p-2 d-inline-block bg-white border"
                        v-html="qrCode"
                    />

                    <div v-if="setupKey" class="mt-4">
                        <p class="font-semibold">
                            Kunci Pengaturan: <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4 col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Kode</label>
                            <input
                                id="code"
                                v-model="confirmationForm.code"
                                type="text"
                                class="form-control"
                                :class="{
                                    'is-invalid': confirmationForm.errors.code,
                                }"
                                inputmode="numeric"
                                autofocus
                                autocomplete="one-time-code"
                                placeholder="Masukkan kode dari aplikasi autentikator"
                                @keyup.enter="confirmTwoFactorAuthentication"
                            />
                            <div
                                v-if="confirmationForm.errors.code"
                                class="text-danger mt-1"
                            >
                                {{ confirmationForm.errors.code }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="recoveryCodes.length > 0 && !confirming"
                    class="mt-4"
                >
                    <div class="mt-4">
                        <p class="font-semibold">
                            Simpan kode pemulihan ini di pengelola kata sandi
                            yang aman. Kode ini dapat digunakan untuk memulihkan
                            akses ke akun Anda jika perangkat autentikasi dua
                            faktor Anda hilang.
                        </p>
                    </div>

                    <div
                        class="mt-4 p-3 bg-light text-dark rounded font-monospace recovery-code"
                    >
                        <div class="row">
                            <div
                                v-for="(code, index) in recoveryCodes"
                                :key="index"
                            >
                                {{ code }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div v-if="!twoFactorEnabled" class="text-end">
                    <ConfirmsPassword
                        @confirmed="enableTwoFactorAuthentication"
                    >
                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="enabling"
                        >
                            <span
                                v-if="enabling"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            Aktifkan
                        </button>
                    </ConfirmsPassword>
                </div>

                <div v-else class="d-flex justify-content-end">
                    <ConfirmsPassword
                        @confirmed="confirmTwoFactorAuthentication"
                    >
                        <button
                            v-if="confirming"
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="enabling"
                        >
                            <span
                                v-if="enabling"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            Konfirmasi
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <button
                            v-if="recoveryCodes.length > 0 && !confirming"
                            type="button"
                            class="btn btn-secondary me-2"
                        >
                            Regenerasi Kode Pemulihan
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <button
                            v-if="recoveryCodes.length === 0 && !confirming"
                            type="button"
                            class="btn btn-secondary me-2"
                        >
                            Tampilkan Kode Pemulihan
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword
                        @confirmed="disableTwoFactorAuthentication"
                    >
                        <button
                            v-if="confirming"
                            type="button"
                            class="btn btn-secondary"
                            :disabled="disabling"
                        >
                            Batal
                        </button>
                    </ConfirmsPassword>

                    <ConfirmsPassword
                        @confirmed="disableTwoFactorAuthentication"
                    >
                        <button
                            v-if="!confirming"
                            type="button"
                            class="btn btn-danger"
                            :disabled="disabling"
                        >
                            <span
                                v-if="disabling"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            Nonaktifkan
                        </button>
                    </ConfirmsPassword>
                </div>
            </div>
        </div>
    </div>
</template>
