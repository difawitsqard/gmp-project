<template>
    <Head title="Satuan" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Satuan</h4>
                        <h6>Kelola Satuan</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Refresh"
                            @click="this.fetch()"
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
                    <a
                        href="javascript:void(0);"
                        class="btn btn-added"
                        @click="addUnit"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather>
                        Tambah Satuan Baru</a
                    >
                </div>
            </div>
            <!-- /product list -->
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
                                v-on:click="
                                    filter = !filter;
                                    if (!filter) resetExcept();
                                "
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
                                            type="calendar"
                                            class="info-img"
                                        ></vue-feather>
                                        <div class="input-groupicon">
                                            <date-picker
                                                v-model="startdate"
                                                placeholder="Choose Date"
                                                class="form-control"
                                                :editable="true"
                                                :clearable="false"
                                                :input-format="dateFormat"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12 mb-3">
                                    <vue-select
                                        :options="UnitStatus"
                                        v-model="filters.status"
                                        id="unitstatus"
                                        placeholder="Filter Status"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->
                    <div class="table-responsive">
                        <a-table
                            class="table datanew table-hover table-center mb-0"
                            :columns="columns"
                            :data-source="units.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
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
                                                    ? "Aktif"
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
                                                @click="editUnit(record.id)"
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
            <!-- /product list -->
        </div>
    </div>
    <units-modal
        ref="unitsModal"
        :is-edit="modalUnit.isEdit"
        :model="{
            id: modalUnit.id,
            name: modalUnit.name,
            short_name: modalUnit.short_name,
            status: modalUnit.status,
        }"
        modal-id="units-modal"
        @units-submit="handleUnitSubmit"
    />
</template>
<script>
import { ref } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import UnitsModal from "@/components/modal/units-modal.vue";

const currentDate = ref(new Date());
export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "units.index",
            ["search", "status", "created"],
            { only: ["units"] }
        );

        const resetExcept = () => {
            reset(["sort_order", "sort_field"]);
        };

        return { filters, fetch, reset, resetExcept };
    },
    components: {
        Head,
        Link,
        UnitsModal,
    },
    props: {
        units: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            filter: false,
            startdate: currentDate,
            dateFormat: "dd-MM-yyyy",
            search: this.filters?.search || "",
            UnitsSort: ["Sort by Date", "Newest", "Oldest"],
            UnitStatus: [
                { value: 1, label: "Aktif" },
                { value: 0, label: "Tidak Aktif" },
            ],
            modalUnit: {
                isEdit: false,
                id: null,
                name: null,
                short_name: null,
                status: true,
            },
            columns: [
                {
                    title: "Satuan",
                    dataIndex: "name",
                    key: "name",
                    sorter: true,
                },
                {
                    title: "Nama Pendek",
                    dataIndex: "short_name",
                    sorter: true,
                },
                {
                    title: "Jumlah Produk",
                    dataIndex: "items",
                    sorter: true,
                },
                {
                    title: "Dibuat Pada",
                    dataIndex: "created_at",
                    sorter: true,
                    customRender: ({ text }) =>
                        this.$helpers.formatDate(text, "DD MMMM YYYY HH:mm"),
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: false,
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
                current: this.units.current_page,
                pageSize: this.units.per_page,
                total: this.units.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    mounted() {
        this.data = this.units;
    },
    methods: {
        handleTableChange(pagination, filters, sorter) {
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;

            if (sorter.order) {
                this.filters.sort_field = sorter.field;
                this.filters.sort_order = sorter.order;
            } else {
                this.filters.sort_field = null;
                this.filters.sort_order = null;
            }

            this.fetch();
        },

        handleUnitSubmit(response) {
            if (response.status) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: response.message,
                });

                this.fetch();
            }
        },

        addUnit() {
            this.modalUnit.isEdit = false;
            this.modalUnit.id = null;
            this.modalUnit.name = null;
            this.modalUnit.short_name = null;
            this.modalUnit.status = true;

            this.$refs.unitsModal.showModal();
        },

        editUnit(id) {
            const unit = this.units.data.find((unit) => unit.id === id);

            if (!unit) {
                return;
            }
            this.modalUnit.isEdit = true;
            this.modalUnit.id = unit.id;
            this.modalUnit.name = unit.name;
            this.modalUnit.short_name = unit.short_name;
            this.modalUnit.status = unit.status;

            this.$refs.unitsModal.showModal();
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
            router.delete(route("units.destroy", { id }), {
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
