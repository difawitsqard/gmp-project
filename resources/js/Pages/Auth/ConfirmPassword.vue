<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const passwordInput = ref(null);
const showPassword = ref(false);

const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => {
            form.reset();
            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head title="Konfirmasi Password" />

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
                                    <h3>Area Terproteksi</h3>
                                    <h4>
                                        Ini adalah area terproteksi dari
                                        aplikasi. Silakan konfirmasi password
                                        Anda sebelum melanjutkan.
                                    </h4>
                                </div>
                                <div class="form-login">
                                    <label>Password</label>
                                    <div class="pass-group">
                                        <input
                                            id="password"
                                            ref="passwordInput"
                                            v-model="form.password"
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            class="form-control pass-input"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.password,
                                            }"
                                            required
                                            autocomplete="current-password"
                                            autofocus
                                            placeholder="Masukkan password Anda"
                                        />
                                        <span
                                            @click="toggleShow"
                                            class="toggle-password"
                                        >
                                            <i
                                                :class="{
                                                    'fas fa-eye': showPassword,
                                                    'fas fa-eye-slash':
                                                        !showPassword,
                                                }"
                                            ></i>
                                        </span>
                                        <div
                                            v-if="form.errors.password"
                                            class="invalid-feedback"
                                        >
                                            {{ form.errors.password }}
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
                                        Konfirmasi
                                    </button>
                                </div>

                                <div class="signinform text-center">
                                    <h4>
                                        <Link
                                            :href="route('login')"
                                            class="hover-a"
                                        >
                                            Kembali ke halaman login
                                        </Link>
                                    </h4>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div
                        class="my-4 d-flex justify-content-center align-items-center copyright-text"
                    >
                        <p>
                            Copyright &copy;
                            {{ new Date().getFullYear() }} All rights reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
