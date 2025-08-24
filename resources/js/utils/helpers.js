export function getImageUrl(imageName) {
    return new URL(`${imageName}`, import.meta.url).href;
}

// rupiah formatter
export function formatRupiah(angka, prefix) {
    let number_string = angka
        .toString()
        .replace(/[^,\d]/g, "")
        .toString();
    let split = number_string.split(",");
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
    return prefix === undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
}

export function formatDate(dateString, format = "D MMMM YYYY") {
    if (!dateString) return "-";

    try {
        return window.dayjs(dateString).format(format);
    } catch (error) {
        console.error("Error formatting date:", error);
        return "-";
    }
}

export function timeAgo(dateString) {
    if (!dateString) return "-";

    if (!window.dayjs.fn.fromNow) {
        import("dayjs/plugin/relativeTime").then((module) => {
            window.dayjs.extend(module.default);
        });
    }

    return window.dayjs(dateString).fromNow();
}

export function formatDateRange(startDate, endDate, format = "D MMMM YYYY") {
    return `${formatDate(startDate, format)} - ${formatDate(endDate, format)}`;
}

// helper untuk membuat nama jadi singkat kalo terlalu panjang
// contoh Bajragin Koko Prayoga => Bajragin K. P.
export function abbreviateName(name) {
    const parts = name.split(" ");
    if (parts.length > 2) {
        return `${parts[0]} ${parts[1].charAt(0)}. ${parts[2].charAt(0)}.`;
    }
    return name;
}
