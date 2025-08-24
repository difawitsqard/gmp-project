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
                <ul class="table-top-head d-flex align-items-center">
                    <li>
                        <collapse-header-toggle />
                    </li>
                    <li>
                        <Link
                            :href="route('forecasting.index')"
                            class="btn btn-secondary"
                            ><vue-feather
                                type="arrow-left"
                                class="me-2"
                            ></vue-feather
                            >Riwayat Prediksi</Link
                        >
                    </li>
                </ul>
            </div>

            <ValidationExplanationCard />

            <div class="row">
                <div class="col-12">
                    <form @submit.prevent="submitForm">
                        <div class="card">
                            <div class="card-body add-product">
                                <div
                                    class="alert alert-outline-info border border-info py-3"
                                >
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            <vue-feather
                                                type="info"
                                                class="flex-shrink-0 me-2"
                                                style="
                                                    width: 30px;
                                                    height: 30px;
                                                "
                                            ></vue-feather>
                                        </div>
                                        <div class="w-100">
                                            <div class="fs-12 op-8 mb-1">
                                                <ul>
                                                    <li>
                                                        <strong
                                                            >Frekuensi</strong
                                                        >
                                                        menentukan interval data
                                                        yang digunakan untuk
                                                        analisis, misalnya
                                                        harian, mingguan, atau
                                                        bulanan.
                                                    </li>
                                                    <li>
                                                        <strong>Horison</strong>
                                                        adalah jumlah periode ke
                                                        depan yang akan
                                                        diprediksi. Misalnya,
                                                        jika horison diatur ke 3
                                                        dan frekuensi adalah
                                                        bulanan, maka prediksi
                                                        akan dibuat untuk 3
                                                        bulan ke depan.
                                                    </li>
                                                </ul>
                                                <div class="mt-2 text-dark">
                                                    <strong>Catatan</strong>
                                                    Hasil prediksi digunakan
                                                    sebagai alat ukur atau
                                                    referensi, dan tidak
                                                    dijadikan acuan 100%. Selalu
                                                    lakukan pengecekan dan
                                                    pertimbangan tambahan
                                                    sebelum mengambil keputusan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Nama</label
                                    ><input
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g Prediksi Stok Bulan Ini"
                                        :class="{ 'is-invalid': errors.name }"
                                        v-model="form.name"
                                    />
                                    <div
                                        v-if="errors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ errors.name }}
                                    </div>
                                </div>

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
                                                            type="database"
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
                                                                v-model="
                                                                    form.startDate
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.startDate,
                                                                }"
                                                                :max-date="
                                                                    maxDate
                                                                "
                                                                :enable-time-picker="
                                                                    false
                                                                "
                                                                :min-date="
                                                                    form.startDate
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
                                                                v-model="
                                                                    form.endDate
                                                                "
                                                                :class="{
                                                                    'is-invalid':
                                                                        errors.endDate,
                                                                }"
                                                                :max-date="
                                                                    maxDate
                                                                "
                                                                :enable-time-picker="
                                                                    false
                                                                "
                                                                :min-date="
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
                                                            <AsyncProductForecastSelect
                                                                v-model="
                                                                    form.products
                                                                "
                                                                :frequency="
                                                                    form.frequency
                                                                "
                                                                v-model:start-date="
                                                                    form.startDate
                                                                "
                                                                v-model:end-date="
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
                                                            type="activity"
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
                                                            >Horison</label
                                                        >
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            v-model="
                                                                form.horizon
                                                            "
                                                            placeholder="Horison"
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
import AsyncProductForecastSelect from "@/components/AsyncProductForecastSelect.vue";
import { Head, Link } from "@inertiajs/vue3";
import { getAllForecastFrequencies } from "@/constants/forecastFrequency";
import ValidationExplanationCard from "./ValidationExplanationCard.vue";

export default {
    components: {
        AsyncProductForecastSelect,
        Head,
        ValidationExplanationCard,
        Link,
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

        // set end date
        const endDate = this.initialData?.end_date
            ? new Date(this.initialData.end_date)
            : new Date();

        const now = new Date();
        const defaultName = `Prediksi ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;

        return {
            errors: {},
            form: {
                name: defaultName,
                products: [],
                frequency: "D",
                horizon: 1,
                startDate: minDate,
                endDate: endDate,
                enforceDataQuality: true,
            },
            ChooseFrequency: getAllForecastFrequencies(),
            minDate: minDate,
            maxDate: now,
        };
    },
    methods: {
        submitForm() {
            this.$inertia.post(this.route("forecasting.request"), this.form, {
                onSuccess: () => {
                    console.log("Form submitted successfully!");
                },
                onError: (errors) => {
                    this.errors = errors;
                },
            });
        },
    },
};
</script>
