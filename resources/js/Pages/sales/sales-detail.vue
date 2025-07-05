<template>
    <Head title="Order List" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>

    <div class="page-wrapper sales-list">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Detail Pesanan</h4>
                        <h6 class="fw-bold">#{{ order.id }}</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            ref="collapseHeader"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Collapse"
                            @click="toggleCollapse"
                        >
                            <i
                                data-feather="chevron-up"
                                class="feather-chevron-up"
                            ></i>
                        </a>
                    </li>
                </ul>
                <div class="page-btn" v-show="order.canbe_cancelled">
                    <button
                        type="button"
                        class="btn btn-added btn-danger"
                        @click="cancelOrder"
                    >
                        <vue-feather type="x" class="me-2" />Batalkan Pesanan
                    </button>
                </div>
                <div
                    class="page-btn"
                    v-show="order.canbe_change_payment_method"
                >
                    <button
                        type="button"
                        class="btn btn-added btn-secondary"
                        @click="changePaymentMethod"
                    >
                        <vue-feather type="repeat" class="me-2" />
                        Ubah Metode Pembayaran
                    </button>
                </div>
                <div class="page-btn">
                    <Link
                        :href="route('orders.index')"
                        class="btn btn-added btn-secondary"
                        ><vue-feather
                            type="arrow-left"
                            class="me-2"
                        ></vue-feather
                        >Riwayat Pesanan</Link
                    >
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <div class="row mb-3">
                            <div
                                class="col-12 col-sm-6 col-md-4 col-xl-4 pb-3 border-bottom mb-3"
                            >
                                <h4 class="mb-3">Detail Pesanan</h4>
                                <div class="row">
                                    <span class="col-6 mb-1">ID Pesanan</span>
                                    <span class="col-6 fw-bold mb-1">
                                        #{{ order.id }}
                                    </span>
                                    <span class="col-6 mb-1"
                                        >Status Pesanan</span
                                    >
                                    <span class="col-6 mb-1">
                                        <span
                                            class="badge"
                                            :class="
                                                'bg-outline-' +
                                                getOrderStatus(order.status)
                                                    .color
                                            "
                                        >
                                            {{
                                                getOrderStatus(order.status)
                                                    .label
                                            }}
                                        </span>
                                    </span>
                                    <span class="col-6 mb-1"
                                        >Metode Pengiriman</span
                                    >
                                    <span class="col-6 mb-1">
                                        {{
                                            order.shipping_method
                                                ? order.shipping_method
                                                : "Belum dipilih"
                                        }}
                                    </span>

                                    <span class="col-6 mb-1"
                                        >Tanggal Pesanan</span
                                    >
                                    <span class="col-6 mb-1">
                                        {{
                                            dayjs(order.created_at).format(
                                                "D MMMM YYYY HH:mm"
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div
                                class="col-12 col-sm-6 col-md-4 col-xl-4 pb-3 border-bottom mb-3"
                            >
                                <h4 class="mb-3">Detail Pembayaran</h4>
                                <div class="row">
                                    <span class="col-6 mb-1"
                                        >Metode Pembayaran</span
                                    >
                                    <span class="col-6 mb-1">
                                        {{
                                            order.latest_payment.payment_type
                                                ? getPaymentInfo(
                                                      order.latest_payment
                                                          .payment_type
                                                  ).label
                                                : "Belum dipilih"
                                        }}
                                    </span>
                                    <!-- <span class="col-6 mb-1"
                                        >Status Pembayaran</span
                                    >
                                    <span class="col-6 mb-1">
                                        <span
                                            class="badge"
                                            :class="
                                                'bg-outline-' +
                                                getPaymentStatus(
                                                    order.latest_payment.status
                                                ).color
                                            "
                                        >
                                            {{
                                                getPaymentStatus(
                                                    order.latest_payment.status
                                                ).label
                                            }}
                                        </span>
                                    </span> -->
                                    <template
                                        v-if="order.latest_payment.paid_at"
                                    >
                                        <span class="col-6 mb-1"
                                            >Tanggal Pembayaran
                                        </span>
                                        <span class="col-6 mb-1">
                                            {{
                                                order.latest_payment.paid_at
                                                    ? dayjs(
                                                          order.latest_payment
                                                              .paid_at
                                                      ).format(
                                                          "D MMMM YYYY HH:mm"
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </template>
                                    <template
                                        v-else-if="
                                            order.latest_payment.expired_at &&
                                            order.latest_payment.payment_type
                                        "
                                    >
                                        <span class="col-6 mb-1"
                                            >Bayar Sebelum</span
                                        >
                                        <span class="col-6 mb-1">
                                            {{
                                                order.latest_payment.expired_at
                                                    ? dayjs(
                                                          order.latest_payment
                                                              .expired_at
                                                      ).format(
                                                          "D MMMM YYYY HH:mm"
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="col-12 col-sm-12 col-md-4 col-xl-4 pb-3 border-bottom mb-3"
                            >
                                <h4 class="mb-3">Detail Pelanggan</h4>
                                <address>
                                    <strong>{{ order.name }}</strong
                                    ><br />
                                    7657 NW Prairie View Rd<br />
                                    Kansas City, Mississippi, 64151<br />
                                    United States<br />
                                    Phone: (816) 741-5790<br />
                                    Email: email@client.com
                                </address>
                            </div>
                            <div class="col-12">
                                <h4 class="mb-3">Ringkasan Pesanan</h4>
                                <div class="table-responsive no-pagination">
                                    <table class="table datanew">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Qty</th>
                                                <th>Harga</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in order.items"
                                                :key="index"
                                            >
                                                <td>
                                                    <div class="productimgname">
                                                        <a
                                                            href="javascript:void(0);"
                                                            class="product-img stock-img"
                                                        >
                                                            <img
                                                                v-lazy=""
                                                                alt="product"
                                                            />
                                                        </a>
                                                        <a
                                                            href="javascript:void(0);"
                                                            >{{
                                                                item.product
                                                                    .name
                                                            }}</a
                                                        >
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ item.quantity }}
                                                </td>
                                                <td>
                                                    {{
                                                        $helpers.formatRupiah(
                                                            item.price
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        $helpers.formatRupiah(
                                                            item.subtotal
                                                        )
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 ms-auto">
                                    <div
                                        class="total-order w-100 max-widthauto m-auto mb-4"
                                    >
                                        <ul>
                                            <li>
                                                <h4>Subtotal</h4>
                                                <h5>
                                                    {{
                                                        $helpers.formatRupiah(
                                                            order.sub_total
                                                        )
                                                    }}
                                                </h5>
                                            </li>
                                            <template
                                                v-for="tax in order.taxes"
                                                :key="tax.id"
                                            >
                                                <li>
                                                    <h4>
                                                        {{ tax.name }}
                                                        <template
                                                            v-if="tax.percent"
                                                        >
                                                            ({{ tax.percent }}%)
                                                        </template>
                                                    </h4>
                                                    <h5>
                                                        {{
                                                            $helpers.formatRupiah(
                                                                tax.pivot.amount
                                                            )
                                                        }}
                                                    </h5>
                                                </li>
                                            </template>
                                            <li
                                                v-show="
                                                    order.shipping_method ===
                                                    'delivery'
                                                "
                                            >
                                                <h4 v-show="order.shipping_fee">
                                                    Biaya Pengiriman
                                                </h4>
                                                <h5>
                                                    {{
                                                        $helpers.formatRupiah(
                                                            order.shipping_fee ??
                                                                0
                                                        )
                                                    }}
                                                </h5>
                                            </li>
                                            <li>
                                                <h4>Total</h4>
                                                <h5>
                                                    {{
                                                        $helpers.formatRupiah(
                                                            order.total
                                                        )
                                                    }}
                                                </h5>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top py-3 d-flex justify-content-end">
                            <button
                                v-show="
                                    order.latest_payment.status === 'pending'
                                "
                                @click="showSnap"
                                type="button"
                                class="btn btn-primary rounded-8 me-2"
                            >
                                Lanjutkan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import { getOrderStatus } from "@/constants/orderStatus";
import { getPaymentStatus } from "@/constants/paymentStatus";
import { getPaymentInfo } from "@/constants/paymentType";
import useMidtransPayment from "@/composables/useMidtransPayment";
import { skipPreloadNextRequest } from "@/composables/usePreloadControl";
const { payWithMidtrans, closeSnap } = useMidtransPayment();

export default {
    name: "SalesDetail",
    props: {
        order: {
            required: true,
            type: Object,
        },
        midtrans_snap_token: {
            type: String,
            default: null,
        },
        midtrans_client_key: {
            type: String,
            default: null,
        },
        midtrans_show_snap: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        Head,
        Link,
    },
    mounted() {
        // Initialize Snap.js if needed
        console.log(this.order);
        if (
            this.midtrans_snap_token &&
            this.midtrans_client_key &&
            this.midtrans_show_snap
        ) {
            this.showSnap();
        }
    },
    beforeUnmount() {
        closeSnap();
    },
    methods: {
        dayjs,
        getOrderStatus,
        getPaymentStatus,
        getPaymentInfo,
        async showSnap() {
            payWithMidtrans({
                snapToken: this.midtrans_snap_token,
                clientKey: this.midtrans_client_key,
                onSuccess: () => {
                    this.refreshData();
                },
                onPending: () => {
                    this.refreshData();
                },
                onError: () => {
                    this.refreshData();
                },
                onClose: () => {
                    this.refreshData();
                },
            });
        },
        async cancelOrder() {
            const confirmed = await this.$swal.fire({
                title: "Batalkan Pesanan",
                text: "Apakah Anda yakin ingin membatalkan pesanan ini ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, batalkan!",
                cancelButtonText: "Batal",
            });
            if (confirmed.isConfirmed) {
                this.$inertia.post(
                    route("orders.cancel", this.order.id),
                    {},
                    {
                        only: ["order"],
                        onError: (result) => {
                            console.log("Error:", result);
                        },
                    }
                );
            }
        },
        async changePaymentMethod() {
            const confirmed = await this.$swal.fire({
                title: "Ubah Metode Pembayaran",
                text: "Apakah Anda yakin ingin mengubah metode pembayaran pesanan ini ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, ubah!",
                cancelButtonText: "Batal",
            });
            if (confirmed.isConfirmed) {
                this.$inertia.post(
                    route("payments.retry", this.order.id),
                    {},
                    {
                        only: [
                            "midtrans_snap_token",
                            "midtrans_client_key",
                            "midtrans_show_snap",
                        ],
                        onSuccess: () => {
                            this.showSnap();
                        },
                        onError: (result) => {
                            console.log("Error:", result);
                        },
                    }
                );
            }
        },
        refreshData() {
            skipPreloadNextRequest();
            this.$inertia.reload({
                only: [
                    "order",
                    "midtrans_snap_token",
                    "midtrans_client_key",
                    "midtrans_show_snap",
                ],
            });
        },
    },
};
</script>
