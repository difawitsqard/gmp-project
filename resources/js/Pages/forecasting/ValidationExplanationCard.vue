<template>
    <div class="accordion accordion-info mb-3" id="accordionPrimaryExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingPrimaryOne">
                <button
                    class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsePrimaryOne"
                    aria-expanded="false"
                    aria-controls="collapsePrimaryOne"
                >
                    <i class="fas fa-shield-check text-secondary me-3"></i>
                    <div>
                        Kriteria data untuk hasil prediksi terbaik
                        <!-- <span class="fw-semibold"
                            >Tips Mendapatkan Prediksi yang Akurat</span
                        >
                        <small class="d-block text-muted"
                            >Kriteria data untuk hasil prediksi terbaik</small
                        > -->
                    </div>
                </button>
            </h2>
            <div
                id="collapsePrimaryOne"
                class="accordion-collapse collapse"
                aria-labelledby="headingPrimaryOne"
                data-bs-parent="#accordionPrimaryExample"
            >
                <div class="accordion-body">
                    <div class="alert alert-light border mb-3">
                        <h6 class="text-dark">Kenapa Kualitas Data Penting?</h6>
                        <p class="mt-2">
                            Prediksi bekerja paling baik dengan data penjualan
                            yang **konsisten**. Terlalu banyak periode kosong
                            (nilai 0) akan menurunkan akurasi.
                        </p>
                    </div>

                    <h6 class="mb-3">
                        <i class="fas fa-clipboard-check me-2"></i>Kriteria
                        Utama
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="text-dark">Minimal 13 Periode</h6>
                                <p class="text-muted mt-2 mb-0">
                                    Dibutuhkan setidaknya 13 titik data
                                    penjualan (misal: 13 hari untuk frekuensi
                                    harian) untuk mengenali pola.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="text-dark">Minimal 5 Penjualan</h6>
                                <p class="text-muted mt-2 mb-0">
                                    Setidaknya 5 dari 13 periode harus memiliki
                                    nilai (lebih dari 0) agar data tidak terlalu
                                    kosong.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="text-dark">Jeda Maksimal</h6>
                                <p class="text-muted mt-2">
                                    Tidak boleh ada jeda penjualan terlalu lama.
                                    (Contoh: 30 hari berturut-turut tanpa
                                    penjualan akan dianggap terlalu kosong.)
                                </p>
                                <!-- <div
                                    class="d-flex justify-content-between mb-1"
                                >
                                    <span>Harian</span>
                                    <strong>Maks 30 hari</strong>
                                </div>
                                <div
                                    class="d-flex justify-content-between mb-1"
                                >
                                    <span>Mingguan</span>
                                    <strong>Maks 8 minggu</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Bulanan</span>
                                    <strong>Maks 3 bulan</strong>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">
                        <i class="fas fa-star me-2"></i>Skor Kualitas Data
                    </h6>
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center small ps-0"
                        >
                            80-100% Data Terisi
                            <span class="badge bg-success">Sangat Baik</span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center small ps-0"
                        >
                            60-79% Data Terisi
                            <span class="badge bg-info">Baik</span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center small ps-0"
                        >
                            40-59% Data Terisi
                            <span class="badge bg-warning text-dark"
                                >Cukup</span
                            >
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center small ps-0"
                        >
                            0-39% Data Terisi
                            <span class="badge bg-danger">Kurang</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PredictionTips",
};
</script>
