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
                        <div class="dash-widget dash2 w-100">
                            <div class="dash-widgetimg">
                                <span>
                                    <vue-feather
                                        type="package"
                                        class="text-primary"
                                    />
                                </span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    <vue3-autocounter
                                        class="counters"
                                        ref="counter"
                                        :startAmount="0"
                                        :delay="5"
                                        :endAmount="
                                            $page.props.dashboardData
                                                .totalProducts
                                        "
                                        :duration="5"
                                        :autoinit="true"
                                    />
                                </h5>
                                <h6>Total Produk</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-widget dash w-100">
                            <div class="dash-widgetimg">
                                <span>
                                    <vue-feather
                                        type="trending-down"
                                        class="text-warning"
                                    />
                                </span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    <vue3-autocounter
                                        class="counters"
                                        ref="counter"
                                        :startAmount="50"
                                        :delay="4"
                                        :endAmount="
                                            $page.props.dashboardData
                                                .lowStockProducts
                                        "
                                        :duration="3"
                                        :autoinit="true"
                                    />
                                </h5>
                                <h6>Stok Rendah</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-widget w-100">
                            <div class="dash-widgetimg">
                                <span>
                                    <vue-feather
                                        type="alert-triangle"
                                        class="text-danger"
                                    />
                                </span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    <vue3-autocounter
                                        class="counters"
                                        ref="counter"
                                        :startAmount="50"
                                        :delay="4"
                                        :endAmount="
                                            $page.props.dashboardData
                                                .outOfStockProducts
                                        "
                                        :duration="3"
                                        :autoinit="true"
                                    />
                                </h5>
                                <h6>Produk Habis</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="dash-widget dash1 w-100">
                            <div class="dash-widgetimg">
                                <span>
                                    <vue-feather
                                        type="shopping-cart"
                                        class="text-success"
                                    />
                                </span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    <vue3-autocounter
                                        class="counters"
                                        ref="counter"
                                        :startAmount="50"
                                        :delay="3"
                                        :endAmount="
                                            $page.props.dashboardData
                                                .waitingProcessingOrders
                                        "
                                        :duration="3"
                                        :autoinit="true"
                                    />
                                </h5>
                                <h6>Pesanan Masuk</h6>
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
