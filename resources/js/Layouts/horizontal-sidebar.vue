<template>
    <div class="sidebar horizontal-sidebar">
        <div id="sidebar-menu-3" class="sidebar-menu">
            <ul class="nav">
                <li class="submenu">
                    <a
                        href="#sidebarDashboardOne"
                        data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="false"
                        aria-controls="sidebarDashboardOne"
                        :class="[
                            isDashboardOneMenu ? 'active' : 'notactive',
                            { subdrop: isDashboardOneMenuOpen },
                        ]"
                        v-on:click="
                            isDashboardOneMenuOpen = !isDashboardOneMenuOpen
                        "
                    >
                        <vue-feather type="grid"></vue-feather>
                        <span>Main Menu</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="collapse menu-dropdown" id="sidebarDashboardOne">
                        <li class="submenu">
                            <a
                                href="#sidebarDashboard"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboard"
                                :class="[
                                    isDashboardMenu ? 'active' : 'notactive',
                                    { subdrop: isDashboardMenuOpen },
                                ]"
                                v-on:click="
                                    isDashboardMenuOpen = !isDashboardMenuOpen
                                "
                            >
                                <span>Dashboard</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul
                                class="collapse menu-dropdown"
                                id="sidebarDashboard"
                            >
                                <li>
                                    <a href="javascript:void(0)"
                                        >Admin Dashboard</a
                                    >
                                </li>
                                <li>
                                    <a href="javascript:void(0)"
                                        >Sales Dashboard</a
                                    >
                                </li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a
                                href="#sidebarApplication"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarApplication"
                                :class="[
                                    isApplicationMenu ? 'active' : 'notactive',
                                    { subdrop: isApplicationMenuOpen },
                                ]"
                                v-on:click="
                                    isApplicationMenuOpen =
                                        !isApplicationMenuOpen
                                "
                            >
                                <span>Application</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul
                                class="collapse menu-dropdown"
                                id="sidebarApplication"
                            >
                                <li><a href="javascript:void(0)">Chat</a></li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);">
                                        <span>Call</span>
                                        <span
                                            class="menu-arrow inside-submenu"
                                        ></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)"
                                                >Video Call</a
                                            >
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"
                                                >Audio Call</a
                                            >
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"
                                                >Call History</a
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <!-- Other menu items -->
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- Other menu items -->
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isApplicationMenuOpen: false,
            isDashboardOneMenuOpen: false,
            isDashboardMenuOpen: false,
            isDashboardOneMenu: false,
            isDashboardMenu: false,
        };
    },
};
</script>
