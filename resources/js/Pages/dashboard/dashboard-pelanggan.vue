<template>
    <Head title="Dasbor" />

    <div>
        <layout-header></layout-header>
        <layout-sidebar></layout-sidebar>

        <div class="page-wrapper">
            <div class="content">
                <div
                    class="welcome d-lg-flex align-items-center justify-content-between"
                >
                    <div class="d-flex align-items-center welcome-text">
                        <h4 class="d-flex align-items-center">
                            Hai, Selamat datang {{ $page.props.auth.user.name }}
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count">
                            <div class="dash-counts">
                                <h4>
                                    {{ $page.props.dashboardData.totalOrders }}
                                </h4>
                                <h5>Total Pesanan</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="shopping-cart"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData.pendingPayment
                                    }}
                                </h4>
                                <h5>Menunggu Pembayaran</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="clock"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das2">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData
                                            .pendingConfirmation
                                    }}
                                </h4>
                                <h5>Menunggu Konfirmasi</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="alert-circle"></vue-feather>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>
                                    {{
                                        $page.props.dashboardData
                                            .completedOrders
                                    }}
                                </h4>
                                <h5>Pesanan Selesai</h5>
                            </div>
                            <div class="dash-imgs">
                                <vue-feather type="check-circle"></vue-feather>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        Head: Head,
    },
};
</script>
