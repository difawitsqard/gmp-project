import { reactive, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { skipPreloadNextRequest } from "@/composables/usePreloadControl";
import debounce from "lodash/debounce";

export function useInertiaFiltersDynamic(
    routeName,
    filterKeys = [],
    options = {}
) {
    const url = usePage().url;
    const query = new URLSearchParams(url.split("?")[1] || "");

    const filters = reactive({});
    filterKeys.forEach((key) => {
        const val = query.getAll(key);
        filters[key] = val.length > 1 ? val : val[0] ?? "";
    });

    const fetch = (extra = {}) => {
        const payload = { ...filters, ...extra };

        // Optional: clean empty
        Object.keys(payload).forEach((key) => {
            if (
                payload[key] === "" ||
                (Array.isArray(payload[key]) && payload[key].length === 0)
            ) {
                delete payload[key];
            }
        });

        skipPreloadNextRequest();
        router.get(route(routeName), payload, {
            preserveState: options.preserveState ?? true,
            preserveScroll: options.preserveScroll ?? true,
            replace: true,
            only: options.only || [],
        });
    };

    const reset = () => {
        filterKeys.forEach(
            (key) => (filters[key] = Array.isArray(filters[key]) ? [] : "")
        );
        fetch();
    };

    const addWatch = () => {
        filterKeys.forEach((key) => {
            if (key === "search") {
                watch(
                    () => filters[key],
                    debounce(() => fetch({ page: 1 }), 300)
                );
            } else {
                watch(
                    () => filters[key],
                    () => fetch({ page: 1 })
                );
            }
        });
    };

    addWatch();

    return { filters, fetch, reset };
}
