<script setup>
import * as Yup from "yup";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import TextInput from "@/components/TextInput.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

// Yup schema
const schema = Yup.object().shape({
    email: Yup.string().required("Email is required").email("Invalid email"),
    password: Yup.string().min(6, "Min 6 chars").required("Password required"),
});

// Show password state
const showPassword = ref(false);
const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

// Inertia form
const form = useForm({
    email: "",
    password: "",
    remember: false,
});

// Error state lokal
const errors = ref({});

// Submit handler
const onSubmit = async () => {
    try {
        errors.value = {};

        // validasi manual
        const validData = await schema.validate(form, { abortEarly: false });

        // kirim ke server
        form.post(route("login"), {
            onFinish: () => form.reset("password"),
            onError: (err) => console.log("Server errors:", err),
        });
    } catch (err) {
        if (err.inner) {
            // kumpulkan error Yup
            err.inner.forEach((e) => {
                errors.value[e.path] = e.message;
            });
        }
    }
};
</script>

<template>
    <Head title="Masuk" />

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
                                    <h3>Masuk</h3>
                                    <h4>
                                        Akses menggunakan email dan kata sandi.
                                    </h4>
                                </div>
                                <div class="form-login">
                                    <label class="form-label">Email</label>
                                    <div class="form-addons">
                                        <TextInput
                                            name="email"
                                            type="text"
                                            placeholder="example@guritamandala.com"
                                            v-model="form.email"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errors.email ||
                                                    form.errors.email,
                                            }"
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                errors.email ||
                                                form.errors.email
                                            }}
                                        </div>
                                        <div
                                            class="emailshow text-danger"
                                            id="email"
                                        ></div>
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
                                            :type="
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            class="form-control pass-input"
                                            :class="{
                                                'is-invalid': errors.password,
                                            }"
                                        />
                                        <span
                                            @click="toggleShow"
                                            class="toggle-password"
                                        >
                                            <i
                                                :class="{
                                                    'fas fa-eye':
                                                        !errors.password &&
                                                        showPassword,
                                                    'fas fa-eye-slash':
                                                        !errors.password &&
                                                        !showPassword,
                                                }"
                                            ></i>
                                        </span>
                                        <div class="invalid-feedback">
                                            {{ errors.password }}
                                        </div>
                                        <div
                                            class="emailshow text-danger"
                                            id="password"
                                        ></div>
                                    </div>
                                </div>
                                <div class="form-login authentication-check">
                                    <div class="row">
                                        <div class="col-6">
                                            <div
                                                class="custom-control custom-checkbox"
                                            >
                                                <label
                                                    class="checkboxs ps-4 mb-0 pb-0 line-height-1"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        v-model="form.remember"
                                                    />
                                                    <span
                                                        class="checkmarks"
                                                    ></span>
                                                    ingat saya
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <Link
                                                v-if="canResetPassword"
                                                :href="
                                                    route('password.request')
                                                "
                                                class="forgot-link"
                                            >
                                                Lupa kata sandi?
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-login">
                                    <button class="btn btn-login" type="submit">
                                        Masuk
                                    </button>
                                </div>
                                <div class="signinform">
                                    <h4>
                                        Belum punya akun ?<Link
                                            :href="route('register')"
                                            class="hover-a"
                                        >
                                            Buat akun</Link
                                        >
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
