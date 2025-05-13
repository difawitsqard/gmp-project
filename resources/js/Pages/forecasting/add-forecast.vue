<template>
    <Head title="Prediksi Stok" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper notes-page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Prediksi Stok</h4>
                        <h6>
                            Prediksi kebutuhan stok berdasarkan data historis
                        </h6>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <ul class="table-top-head">
                        <li>
                            <a
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Refresh"
                                ><i
                                    data-feather="rotate-ccw"
                                    class="feather-rotate-ccw"
                                ></i
                            ></a>
                        </li>
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
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- <div class="card-header">
                            <h4 class="card-title">Konfigurasi</h4>
                        </div> -->
                        <div class="card-body">
                            <div class="row border-bottom mb-3">
                                <div class="col-sm-3 col-12 mb-3">
                                    <label class="form-label">Frekuensi</label>
                                    <vue-select
                                        v-model="form.frequency"
                                        :options="ChooseFrequency"
                                        id="choosefrequency"
                                        placeholder="Pilih Frekuensi"
                                    />
                                </div>
                                <div class="col-sm-3 col-12 mb-3">
                                    <label class="form-label">Horizon</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model="form.horizon"
                                        placeholder="Horizon"
                                        min="1"
                                        max="12"
                                    />
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <form @submit.prevent="submit">
                                        <label class="form-label">Produk</label>
                                        <AsyncProductSelect
                                            v-model="form.products"
                                        />
                                    </form>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button
                                    type="button"
                                    class="btn btn-submit me-2"
                                    @click="submitForm"
                                >
                                    Buat Prediksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AsyncProductSelectVue from "@/components/AsyncProductSelect.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

export default {
    components: {
        AsyncProductSelect: AsyncProductSelectVue,
        Head,
    },
    data() {
        return {
            errors: {},
            form: {
                products: [],
                frequency: "D",
                horizon: 1,
            },
            ChooseFrequency: [
                { id: "D", text: "Harian" },
                { id: "W", text: "Mingguan" },
                { id: "M", text: "Bulanan" },
            ],
        };
    },
    watch: {
        "form.products": {
            handler(newVal) {
                const lastAdded = newVal.at(-1);
                if (!lastAdded) return;

                const found = this.form.products.find(
                    (p) => p.id === lastAdded.id
                );
                const frequencyKey =
                    this.form.frequency === "M"
                        ? "month"
                        : this.form.frequency === "W"
                        ? "week"
                        : "day";

                const totalPoints =
                    found?.timeSeriesOrderItems?.[frequencyKey] ?? 0;

                if (totalPoints < 8) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: `Produk "${found?.name}" hanya memiliki ${totalPoints} titik data ${frequencyKey}, minimal 8 diperlukan.`,
                        confirmButtonClass: "btn btn-danger",
                    });

                    // Hapus item yang baru ditambahkan
                    this.form.products = newVal.slice(0, -1);
                }
            },
            deep: true,
        },
        "form.frequency": {
            handler(newFrequency) {
                // Validasi ulang semua produk berdasarkan frekuensi baru
                this.form.products = this.form.products.filter((product) => {
                    const frequencyKey =
                        newFrequency === "M"
                            ? "month"
                            : newFrequency === "W"
                            ? "week"
                            : "day";

                    const totalPoints =
                        product?.timeSeriesOrderItems?.[frequencyKey] ?? 0;

                    if (totalPoints < 8) {
                        Swal.fire({
                            icon: "error",
                            title: "Produk Tidak Valid",
                            text: `Produk "${product.name}" hanya memiliki ${totalPoints} titik data ${frequencyKey}, minimal 8 diperlukan.`,
                            confirmButtonClass: "btn btn-danger",
                        });
                        return false; // Hapus produk dari daftar
                    }

                    return true; // Pertahankan produk dalam daftar
                });
            },
            immediate: true,
        },
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
        submitForm() {
            this.$inertia.post(this.route("forecasting.request"), this.form, {
                onSuccess: () => {
                    console.log("Form submitted successfully!");
                },
                onError: (errors) => {
                    const invalidNames = Object.entries(errors)
                        .map(([key]) => {
                            const match = key.match(/^products\.(\d+)\.id$/);
                            return match
                                ? this.form.products[parseInt(match[1])]?.name
                                : null;
                        })
                        .filter(Boolean);

                    // 2. Tawarkan untuk hapus produk yang tidak valid
                    Swal.fire({
                        icon: "warning",
                        title: "Beberapa produk tidak valid!",
                        html: `Beberapa produk tidak dapat diprediksi karena memiliki data historis (time series) yang tidak memenuhi syarat, seperti terlalu banyak nilai nol atau jarak antar transaksi yang terlalu jauh.<br><br>

                        <b>Produk berikut tidak valid:</b>
                        <ul style="text-align: left; margin-top: 10px;">
                            ${invalidNames
                                .map((name) => `<li>${name}</li>`)
                                .join("")}
                        </ul>

                        <div style="margin-top: 15px;">
                            <i>Rekomendasi:</i><br>
                            Coba ubah frekuensi analisis menjadi <b>mingguan</b> atau <b>bulanan</b> agar pola historis lebih cocok dan stabil untuk diprediksi.
                        </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: "Hapus Produk",
                        cancelButtonText: "Batal",
                        confirmButtonClass: "btn btn-danger",
                        cancelButtonClass: "btn btn-secondary ml-1",
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Hapus produk yang tidak valid
                            this.form.products = this.form.products.filter(
                                (p) => !invalidNames.includes(p.name)
                            );
                        }
                    });
                },
            });
        },
        toggleCollapse() {
            const collapseHeader = this.$refs.collapseHeader;

            if (collapseHeader) {
                collapseHeader.classList.toggle("active");
                document.body.classList.toggle("header-collapse");
            }
        },
    },
};
</script>
