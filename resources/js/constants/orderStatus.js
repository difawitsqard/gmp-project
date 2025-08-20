export const orderStatusMap = {
    waiting_confirmation: { label: "Menunggu Konfirmasi", color: "dark" },
    pending: { label: "Menunggu Pembayaran", color: "dark" },
    waiting_processing: { label: "Diproses", color: "primary" },
    completed: { label: "Selesai", color: "success" },
    cancelled: { label: "Dibatalkan", color: "danger" },
    default: { label: "Status Tidak Diketahui", color: "secondary" },
};

export const orderStatusMapForWarehouse = {
    waiting_processing: { label: "Menunggu Diproses", color: "warning" }, // Ubah untuk staff gudang
    completed: { label: "Selesai", color: "success" },
};

export function getOrderStatus(status) {
    return orderStatusMap[status] || orderStatusMap.default;
}

export function getOrderStatusForWarehouse(status) {
    return (
        orderStatusMapForWarehouse[status] || orderStatusMapForWarehouse.default
    );
}

// get all order status
export function getAllOrderStatus() {
    return Object.entries(orderStatusMap)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            value: key,
            label: value.label,
            color: value.color,
        }));
}

export function getAllOrderStatusForWarehouse() {
    return Object.entries(orderStatusMapForWarehouse)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            value: key,
            label: value.label,
            color: value.color,
        }));
}
