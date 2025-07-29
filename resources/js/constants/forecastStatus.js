export const forecastStatusMap = {
    pending: { label: "Menunggu Proses", color: "dark", icon: "clock" },
    processing: {
        label: "Sedang Diproses",
        color: "primary",
        icon: "refresh-cw",
    },
    done: { label: "Selesai", color: "success", icon: "check-circle" },
    failed: { label: "Gagal", color: "danger", icon: "x-circle" },
    default: {
        label: "Status Tidak Diketahui",
        color: "secondary",
        icon: "alert-octagon",
    },
};

export function getForecastStatus(status) {
    return forecastStatusMap[status] || forecastStatusMap.default;
}

// get all forecast status
export function getAllForecastStatus() {
    return Object.entries(forecastStatusMap)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            id: key,
            text: value.label,
            color: value.color,
        }));
}
