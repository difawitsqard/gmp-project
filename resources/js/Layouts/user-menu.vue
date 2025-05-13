<template>
    <ul>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Inventaris</h6>
            <ul>
                <li
                    :class="{
                        active:
                            route().current('products.index') ||
                            route().current('products.show'),
                    }"
                >
                    <Link :href="route('products.index')">
                        <vue-feather type="box" />
                        <span>Produk</span>
                    </Link>
                </li>
                <li :class="{ active: route().current('products.create') }">
                    <Link :href="route('products.create')">
                        <vue-feather type="plus-square" />
                        <span>Buat Produk</span>
                    </Link>
                </li>
                <li
                    :class="{
                        active: route().current('product-categories.index'),
                    }"
                >
                    <Link :href="route('product-categories.index')">
                        <vue-feather type="codepen" />
                        <span>Kategori</span>
                    </Link>
                </li>
                <li
                    :class="{
                        active: route().current('units.index'),
                    }"
                >
                    <Link :href="route('units.index')">
                        <vue-feather type="speaker" />
                        <span>Satuan</span>
                    </Link>
                </li>
            </ul>
        </li>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Inventaris Pintar</h6>
            <ul>
                <li :class="{ active: route().current('forecasting.create') }">
                    <Link :href="route('forecasting.create')">
                        <vue-feather type="trending-up" />
                        <span>Prediksi Stok</span>
                    </Link>
                </li>
                <li :class="{ active: route().current('orders.create') }">
                    <Link :href="route('orders.create')">
                        <vue-feather type="shopping-cart" />
                        <span>Buat Pesanan</span>
                    </Link>
                </li>
            </ul>
        </li>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Pemesanan</h6>
            <ul>
                <li :class="{ active: route().current('orders.index') }">
                    <Link :href="route('orders.index')">
                        <vue-feather type="file-text" />
                        <span>Riwayat Pemesanan</span>
                    </Link>
                </li>
                <li :class="{ active: route().current('orders.create') }">
                    <Link :href="route('orders.create')">
                        <vue-feather type="shopping-cart" />
                        <span>Buat Pesanan</span>
                    </Link>
                </li>
            </ul>
        </li>
    </ul>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        Link,
    },
    // data() {
    //     return {
    //         openMenuItem: null,
    //         openSubmenuOneItem: null,
    //     };
    // },
    // methods: {
    //     expandSubMenus(menu) {
    //         this.sideBarData.forEach((item) => {
    //             item.menu.forEach((subMenu) => {
    //                 if (subMenu !== menu) {
    //                     subMenu.showSubRoute = false;
    //                 }
    //             });
    //         });
    //         menu.showSubRoute = !menu.showSubRoute;
    //     },
    //     openMenu(menu) {
    //         this.openMenuItem = this.openMenuItem === menu ? null : menu;
    //     },
    //     openSubmenuOne(subMenus) {
    //         this.openSubmenuOneItem =
    //             this.openSubmenuOneItem === subMenus ? null : subMenus;
    //     },
    // },
};
</script>
