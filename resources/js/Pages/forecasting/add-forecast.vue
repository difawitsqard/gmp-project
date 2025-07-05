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
                    <form @submit.prevent="submitForm">
                        <div class="card">
                            <div class="card-body add-product">
                                <div
                                    class="accordion-card-one accordion"
                                    id="accordionForecasting"
                                >
                                    <div class="accordion-item">
                                        <div
                                            class="accordion-header"
                                            id="headingOne"
                                        >
                                            <div
                                                class="accordion-button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne"
                                                aria-expanded="true"
                                                aria-controls="collapseOne"
                                            >
                                                <div class="addproduct-icon">
                                                    <h5>
                                                        <vue-feather
                                                            type="info"
                                                            class="add-info"
                                                        ></vue-feather
                                                        ><span>Data Input</span>
                                                    </h5>
                                                    <a
                                                        href="javascript:void(0);"
                                                        ><vue-feather
                                                            type="chevron-down"
                                                            class="chevron-down-add"
                                                        ></vue-feather
                                                    ></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            id="collapseOne"
                                            class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne"
                                        >
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div
                                                        class="col-lg-4 col-12"
                                                    >
                                                        <div
                                                            class="mb-3 add-product"
                                                        >
                                                            <label
                                                                class="form-label"
                                                                >Frekuensi</label
                                                            >
                                                            <vue-select
                                                                v-model="
                                                                    form.frequency
                                                                "
                                                                :options="
                                                                    ChooseFrequency
                                                                "
                                                                id="choosefrequency"
                                                                placeholder="Pilih Frekuensi"
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.frequency,
                                                                }"
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.frequency
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.frequency
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-12"
                                                    >
                                                        <div
                                                            class="mb-3 add-product"
                                                        >
                                                            <label
                                                                class="form-label"
                                                                >Tanggal
                                                                Mulai</label
                                                            >
                                                            <date-picker
                                                                placeholder="Pilih Tanggal Mulai"
                                                                class="form-control"
                                                                v-model="
                                                                    form.startDate
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.startDate,
                                                                }"
                                                                :lower-limit="
                                                                    minDate
                                                                "
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.startDate
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.startDate
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-12"
                                                    >
                                                        <div
                                                            class="mb-3 add-product"
                                                        >
                                                            <label
                                                                class="form-label"
                                                                >Tanggal
                                                                Selesai</label
                                                            >
                                                            <date-picker
                                                                placeholder="Pilih Tanggal Selesai"
                                                                class="form-control"
                                                                v-model="
                                                                    form.endDate
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.endDate,
                                                                }"
                                                                :lower-limit="
                                                                    form.startDate
                                                                "
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.endDate
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.endDate
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <div>
                                                            <label
                                                                class="form-label"
                                                                >Produk</label
                                                            >
                                                            <AsyncProductSelect
                                                                v-model="
                                                                    form.products
                                                                "
                                                                :frequency="
                                                                    form.frequency
                                                                "
                                                                :start-date="
                                                                    form.startDate
                                                                "
                                                                :end-date="
                                                                    form.endDate
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.products,
                                                                }"
                                                            />
                                                            <div
                                                                v-if="
                                                                    errors.products
                                                                "
                                                                class="invalid-feedback"
                                                            >
                                                                {{
                                                                    errors.products
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-2">
                                        <div
                                            class="accordion-header"
                                            id="headingTwo"
                                        >
                                            <div
                                                class="accordion-button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseTwo"
                                                aria-expanded="true"
                                                aria-controls="collapseTwo"
                                            >
                                                <div class="addproduct-icon">
                                                    <h5>
                                                        <vue-feather
                                                            type="info"
                                                            class="add-info"
                                                        ></vue-feather
                                                        ><span>Prediksi</span>
                                                    </h5>
                                                    <a
                                                        href="javascript:void(0);"
                                                        ><vue-feather
                                                            type="chevron-down"
                                                            class="chevron-down-add"
                                                        ></vue-feather
                                                    ></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            id="collapseTwo"
                                            class="accordion-collapse collapse show"
                                            aria-labelledby="headingTwo"
                                        >
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div
                                                        class="col-lg-4 col-12 mb-3"
                                                    >
                                                        <label
                                                            class="form-label"
                                                            >Horizon</label
                                                        >
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            v-model="
                                                                form.horizon
                                                            "
                                                            placeholder="Horizon"
                                                            min="1"
                                                            max="12"
                                                            :class="{
                                                                'is-invalid':
                                                                    errors.horizon,
                                                            }"
                                                        />
                                                        <div
                                                            v-if="
                                                                errors.horizon
                                                            "
                                                            class="invalid-feedback"
                                                        >
                                                            {{ errors.horizon }}
                                                        </div>
                                                        <small
                                                            class="form-text text-muted"
                                                        >
                                                            Masukkan nilai
                                                            antara 1-12
                                                        </small>
                                                    </div>
                                                    <div
                                                        class="col-lg-4 col-12 mb-3"
                                                    >
                                                        <label
                                                            class="form-label"
                                                            >Opsi Lain</label
                                                        >
                                                        <div
                                                            class="form-check form-check-md d-flex align-items-center"
                                                        >
                                                            <input
                                                                v-model="
                                                                    form.enforceDataQuality
                                                                "
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                id="checkebox-md"
                                                                checked
                                                            />
                                                            <label
                                                                class="form-check-label"
                                                                for="checkebox-md"
                                                            >
                                                                Cegah proses
                                                                jika data
                                                                dianggap tidak
                                                                layak
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button
                                        type="submtit"
                                        class="btn btn-submit me-2"
                                    >
                                        Buat Prediksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    props: {
        initialData: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        const minDate = this.initialData?.start_date
            ? new Date(this.initialData.start_date)
            : new Date(new Date().setFullYear(new Date().getFullYear() - 1));

        return {
            errors: {},
            form: {
                products: [],
                frequency: "D",
                horizon: 1,
                startDate: minDate,
                endDate: new Date(),
                enforceDataQuality: true,
            },
            ChooseFrequency: [
                { id: "D", text: "Harian" },
                { id: "W", text: "Mingguan" },
                { id: "M", text: "Bulanan" },
            ],
            minDate: minDate,
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

                if (totalPoints < 13) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: `Produk "${found?.name}" hanya memiliki ${totalPoints} titik data ${frequencyKey}, minimal 13 diperlukan.`,
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

                    if (totalPoints < 13) {
                        Swal.fire({
                            icon: "error",
                            title: "Produk Tidak Valid",
                            text: `Produk "${product.name}" hanya memiliki ${totalPoints} titik data ${frequencyKey}, minimal 13 diperlukan.`,
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
