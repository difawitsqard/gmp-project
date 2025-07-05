<template>
    <Head title="Dashboard" />

    <div>
        <layout-header></layout-header>
        <layout-sidebar></layout-sidebar>

        <div class="page-wrapper">
            <div class="content"></div>
        </div>
    </div>
</template>

<script>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { basicAreaChart } from "./data";
import AdminProducts from "@/assets/json/admin-products.json";
import AdminExpired from "@/assets/json/admin-expired.json";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            basicAreaChart: basicAreaChart,
            AdminProducts: AdminProducts,
            AdminExpired: AdminExpired,
            user: this.$page.props.auth.user,
        };
    },
    components: {
        Head: Head,
    },
    methods: {
        showConfirmation() {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                confirmButtonClass: "btn btn-primary",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        confirmButtonClass: "btn btn-success",
                    });
                }
            });
        },
        getImageUrl(imageName) {
            return new URL(`${imageName}`, import.meta.url).href;
        },
    },
};
</script>
