<template>
    <Head title="Profil" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Profil</h4>
                    <h6>Pengaturan Akun Pengguna</h6>
                </div>
            </div>

            <!-- Informasi Profil -->
            <UpdateProfileInformationForm
                :user="$page.props.auth.user"
            ></UpdateProfileInformationForm>

            <!-- Update Password -->
            <div v-if="$page.props.jetstream.canUpdatePassword">
                <UpdatePasswordForm />
            </div>

            <!-- Two Factor Authentication -->
            <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                <TwoFactorAuthenticationForm
                    :requires-confirmation="confirmsTwoFactorAuthentication"
                />
            </div>

            <!-- Browser Sessions -->
            <LogoutOtherBrowserSessionsForm :sessions="$page.props.sessions" />

            <!-- Delete User -->
            <div v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                <DeleteUserForm />
            </div>
        </div>
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import TwoFactorAuthenticationForm from "@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue";
import LogoutOtherBrowserSessionsForm from "@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue";
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";

export default {
    components: {
        UpdateProfileInformationForm,
        UpdatePasswordForm,
        TwoFactorAuthenticationForm,
        LogoutOtherBrowserSessionsForm,
        DeleteUserForm,
        Head,
    },
    props: {
        confirmsTwoFactorAuthentication: Boolean,
        sessions: Array,
    },
    data() {
        return {};
    },
};
</script>
