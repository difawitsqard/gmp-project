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

                <!-- Stats Cards -->
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

                <!-- Ordering Guide Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5
                                    class="card-title mb-3 d-flex align-items-center"
                                >
                                    <vue-feather
                                        type="book-open"
                                        class="me-2"
                                    ></vue-feather>
                                    Panduan Pemesanan
                                </h5>
                                <div
                                    class="alert alert-primary alert-dismissible fade show custom-alert-icon d-flex align-items-center rounded-3"
                                >
                                    <vue-feather
                                        type="info"
                                        class="flex-shrink-0 me-2"
                                    ></vue-feather>
                                    Sebelum melakukan pesanan pastikan nama,
                                    alamat, nomor telepon, dan email Anda sudah
                                    benar.
                                    <Link
                                        :href="route('profile.show')"
                                        class="ms-1 link-primary"
                                        >Lihat Profil</Link
                                    >
                                </div>
                                <!-- Step by step guide -->
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div
                                            class="card h-100 border rounded-4"
                                        >
                                            <div class="card-body text-center">
                                                <div
                                                    class="rounded-circle bg-light-primary d-flex align-items-center justify-content-center mx-auto mb-3"
                                                    style="
                                                        width: 70px;
                                                        height: 70px;
                                                    "
                                                >
                                                    <vue-feather
                                                        type="search"
                                                        class="text-primary"
                                                        size="24"
                                                    ></vue-feather>
                                                </div>
                                                <h5 class="mb-2">
                                                    Pilih Produk
                                                </h5>
                                                <p class="text-muted">
                                                    Telusuri katalog dan pilih
                                                    produk yang Anda inginkan
                                                    <br />
                                                    <Link
                                                        :href="
                                                            route(
                                                                'orders.create'
                                                            )
                                                        "
                                                        class="link-primary"
                                                    >
                                                        Lihat Katalog
                                                    </Link>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div
                                            class="card h-100 border rounded-4"
                                        >
                                            <div class="card-body text-center">
                                                <div
                                                    class="rounded-circle bg-light-success d-flex align-items-center justify-content-center mx-auto mb-3"
                                                    style="
                                                        width: 70px;
                                                        height: 70px;
                                                    "
                                                >
                                                    <vue-feather
                                                        type="shopping-cart"
                                                        class="text-success"
                                                        size="24"
                                                    ></vue-feather>
                                                </div>
                                                <h5 class="mb-2">
                                                    Tambahkan ke Keranjang
                                                </h5>
                                                <p class="text-muted">
                                                    Klik produk untuk
                                                    menambahkan ke keranjang
                                                    belanja Anda
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div
                                            class="card h-100 border rounded-4"
                                        >
                                            <div class="card-body text-center">
                                                <div
                                                    class="rounded-circle bg-light-info d-flex align-items-center justify-content-center mx-auto mb-3"
                                                    style="
                                                        width: 70px;
                                                        height: 70px;
                                                    "
                                                >
                                                    <vue-feather
                                                        type="credit-card"
                                                        class="text-info"
                                                        size="24"
                                                    ></vue-feather>
                                                </div>
                                                <h5 class="mb-2">Pembayaran</h5>
                                                <p class="text-muted">
                                                    Selesaikan pesanan dan
                                                    lakukan pembayaran melalui
                                                    metode yang tersedia
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        Head,
        Link,
    },
};
</script>

<style scoped>
.bg-light-primary {
    background-color: rgba(13, 110, 253, 0.1);
}
.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1);
}
.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1);
}
.bg-light-info {
    background-color: rgba(13, 202, 240, 0.1);
}
.text-primary {
    color: #0d6efd;
}
.text-success {
    color: #198754;
}
.text-warning {
    color: #ffc107;
}
.text-info {
    color: #0dcaf0;
}
.feather-16 {
    width: 16px;
    height: 16px;
}
</style>
