<template>
    <Head title="Dasbor Admin" />

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
                    <div class="col-xl-4 col-sm-6 col-12 d-flex">
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
                    <div class="col-xl-4 col-sm-6 col-12 d-flex">
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
                    <div class="col-xl-4 col-sm-6 col-12 d-flex">
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
                    <div class="col-sm-12 col-md-12 col-xl-4 d-flex">
                        <div class="card flex-fill default-cover w-100 mb-4">
                            <div
                                class="card-header d-flex justify-content-between align-items-center"
                            >
                                <h4 class="card-title mb-0">Produk Terlaris</h4>
                                <div class="dropdown">
                                    <Link
                                        :href="
                                            route('products.index', {
                                                sort: 'bestseller',
                                            })
                                        "
                                        class="view-all d-flex align-items-center"
                                    >
                                        Lihat Semua<span
                                            class="ps-2 d-flex align-items-center"
                                            ><vue-feather
                                                type="arrow-right"
                                                class="feather-16"
                                            ></vue-feather
                                        ></span>
                                    </Link>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        class="table table-borderless best-seller"
                                    >
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produk</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(item, index) in $page
                                                    .props.dashboardData
                                                    .topProducts"
                                                :key="item.id"
                                            >
                                                <td>{{ index + 1 }}</td>
                                                <td class="productimgname">
                                                    <div class="product-img">
                                                        <img
                                                            v-lazy="
                                                                item.image_url
                                                            "
                                                            class="me-2 rounded"
                                                            alt="product"
                                                        />
                                                    </div>
                                                    <div>
                                                        {{ item.name }}
                                                        <div
                                                            class="text-muted"
                                                            style="
                                                                font-size: 0.9em;
                                                            "
                                                        >
                                                            Terjual
                                                            {{
                                                                item.total_sold
                                                            }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12 col-xl-8 d-flex">
                        <div class="card flex-fill default-cover w-100 mb-4">
                            <div
                                class="card-header d-flex justify-content-between align-items-center"
                            >
                                <h4 class="card-title mb-0">
                                    Riwayat Prediksi
                                </h4>
                                <div class="dropdown">
                                    <Link
                                        :href="
                                            route('forecasting.index', {
                                                status: 'done',
                                            })
                                        "
                                        class="view-all d-flex align-items-center"
                                    >
                                        Lihat Semua<span
                                            class="ps-2 d-flex align-items-center"
                                            ><vue-feather
                                                type="arrow-right"
                                                class="feather-16"
                                            ></vue-feather
                                        ></span>
                                    </Link>
                                </div>
                            </div>
                            <div class="card-body">
                                <table
                                    class="table table-borderless recent-transactions"
                                >
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Prediksi</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(item, index) in $page.props
                                                .dashboardData.recentForecasts"
                                            :key="item.id"
                                        >
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                <Link
                                                    :href="
                                                        route(
                                                            'forecasting.show',
                                                            item.id
                                                        )
                                                    "
                                                    class="fw-bold"
                                                    >{{
                                                        item.name ?? "-"
                                                    }}</Link
                                                >
                                                <div
                                                    class="text-muted"
                                                    style="font-size: 0.9em"
                                                >
                                                    <strong>Oleh</strong>
                                                    {{
                                                        item.created_by?.name ||
                                                        "-"
                                                    }}
                                                </div>
                                            </td>
                                            <td>
                                                {{
                                                    item.analysed_products_count
                                                        ? item.analysed_products_count +
                                                          " Produk"
                                                        : "-"
                                                }}
                                            </td>
                                            <td>
                                                {{
                                                    this.$helpers.formatDate(
                                                        item.created_at,
                                                        "DD MMMM YYYY HH:mm"
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="
                                                !$page.props.dashboardData
                                                    .recentForecasts.length
                                            "
                                        >
                                            <td
                                                colspan="6"
                                                class="text-center text-muted"
                                            >
                                                Belum ada riwayat prediksi
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";

export default {
    components: {
        Head: Head,
        Link: Link,
    },
};
</script>
