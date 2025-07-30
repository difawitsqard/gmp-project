<script setup>
import * as Yup from "yup";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import TextInput from "@/components/TextInput.vue";

// Yup schema
const schema = Yup.object().shape({
    name: Yup.string().required("Nama wajib diisi"),
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
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const errors = ref({});

const onSubmit = async () => {
    errors.value = {};
    try {
        await schema.validate(form, { abortEarly: false });
        form.post(route("register"), {
            onFinish: () => form.reset("password", "password_confirmation"),
            onError: (err) => {
                Object.assign(errors.value, err);
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
    <Head title="Daftar" />
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
                        <form @submit.prevent="onSubmit">
                            <div class="login-userset">
                                <div class="login-userheading">
                                    <h3>Daftar Akun</h3>
                                    <h4>
                                        Silakan isi data untuk membuat akun
                                        baru.
                                    </h4>
                                </div>
                                <div class="form-login">
                                    <label class="form-label">Nama</label>
                                    <div class="form-addons">
                                        <TextInput
                                            name="name"
                                            type="text"
                                            placeholder="Nama lengkap"
                                            v-model="form.name"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.name ||
                                                    form.errors.name,
                                            }"
                                            required
                                            autofocus
                                            autocomplete="name"
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                errors.name || form.errors.name
                                            }}
                                        </div>
                                        <vue-feather
                                            type="user"
                                            v-if="
                                                !errors.name &&
                                                !form.errors.name
                                            "
                                        />
                                    </div>
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
                                            autocomplete="username"
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
                                    <label>Password</label>
                                    <div class="pass-group">
                                        <TextInput
                                            name="password"
                                            placeholder="Password"
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
                                        Daftar
                                    </button>
                                </div>
                                <div class="signinform">
                                    <h4>
                                        Sudah punya akun?
                                        <Link
                                            :href="route('login')"
                                            class="hover-a"
                                        >
                                            Masuk
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
