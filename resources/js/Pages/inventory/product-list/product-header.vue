<template>
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Produk</h4>
                <h6>Kelola Produk</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Refresh"
                    ><i data-feather="rotate-ccw" class="feather-rotate-ccw"></i
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
                    <i data-feather="chevron-up" class="feather-chevron-up"></i>
                </a>
            </li>
        </ul>
        <div class="page-btn">
            <Link :href="route('products.create')" class="btn btn-added">
                <vue-feather type="plus-circle" class="me-2"></vue-feather>
                Tambah Produk Baru
            </Link>
        </div>
    </div>
</template>
<script>
import { Link } from "@inertiajs/vue3";
export default {
    components: {
        Link,
    },
    data() {
        return {};
    },
    methods: {
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
