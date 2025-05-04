export default function loadMidtransSnap(clientKey) {
    return new Promise((resolve, reject) => {
        if (typeof window.snap !== "undefined") {
            return resolve(window.snap); // Sudah dimuat
        }

        const script = document.createElement("script");
        script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
        script.setAttribute("data-client-key", clientKey);
        script.onload = () => resolve(window.snap);
        script.onerror = () =>
            reject(new Error("Gagal memuat Midtrans Snap.js"));
        document.head.appendChild(script);
    });
}
