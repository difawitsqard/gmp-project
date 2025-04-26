<template>
    <Head title="Profile" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Profile</h4>
                    <h6>User Profile</h6>
                </div>
            </div>
            <UpdateProfileInformationForm
                :user="$page.props.auth.user"
            ></UpdateProfileInformationForm>
        </div>
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
export default {
    components: {
        UpdateProfileInformationForm,
        Head,
    },
    data() {
        return {};
    },
};
</script>
