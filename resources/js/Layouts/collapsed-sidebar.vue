<template>
    <div class="sidebar collapsed-sidebar" id="collapsed-sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu-2" class="sidebar-menu sidebar-menu-three">
                <aside id="aside" class="ui-aside">
                    <ul class="tab nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a
                                class="tablinks nav-link active"
                                href="#home"
                                id="home-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#home"
                                role="tab"
                                aria-selected="true"
                            >
                                <img
                                    src="@/assets/img/icons/menu-icon.svg"
                                    alt=""
                                />
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a
                                class="tablinks nav-link"
                                href="#messages"
                                id="messages-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#product"
                                role="tab"
                                aria-selected="false"
                            >
                                <img
                                    src="@/assets/img/icons/product.svg"
                                    alt=""
                                />
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a
                                class="tablinks nav-link"
                                href="#profile"
                                id="profile-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#sales"
                                role="tab"
                                aria-selected="false"
                            >
                                <img
                                    src="@/assets/img/icons/sales1.svg"
                                    alt=""
                                />
                            </a>
                        </li>
                        <!-- Tab lainnya tetap -->
                    </ul>
                </aside>
                <div class="tab-content tab-content-four pt-2">
                    <ul
                        class="tab-pane active"
                        id="home"
                        aria-labelledby="home-tab"
                    >
                        <li class="submenu">
                            <a
                                href="#sidebarDashboard"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboard"
                                :class="[
                                    isDashboardMenuOpen
                                        ? 'active'
                                        : 'notactive',
                                ]"
                                v-on:click="
                                    isDashboardMenuOpen = !isDashboardMenuOpen
                                "
                            >
                                <span>Dashboard</span
                                ><span class="menu-arrow"></span>
                            </a>
                            <ul
                                class="collapse menu-dropdown"
                                id="sidebarDashboard"
                            >
                                <li>
                                    <a href="#">Admin Dashboard</a>
                                </li>
                                <li>
                                    <a href="#">Sales Dashboard</a>
                                </li>
                            </ul>
                        </li>
                        <!-- Submenu lainnya tetap -->
                    </ul>
                    <!-- Tab lainnya tetap -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isDashboardMenuOpen: false,
            isApplicationMenuOpen: false,
            isExpensesMenuOpen: false,
            isLeavesMenuOpen: false,
            isBaseUIMenuOpen: false,
            isAdvancedMenuOpen: false,
            isChartsMenuOpen: false,
            isIconsMenuOpen: false,
            isFormsMenuOpen: false,
            isLayoutsMenuOpen: false,
            isTablesMenuOpen: false,
            isErrorMenuOpen: false,
            isGeneralMenuOpen: false,
            isWebsiteMenuOpen: false,
            isSystemMenuOpen: false,
            isAuthenticationMenuOpen: false,
            isFinancialMenuOpen: false,
            isAppMenuOpen: false,
            isAttendenceMenuOpen: false,
            isOtherMenuOpen: false,
            isPayrollMenuOpen: false,
            isPlacesMenuOpen: false,
        };
    },
};
</script>
