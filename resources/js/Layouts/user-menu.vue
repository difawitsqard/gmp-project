<template>
    <ul>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Inventory</h6>
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
                        <span>Products</span>
                    </Link>
                </li>
                <li :class="{ active: route().current('products.create') }">
                    <Link :href="route('products.create')">
                        <vue-feather type="plus-square" />
                        <span>Create Product</span>
                    </Link>
                </li>
            </ul>
        </li>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Sales</h6>
            <ul>
                <li :class="{ active: route().current('products.create') }">
                    <Link :href="route('products.create')">
                        <vue-feather type="plus-square" />
                        <span>Create Product</span>
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
