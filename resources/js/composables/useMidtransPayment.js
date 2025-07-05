import loadMidtransSnap from "@/utils/snap";

export default function useMidtransPayment() {
    const payWithMidtrans = async ({
        snapToken,
        clientKey,
        onSuccess,
        onPending,
        onError,
        onClose,
    }) => {
        const snap = await loadMidtransSnap(clientKey);
        snap.pay(snapToken, {
            // onSuccess: function (result) {
            //     onSuccess(result);
            // },
            onSuccess,
            onPending,
            onError,
            onClose,
            language: "id",
        });
    };

    // Optional: fungsi untuk tutup Snap
    const closeSnap = () => {
        if (window.snap && typeof window.snap.hide === "function") {
            window.snap.hide();
        }
    };

    return { payWithMidtrans, closeSnap };
}
