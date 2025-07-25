<template>
    <ul>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Main</h6>
            <ul>
                <li :class="{ active: route().current('dashboard') }">
                    <Link :href="route('dashboard')">
                        <vue-feather type="grid" />
                        <span>Dasbor {{ $page.props.auth.user.roles[0] }}</span>
                    </Link>
                </li>
            </ul>
        </li>

        <li class="submenu-open" v-if="hasRole('Manajer Stok | Staff Gudang')">
            <h6 class="submenu-hdr">Inventaris</h6>
            <ul>
                <li
                    :class="{
                        active:
                            route().current('products.index') ||
                            route().current('products.show') ||
                            route().current('products.edit'),
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
                        active: route().current('stock-management.index'),
                    }"
                >
                    <Link :href="route('stock-management.index')">
                        <vue-feather type="layers" />
                        <span>Kelola Stok</span>
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
        <li class="submenu-open" v-if="hasRole('Manajer Stok')">
            <h6 class="submenu-hdr">Inventaris Pintar</h6>
            <ul>
                <li :class="{ active: route().current('forecasting.create') }">
                    <Link :href="route('forecasting.create')">
                        <vue-feather type="trending-up" />
                        <span>Prediksi Stok</span>
                    </Link>
                </li>
                <li
                    :class="{
                        active:
                            route().current('forecasting.index') ||
                            route().current('forecasting.show'),
                    }"
                >
                    <Link :href="route('forecasting.index')">
                        <vue-feather type="bar-chart-2" />
                        <span>Riwayat Prediksi</span>
                    </Link>
                </li>
            </ul>
        </li>
        <li class="submenu-open" v-if="hasRole('Admin | Pelanggan')">
            <h6 class="submenu-hdr">Pemesanan</h6>
            <ul>
                <li :class="{ active: route().current('orders.create') }">
                    <Link :href="route('orders.create')">
                        <vue-feather type="shopping-cart" />
                        <span>Buat Pesanan</span>
                    </Link>
                </li>
                <li
                    :class="{
                        active:
                            route().current('orders.index') ||
                            route().current('orders.show'),
                    }"
                >
                    <Link :href="route('orders.index')">
                        <vue-feather type="file-text" />
                        <span>Riwayat Pesanan</span>
                    </Link>
                </li>
            </ul>
        </li>
        <li class="submenu-open">
            <h6 class="submenu-hdr">Lainnya</h6>
            <ul>
                <li
                    :class="{
                        active:
                            route().current('orders.index') ||
                            route().current('orders.show'),
                    }"
                    v-if="hasRole('Staff Gudang')"
                >
                    <Link :href="route('orders.index')">
                        <vue-feather type="inbox" />
                        <span>Pesanan Masuk</span>
                    </Link>
                </li>

                <li
                    :class="{ active: route().current('taxes.index') }"
                    v-if="hasRole('Admin')"
                >
                    <Link :href="route('taxes.index')">
                        <vue-feather type="dollar-sign" />
                        <span>Tarif Pajak</span>
                    </Link>
                </li>
                <li
                    :class="{ active: route().current('users.index') }"
                    v-if="hasRole('Admin | Manajer Stok')"
                >
                    <Link :href="route('users.index')">
                        <vue-feather type="users" />
                        <span>Kelola Pengguna</span>
                    </Link>
                </li>
                <li :class="{ active: route().current('profile.show') }">
                    <Link :href="route('profile.show')">
                        <vue-feather type="settings" />
                        <span>Pengaturan Akun</span>
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
};
</script>
