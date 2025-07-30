<script setup>
import * as Yup from "yup";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import TextInput from "@/components/TextInput.vue";

defineProps({
    status: String,
});

const schema = Yup.object().shape({
    email: Yup.string()
        .required("Email wajib diisi")
        .email("Format email salah"),
});

const form = useForm({
    email: "",
});

const errors = ref({});

const submit = async () => {
    errors.value = {};
    try {
        await schema.validate(form, { abortEarly: false });
        form.post(route("password.email"), {
            onError: (err) => {
                errors.value.email = err.email;
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
    <Head title="Lupa Password" />
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
                                    <h3>Lupa Password?</h3>
                                    <h4>
                                        Jika Anda lupa password, kami akan
                                        mengirimkan instruksi reset ke email
                                        Anda.
                                    </h4>
                                    <div
                                        v-if="status"
                                        class="alert alert-success mt-3"
                                    >
                                        {{ status }}
                                    </div>
                                </div>
                                <div class="form-login">
                                    <label class="form-label">Email</label>
                                    <div class="form-addons">
                                        <TextInput
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            placeholder="example@guritamandala.com"
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
                                    <button
                                        type="submit"
                                        class="btn btn-login"
                                        :disabled="form.processing"
                                    >
                                        Kirim Link Reset Password
                                    </button>
                                </div>
                                <div class="signinform text-center">
                                    <h4>
                                        Kembali ke
                                        <Link
                                            :href="route('login')"
                                            class="hover-a"
                                        >
                                            login
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
