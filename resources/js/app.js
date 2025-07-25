import "./bootstrap";
import "../css/app.css";
import "../css/vue-multiselect.css";
import * as helpers from "@/utils/helpers";

import { isPreloading, skipNextPreload } from "./stores/preload";

import Permission from "@/utils/permission";

import { createApp, h, ref } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import { BootstrapVue3, BToastPlugin } from "bootstrap-vue-3";
import VueLazyload from "vue-lazyload";
import Vue3Autocounter from "vue3-autocounter";
import VueApexCharts from "vue3-apexcharts";
import VueSelect from "vue3-select2-component";
import VueMultiSelect from "vue-multiselect";
import DatePicker from "vue3-datepicker";
import StarRating from "vue-star-rating";
import Antd from "ant-design-vue";
import VueFeather from "vue-feather";
import "ant-design-vue/dist/reset.css";
import FlagIcon from "vue-flag-icon";
import VueSweetalert2 from "vue-sweetalert2";
import VueFormWizard from "vue3-form-wizard";
import VueEasyLightbox from "vue-easy-lightbox";
// import ThemifyIcon from "vue-themify-icons";
// import SimpleLineIcons from "vue-simple-line";

import LoadingOverlay from "@/components/LoadingOverlay.vue";

/********* Layout component**********/
import Header from "@/Layouts/pos-header.vue";
import Sidebar from "@/Layouts/pos-sidebar.vue";
import UserMenu from "@/Layouts/user-menu.vue";
import FilesSidebar from "@/Layouts/files-sidebar.vue";
import Settings_Sidebar from "@/Layouts/settings-sidebar.vue";
import Collapsed_Sidebar from "@/Layouts/collapsed-sidebar.vue";
import Horizontal_Sidebar from "@/Layouts/horizontal-sidebar.vue";
import SideSettings from "@/Layouts/side-settings.vue";

/********* Breadcrumb component**********/
import Breadcrumb from "@/components/breadcrumb/layout-breadcrumb.vue";

import "@fontsource/montserrat";
import "bootstrap/dist/css/bootstrap.min.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "boxicons/css/boxicons.min.css";
import "sweetalert2/dist/sweetalert2.min.css";
import "pe7-icon/dist/dist/pe-icon-7-stroke.css";
import "typicons.font/src/font/typicons.css";
import "weathericons/css/weather-icons.css";
import "ionicons-npm/css/ionicons.css";
import "@/assets/css/feather.css";
import "@/assets/css/style.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        router.on("start", () => {
            if (!skipNextPreload.value) {
                isPreloading.value = true;
            }
        });

        router.on("finish", () => {
            isPreloading.value = false;
            skipNextPreload.value = false;
        });

        const app = createApp({
            render: () =>
                h("div", [
                    h(LoadingOverlay, { visible: isPreloading.value }),
                    h(App, props),
                ]),
        })
            .use(plugin)
            .use(ZiggyVue);

        // Tambahkan helpers
        app.config.globalProperties.$helpers = helpers;

        /********* Layout component**********/
        app.component("layout-header", Header);
        app.component("layout-sidebar", Sidebar);
        app.component("user-menu", UserMenu);
        app.component("files-sidebar", FilesSidebar);
        app.component("settings-sidebar", Settings_Sidebar);
        app.component("collapsed-sidebar", Collapsed_Sidebar);
        app.component("horizontal-sidebar", Horizontal_Sidebar);
        app.component("side-settings", SideSettings);

        /********* Breadcrumb component**********/
        app.component("layout-breadcrumb", Breadcrumb);

        app.component("vue3-autocounter", Vue3Autocounter);
        app.component(VueFeather.name, VueFeather);
        app.component("vue-select", VueSelect);
        app.component("vue-multiselect", VueMultiSelect);
        app.component("date-picker", DatePicker);
        app.component("star-rating", StarRating);
        app.use(FlagIcon).use(VueFormWizard);
        app.use(VueSweetalert2);
        app.use(VueApexCharts);
        app.use(VueLazyload, {
            preLoad: 1.3,
            error: "/uploads/images/gmp-placeholder-image.svg",
            loading: "/uploads/images/loading-image.gif",
            attempt: 1,
        });
        app.use(VueEasyLightbox).use(Antd).use(BootstrapVue3).use(BToastPlugin);
        app.mixin(Permission);

        app.mount(el);
    },
    progress: false,
});
