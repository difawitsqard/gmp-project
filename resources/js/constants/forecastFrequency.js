export const forecastFrequencyMap = {
    D: { label: "Harian" },
    W: { label: "Mingguan" },
    M: { label: "Bulanan" },
    default: { label: "Tidak Diketahui" },
};

export function getForecastFrequencyLabel(frequency) {
    return (
        forecastFrequencyMap[frequency]?.label ||
        forecastFrequencyMap.default.label
    );
}

// get all forecast frequency
export function getAllForecastFrequencies() {
    return Object.entries(forecastFrequencyMap)
        .filter(([key]) => key !== "default")
        .map(([key, value]) => ({
            id: key,
            text: value.label,
        }));
}
