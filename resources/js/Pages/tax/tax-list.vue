<template>
    <Head title="Tarif Pajak" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Tarif Pajak</h4>
                        <h6>Kelola Tarfif Pajak</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            @click="fetch()"
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
                <div class="page-btn">
                    <button class="btn btn-added" @click="addTax">
                        <vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather
                        >Tambah Tarif Pajak Baru
                    </button>
                </div>
            </div>

            <div class="card table-list-card">
                <div class="card-body">
                    <div class="table-top">
                        <div class="search-set">
                            <div class="search-input">
                                <input
                                    type="text"
                                    placeholder="Cari.."
                                    class="dark-input"
                                    v-model="filters.search"
                                />
                                <a
                                    href="javascript:void(0);"
                                    v-show="filters.search"
                                    class="btn btn-searchset"
                                    @click="resetExcept"
                                    ><i data-feather="x" class="feather-x"></i
                                ></a>
                            </div>
                        </div>
                        <div class="search-path">
                            <a
                                class="btn btn-filter"
                                id="filter_search"
                                v-on:click="filter = !filter"
                                :class="{ setclose: filter }"
                            >
                                <vue-feather
                                    type="filter"
                                    class="filter-icon"
                                ></vue-feather>
                                <span>
                                    <span
                                        class="d-flex justify-content-center align-items-center"
                                        style="height: 100%"
                                    >
                                        <vue-feather type="x"></vue-feather>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div
                        class="card"
                        :style="{ display: filter ? 'block' : 'none' }"
                        id="filter_inputs"
                    >
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="stop-circle"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="CategoryStatus"
                                            v-model="filters.status"
                                            id="categorystatus"
                                            placeholder="Pilih Status"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                    <div
                                        class="input-blocks d-flex justify-content-end"
                                    >
                                        <a
                                            class="btn btn-filters btn-reset"
                                            @click="resetExcept"
                                        >
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div class="table-responsive">
                        <a-table
                            class="table datanew"
                            :columns="columns"
                            :data-source="taxes.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div class="fw-bold">
                                        {{ record.name }}
                                    </div>
                                </template>
                                <template v-if="column.key === 'fixed_amount'">
                                    <div>
                                        {{
                                            record.fixed_amount != null
                                                ? $helpers.formatRupiah(
                                                      record.fixed_amount
                                                  )
                                                : "-"
                                        }}
                                    </div>
                                </template>
                                <template v-if="column.key === 'status'">
                                    <div>
                                        <span
                                            :class="
                                                record.status
                                                    ? 'badge badge-linesuccess'
                                                    : 'badge badge-linedanger'
                                            "
                                        >
                                            {{
                                                record.status
                                                    ? "Akitf"
                                                    : "Tidak Aktif"
                                            }}</span
                                        >
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <a
                                                class="me-2 p-2"
                                                @click="editTax(record.id)"
                                            >
                                                <i
                                                    data-feather="edit"
                                                    class="feather-edit"
                                                ></i>
                                            </a>
                                            <a
                                                class="confirm-text p-2"
                                                @click="
                                                    showConfirmation(record.id)
                                                "
                                            >
                                                <i
                                                    data-feather="trash-2"
                                                    class="feather-trash-2"
                                                ></i>
                                            </a>
                                        </div>
                                    </td>
                                </template>
                            </template>
                        </a-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <tax-modal
        ref="taxModal"
        :is-edit="modalTax.isEdit"
        :model="{
            id: modalTax.id,
            name: modalTax.name,
            percent: modalTax.percent,
            fixed_amount: modalTax.fixed_amount,
            status: modalTax.status,
        }"
        modal-id="tax-modal"
        @tax-submit="handleTaxSubmit"
    />
</template>
<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import TaxModal from "@/components/modal/taxx-modal.vue";

export default {
    name: "TaxRates",
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "taxes.index",
            ["search", "status", "created"],
            { only: ["taxes"] }
        );

        const resetExcept = () => {
            reset(["sort_order", "sort_field"]);
        };

        return { filters, fetch, reset, resetExcept };
    },
    components: {
        Head,
        Link,
        TaxModal,
    },
    props: {
        taxes: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: false,
            CategoryStatus: [
                { id: null, text: "Pilih Status" },
                { id: 1, text: "Aktif" },
                { id: 0, text: "Tidak Aktif" },
            ],
            modalTax: {
                isEdit: false,
                id: "",
                name: "",
                percent: "",
                fixed_amount: "",
                status: true,
            },
            columns: [
                {
                    title: "Name",
                    dataIndex: "name",
                    key: "name",
                    sorter: true,
                },
                {
                    title: "Tax Rate %",
                    dataIndex: "percent",
                    sorter: true,
                    customRender: ({ text }) => {
                        // Tampilkan nilai float apa adanya, misal 2.5 jadi 2.5%
                        const percent = parseFloat(text);
                        return isNaN(percent) ? "-" : percent + "%";
                    },
                },
                {
                    title: "Jumlah Tetap (Rp)",
                    dataIndex: "fixed_amount",
                    key: "fixed_amount",
                    sorter: true,
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: false,
                },
                {
                    title: "Dibuat",
                    dataIndex: "created_at",
                    key: "created_at",
                    sorter: true,
                    customRender: ({ text }) =>
                        dayjs(text).format("D MMMM YYYY HH:mm"),
                },
                {
                    title: "Aksi",
                    key: "action",
                    sorter: false,
                },
            ],
        };
    },
    computed: {
        pagination() {
            return {
                current: this.taxes.current_page,
                pageSize: this.taxes.per_page,
                total: this.taxes.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    methods: {
        handleTableChange(pagination, filters, sorter) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;

            // Handle sorting
            if (sorter.order) {
                this.filters.sort_field = sorter.field;
                this.filters.sort_order = sorter.order;
            } else {
                // Reset sorting jika header diklik untuk ketiga kalinya (hapus sort)
                this.filters.sort_field = null;
                this.filters.sort_order = null;
            }
            this.fetch();
        },

        addTax() {
            this.modalTax.isEdit = false;
            this.modalTax.id = "";
            this.modalTax.name = "";
            this.modalTax.percent = "";
            this.modalTax.fixed_amount = "";
            this.modalTax.status = true;
            this.$refs.taxModal.showModal();
        },

        editTax(id) {
            const tax = this.taxes.data.find((tax) => tax.id === id);
            if (tax) {
                this.modalTax.isEdit = true;
                this.modalTax.id = tax.id;
                this.modalTax.name = tax.name;
                this.modalTax.percent = tax.percent;
                this.modalTax.fixed_amount = tax.fixed_amount;
                this.modalTax.status = tax.status;
                this.$refs.taxModal.showModal();
            }
        },

        handleTaxSubmit(response) {
            console.log(response);
            if (response.status) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: response.message,
                });

                this.fetch();
            }
        },

        showConfirmation(id) {
            Swal.fire({
                title: "Apa kamu yakin ?",
                text: "Tindakan ini bersifat permanen dan tidak dapat dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.deleteData(id);
                }
            });
        },

        deleteData(id) {
            router.delete(route("taxes.destroy", { id }), {
                onSuccess: () => {
                    const flash = usePage().props.flash;
                    if (flash.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: flash.success,
                        });
                    }
                },
                onError: () => {
                    const errors = usePage().props.errors;
                    if (errors && Object.keys(errors).length > 0) {
                        const firstError = Object.values(errors)[0];
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: firstError,
                        });
                    }
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
