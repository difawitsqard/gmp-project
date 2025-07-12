<script setup>
import { nextTick, ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const recovery = ref(false);

const form = useForm({
    code: "",
    recovery_code: "",
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = "";
    } else {
        codeInput.value.focus();
        form.recovery_code = "";
    }
};

const submit = () => {
    form.post(route("two-factor.login"));
};
</script>

<template>
    <Head title="Autentikasi Dua Faktor" />

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper login-new">
                <div class="container">
                    <div class="login-content user-login">
                        <div class="login-logo">
                            <img src="@/assets/img/logo-small.png" alt="img" />
                            <Link :href="'/'" class="login-logo logo-white">
                                <img src="@/assets/img/logo-small.png" alt="" />
                            </Link>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="login-userset">
                                <div class="login-userheading">
                                    <h3>Autentikasi Dua Faktor</h3>
                                    <h4 v-if="!recovery">
                                        Silakan konfirmasi akses ke akun Anda
                                        dengan memasukkan kode autentikasi yang
                                        disediakan oleh aplikasi autentikator
                                        Anda.
                                    </h4>
                                    <h4 v-else>
                                        Silakan konfirmasi akses ke akun Anda
                                        dengan memasukkan salah satu kode
                                        pemulihan darurat Anda.
                                    </h4>
                                </div>

                                <div class="form-login" v-if="!recovery">
                                    <label>Kode Autentikasi</label>
                                    <div class="form-addons">
                                        <input
                                            id="code"
                                            ref="codeInput"
                                            v-model="form.code"
                                            type="text"
                                            inputmode="numeric"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': form.errors.code,
                                            }"
                                            autofocus
                                            autocomplete="one-time-code"
                                            placeholder="Masukkan kode 6 digit"
                                        />
                                        <div
                                            v-if="form.errors.code"
                                            class="invalid-feedback"
                                        >
                                            {{ form.errors.code }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-login" v-else>
                                    <label>Kode Pemulihan</label>
                                    <div class="form-addons">
                                        <input
                                            id="recovery_code"
                                            ref="recoveryCodeInput"
                                            v-model="form.recovery_code"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.recovery_code,
                                            }"
                                            autocomplete="one-time-code"
                                            placeholder="Masukkan kode pemulihan"
                                        />
                                        <div
                                            v-if="form.errors.recovery_code"
                                            class="invalid-feedback"
                                        >
                                            {{ form.errors.recovery_code }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-login">
                                    <button
                                        class="btn btn-login"
                                        type="submit"
                                        :disabled="form.processing"
                                    >
                                        <span
                                            v-if="form.processing"
                                            class="spinner-border spinner-border-sm me-2"
                                        ></span>
                                        Masuk
                                    </button>
                                </div>

                                <div class="signinform text-center">
                                    <h4>
                                        <a
                                            href="#"
                                            @click.prevent="toggleRecovery"
                                            class="hover-a"
                                        >
                                            <template v-if="!recovery">
                                                Gunakan kode pemulihan
                                            </template>
                                            <template v-else>
                                                Gunakan kode autentikasi
                                            </template>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div
                        class="my-4 d-flex justify-content-center align-items-center copyright-text"
                    >
                        <p>
                            Copyright &copy; {{ new Date().getFullYear() }} All
                            rights reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
