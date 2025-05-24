export const paymentMap = {
    bank_transfer: {
        label: "Transfer Bank",
        color: "primary",
        getDetail: (status) => {
            // status.va_numbers[0].bank biasanya: 'bca', 'bni', 'bri', dst
            if (status.va_numbers && status.va_numbers.length > 0) {
                const bank = status.va_numbers[0].bank?.toUpperCase();
                return `Bank ${bank}`;
            }
            return "Transfer Bank";
        },
    },
    echannel: {
        label: "Mandiri Bill Payment",
        color: "primary",
        getDetail: () => "Mandiri Bill Payment",
    },
    credit_card: {
        label: "Kartu Kredit/Debit",
        color: "info",
        getDetail: () => "Kartu Kredit/Debit",
    },
    gopay: {
        label: "GoPay",
        color: "success",
        getDetail: () => "GoPay",
    },
    shopeepay: {
        label: "ShopeePay",
        color: "warning",
        getDetail: () => "ShopeePay",
    },
    qris: {
        label: "QRIS",
        color: "secondary",
        getDetail: () => "QRIS",
    },
    cstore: {
        label: "Convenience Store",
        color: "secondary",
        getDetail: (status) => {
            // status.store biasanya: 'indomaret' atau 'alfamart'
            if (status.store) {
                return (
                    status.store.charAt(0).toUpperCase() + status.store.slice(1)
                );
            }
            return "Convenience Store";
        },
    },
    akulaku: {
        label: "Akulaku",
        color: "danger",
        getDetail: () => "Akulaku",
    },
    bca_klikbca: {
        label: "KlikBCA",
        color: "primary",
        getDetail: () => "KlikBCA",
    },
    bca_klikpay: {
        label: "BCA KlikPay",
        color: "primary",
        getDetail: () => "BCA KlikPay",
    },
    bri_epay: {
        label: "BRI ePay",
        color: "primary",
        getDetail: () => "BRI ePay",
    },
    danamon_online: {
        label: "Danamon Online",
        color: "primary",
        getDetail: () => "Danamon Online",
    },
    cimb_clicks: {
        label: "CIMB Clicks",
        color: "primary",
        getDetail: () => "CIMB Clicks",
    },
    uob_ezpay: {
        label: "UOB ezPay",
        color: "primary",
        getDetail: () => "UOB ezPay",
    },
    permata_va: {
        label: "Permata VA",
        color: "primary",
        getDetail: () => "Permata VA",
    },
    bni_va: {
        label: "BNI VA",
        color: "primary",
        getDetail: () => "BNI VA",
    },
    bri_va: {
        label: "BRI VA",
        color: "primary",
        getDetail: () => "BRI VA",
    },
    bca_va: {
        label: "BCA VA",
        color: "primary",
        getDetail: () => "BCA VA",
    },
    dana: {
        label: "DANA",
        color: "success",
        getDetail: () => "DANA",
    },
    default: {
        label: "Metode Lain",
        color: "secondary",
        getDetail: () => "Metode Lain",
    },
};

// Helper untuk ambil label dan warna
export function getPaymentInfo(paymentType, status = {}) {
    const map = paymentMap[paymentType] || paymentMap.default;
    return {
        label: map.label,
        color: map.color,
        detail: map.getDetail(status),
    };
}
