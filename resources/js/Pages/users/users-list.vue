<template>
    <Head title="Kelola Pengguna" />
    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Pengguna</h4>
                        <h6>Kelola Pengguna</h6>
                    </div>
                </div>
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
                <div class="page-btn">
                    <a
                        href="javascript:void(0);"
                        class="btn btn-added"
                        @click="addUser"
                        ><vue-feather
                            type="plus-circle"
                            class="me-2"
                        ></vue-feather
                        >Tambah Pengguna Baru</a
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
                            <div class="d-flex align-items-center">
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
                                    <span
                                        ><img
                                            src="@/assets/img/icons/closes.svg"
                                            alt="img"
                                    /></span>
                                </a>
                            </div>
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
                                            :options="UserStatus"
                                            v-model="filters.status"
                                            id="userstatus"
                                            placeholder="Pilih Status"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <vue-feather
                                            type="user"
                                            class="info-img"
                                        ></vue-feather>
                                        <vue-select
                                            :options="UserRole"
                                            id="userrole"
                                            placeholder="Pilih Role"
                                            v-model="filters.role"
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
                            :data-source="users.data"
                            :pagination="pagination"
                            @change="handleTableChange"
                        >
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.key === 'name'">
                                    <div class="userimgname">
                                        <a
                                            href="javascript:void(0);"
                                            class="userslist-img bg-img"
                                        >
                                            <img
                                                v-lazy="
                                                    record.profile_photo_url
                                                "
                                                :alt="record.name"
                                            />
                                        </a>
                                        <div>
                                            <a href="javascript:void(0);">{{
                                                record.name
                                            }}</a>
                                        </div>
                                    </div>
                                </template>
                                <template v-if="column.key === 'status'">
                                    <div>
                                        <span
                                            :class="
                                                record.is_active
                                                    ? 'badge badge-linesuccess'
                                                    : 'badge badge-linedanger'
                                            "
                                        >
                                            {{
                                                record.is_active
                                                    ? "Aktif"
                                                    : "Tidak Aktif"
                                            }}</span
                                        >
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'roles'">
                                    <span
                                        v-for="(role, index) in record.roles"
                                        :key="index"
                                        class="badge badge-pill bg-primary me-1"
                                    >
                                        {{ role }}
                                    </span>
                                </template>
                                <template v-else-if="column.key === 'Status'">
                                    <div>
                                        <span :class="record.Class">{{
                                            record.Status
                                        }}</span>
                                    </div>
                                </template>
                                <template v-else-if="column.key === 'action'">
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <a
                                                class="me-2 p-2 mb-0"
                                                @click="editUser(record)"
                                            >
                                                <i
                                                    data-feather="edit"
                                                    class="feather-edit"
                                                ></i>
                                            </a>
                                            <a
                                                class="me-2 confirm-text p-2 mb-0"
                                                href="javascript:void(0);"
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
    <Teleport to="body">
        <UserModal
            ref="userModal"
            :is-edit="!!modalUserId"
            :user-id="modalUserId"
            modal-id="user-modal"
            @user-submit="handleUserSubmit"
        />
    </Teleport>
</template>

<script>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useInertiaFiltersDynamic } from "@/composables/useInertiaFiltersDynamic";
import UserModal from "@/components/modal/user-modal.vue";

export default {
    setup() {
        const { filters, fetch, reset } = useInertiaFiltersDynamic(
            "users.index",
            ["search", "status", "role"],
            { only: ["users"] }
        );

        const resetExcept = () => {
            reset(["sort_order", "sort_field"]);
        };

        return { filters, fetch, reset, resetExcept };
    },
    components: {
        Head,
        Link,
        UserModal,
    },
    props: {
        users: {
            type: Object,
            required: true,
        },
    },
    computed: {
        pagination() {
            return {
                current: this.users.current_page,
                pageSize: this.users.per_page,
                total: this.users.total,
                showSizeChanger: false,
                showQuickJumper: false,
                showLessItems: true,
            };
        },
    },
    data() {
        return {
            filter: false,
            modalUserId: null,
            columns: [
                {
                    title: "Nama",
                    dataIndex: "name",
                    key: "name",
                    sorter: true,
                },
                {
                    title: "Email",
                    dataIndex: "email",
                    key: "email",
                    sorter: true,
                },
                {
                    title: "Role",
                    dataIndex: "roles",
                    key: "roles",
                    sorter: false,
                },
                {
                    title: "Status",
                    dataIndex: "status",
                    key: "status",
                    sorter: false,
                },
                {
                    title: "Bergabung Pada",
                    dataIndex: "created_at",
                    key: "created_at",
                    sorter: true,
                    customRender: ({ text }) =>
                        this.$helpers.formatDate(text, "D MMMM YYYY HH:mm"),
                },
                {
                    title: "Aksi",
                    key: "action",
                    sorter: false,
                },
            ],
            UserStatus: [
                { id: null, text: "Pilih Status" },
                { id: 1, text: "Aktif" },
                { id: 0, text: "Tidak Aktif" },
            ],
            UserRole: ["Pelanggan", "Admin", "Staff Gudang", "Manajer Stok"],
        };
    },
    methods: {
        handleTableChange(pagination, filters, sorter) {
            // Update pagination
            this.filters.page = pagination.current;
            this.filters.per_page = pagination.pageSize;

            console.log("Filters:", filters);

            // Handle sorting
            if (sorter.order) {
                // sorter.field = 'name'
                // sorter.order = 'ascend' atau 'descend'
                this.filters.sort_field = sorter.field;
                this.filters.sort_order = sorter.order;
            } else {
                // Reset sorting jika header diklik untuk ketiga kalinya (hapus sort)
                this.filters.sort_field = null;
                this.filters.sort_order = null;
            }

            // Panggil API dengan parameter sorting
            this.fetch();
        },

        addUser() {
            this.modalUserId = null;
            this.$refs.userModal.showModal();
        },

        editUser(user) {
            this.modalUserId = user.id;
            this.$refs.userModal.showModal();
        },

        handleUserSubmit(response) {
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
