export const paymentStatusMap = {
    pending: { label: "Belum Dibayar", color: "dark" },
    paid: { label: "Lunas", color: "success" },
    failed: { label: "Gagal", color: "danger" },
    expired: { label: "Kadaluarsa", color: "danger" },
    cancelled: { label: "Dibatalkan", color: "danger" },
    default: { label: "Status Tidak Diketahui", color: "secondary" },
};

export function getPaymentStatus(status) {
    return paymentStatusMap[status] || paymentStatusMap.default;
}

// get all payment status
export function getAllPaymentStatus() {
    return Object.entries(paymentStatusMap)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            value: key,
            label: value.label,
            color: value.color,
        }));
}
