<script setup>
import * as Yup from "yup";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import TextInput from "@/components/TextInput.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    email: String,
    token: String,
});

const schema = Yup.object().shape({
    email: Yup.string()
        .required("Email wajib diisi")
        .email("Format email salah"),
    password: Yup.string()
        .min(6, "Minimal 6 karakter")
        .required("Password wajib diisi"),
    password_confirmation: Yup.string()
        .oneOf([Yup.ref("password"), null], "Konfirmasi password tidak sama")
        .required("Konfirmasi password wajib diisi"),
});

const form = useForm({
    token: props.token,
    email: props.email || "",
    password: "",
    password_confirmation: "",
});

const errors = ref({});

const submit = async () => {
    errors.value = {};
    try {
        await schema.validate(form, { abortEarly: false });
        form.post(route("password.update"), {
            onFinish: () => form.reset("password", "password_confirmation"),
            onError: (err) => {
                errors.value = err;
            },
        });
    } catch (err) {
        if (err.inner) {
            err.inner.forEach((e) => {
                errors.value[e.path] = e.message;
            });
        }
    }
};
</script>

<template>
    <Head title="Reset Password" />
    <div class="account-page">
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
                                    <h3>Reset Password</h3>
                                    <h4>
                                        Silakan masukkan email dan password baru
                                        Anda.
                                    </h4>
                                </div>
                                <div class="form-login">
                                    <label class="form-label">Email</label>
                                    <div class="form-addons">
                                        <TextInput
                                            name="email"
                                            type="email"
                                            placeholder="example@guritamandala.com"
                                            v-model="form.email"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.email ||
                                                    form.errors.email,
                                            }"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            readonly
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                errors.email ||
                                                form.errors.email
                                            }}
                                        </div>
                                        <vue-feather
                                            type="mail"
                                            v-if="
                                                !errors.email &&
                                                !form.errors.email
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="form-login">
                                    <label>Password Baru</label>
                                    <div class="pass-group">
                                        <TextInput
                                            name="password"
                                            placeholder="Password baru"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control pass-input"
                                            :class="{
                                                'is-invalid':
                                                    errors.password ||
                                                    form.errors.password,
                                            }"
                                            autocomplete="new-password"
                                            required
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                errors.password ||
                                                form.errors.password
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-login">
                                    <label>Konfirmasi Password</label>
                                    <div class="pass-group">
                                        <TextInput
                                            name="password_confirmation"
                                            placeholder="Konfirmasi password"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="form-control pass-input"
                                            :class="{
                                                'is-invalid':
                                                    errors.password_confirmation ||
                                                    form.errors
                                                        .password_confirmation,
                                            }"
                                            autocomplete="new-password"
                                            required
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                errors.password_confirmation ||
                                                form.errors
                                                    .password_confirmation
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-login">
                                    <button
                                        class="btn btn-login"
                                        type="submit"
                                        :disabled="form.processing"
                                    >
                                        Reset Password
                                    </button>
                                </div>
                                <div class="signinform">
                                    <h4>
                                        Kembali ke
                                        <Link
                                            :href="route('login')"
                                            class="hover-a"
                                        >
                                            Login
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
                            Copyright &copy; {{ new Date().getFullYear() }} All
                            rights reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
