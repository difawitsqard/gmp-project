export const orderStatusMap = {
    waiting_confirmation: { label: "Menunggu Konfirmasi", color: "dark" },
    pending: { label: "Menunggu Pembayaran", color: "dark" },
    processing: { label: "Diproses", color: "primary" },
    completed: { label: "Selesai", color: "success" },
    cancelled: { label: "Dibatalkan", color: "danger" },
    default: { label: "Status Tidak Diketahui", color: "secondary" },
};

export function getOrderStatus(status) {
    return orderStatusMap[status] || orderStatusMap.default;
}

// get all order status
export function getAllOrderStatus() {
    return Object.entries(orderStatusMap)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            id: key,
            text: value.label,
            color: value.color,
        }));
}
