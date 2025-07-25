<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: String,
    email: String,
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head title="Verifikasi Email" />

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
                        <div class="login-userset">
                            <div class="login-userheading text-center">
                                <h3>Verifikasi Email Anda</h3>
                                <h4 class="verfy-mail-content">
                                    Kami telah mengirimkan link verifikasi ke
                                    email <b>{{ props.email }}</b
                                    >. Silakan cek email Anda dan klik link
                                    verifikasi untuk melanjutkan.
                                </h4>
                            </div>
                            <div
                                v-if="verificationLinkSent"
                                class="alert alert-success text-center my-2"
                            >
                                Link verifikasi baru telah dikirim ke email
                                Anda.
                            </div>
                            <div class="signinform text-center">
                                <h4>
                                    Tidak menerima email?
                                    <a
                                        href="javascript:void(0);"
                                        class="hover-a resend"
                                        @click="submit"
                                    >
                                        Kirim Ulang Link
                                    </a>
                                </h4>
                            </div>
                        </div>
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
