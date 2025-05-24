<template>
    <nav aria-label="Page navigation" class="pagination-style-3">
        <ul class="pagination mb-0 flex-wrap">
            <!-- Prev Button -->
            <li class="page-item" :class="{ disabled: currentPage <= 1 }">
                <a
                    class="page-link"
                    href="javascript:void(0);"
                    @click.prevent="changePage(currentPage - 1)"
                >
                    Prev
                </a>
            </li>

            <!-- First Page -->
            <li class="page-item" :class="{ active: currentPage === 1 }">
                <a
                    class="page-link"
                    href="javascript:void(0);"
                    @click.prevent="changePage(1)"
                    >1</a
                >
            </li>

            <!-- Ellipsis at start -->
            <li v-if="startEllipsis" class="page-item">
                <a class="page-link" href="javascript:void(0);">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
            </li>

            <!-- Middle Pages -->
            <li
                v-for="page in visiblePageNumbers"
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
            >
                <a
                    class="page-link"
                    href="javascript:void(0);"
                    @click.prevent="changePage(page)"
                    >{{ page }}</a
                >
            </li>

            <!-- Ellipsis at end -->
            <li v-if="endEllipsis" class="page-item">
                <a class="page-link" href="javascript:void(0);">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
            </li>

            <!-- Last Page -->
            <li
                v-if="totalPages > 1"
                class="page-item"
                :class="{ active: currentPage === totalPages }"
            >
                <a
                    class="page-link"
                    href="javascript:void(0);"
                    @click.prevent="changePage(totalPages)"
                    >{{ totalPages }}</a
                >
            </li>

            <!-- Next Button -->
            <li
                class="page-item"
                :class="{ disabled: currentPage >= totalPages }"
            >
                <a
                    class="page-link text-primary"
                    href="javascript:void(0);"
                    @click.prevent="changePage(currentPage + 1)"
                >
                    Next
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "PaginationStyle3",
    props: {
        currentPage: { type: Number, required: true },
        totalPages: { type: Number, required: true },
        maxVisiblePages: { type: Number, default: 3 },
    },
    computed: {
        visiblePageNumbers() {
            if (this.totalPages <= 2) return [];
            let start = Math.max(
                2,
                this.currentPage - Math.floor(this.maxVisiblePages / 2)
            );
            let end = Math.min(
                this.totalPages - 1,
                start + this.maxVisiblePages - 1
            );
            start = Math.max(2, end - this.maxVisiblePages + 1);
            const pages = [];
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        startEllipsis() {
            return (
                this.visiblePageNumbers.length > 0 &&
                this.visiblePageNumbers[0] > 2
            );
        },
        endEllipsis() {
            return (
                this.visiblePageNumbers.length > 0 &&
                this.visiblePageNumbers[this.visiblePageNumbers.length - 1] <
                    this.totalPages - 1
            );
        },
    },
    methods: {
        changePage(page) {
            if (
                page <= 0 ||
                page > this.totalPages ||
                page === this.currentPage
            )
                return;
            this.$emit("page-changed", page);
        },
    },
};
</script>
