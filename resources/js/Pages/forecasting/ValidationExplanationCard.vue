<template>
    <div class="accordion accordion-info mb-2" id="accordionPrimaryExample">
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
                        <i class="fas fa-lightbulb me-2"></i>
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
                            yang <b>konsisten</b>. Terlalu banyak periode kosong
                            (nilai 0) dapat menurunkan akurasi.
                        </p>
                    </div>

                    <h6 class="mb-3">
                        <i class="fas fa-clipboard-check me-1"></i>
                        Sistem akan membantu anda dalam menilai kualitas data,
                        berikut merupakan Kriteria utama
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="text-dark">Minimal 13 Periode</h6>
                                <p class="text-muted mt-2 mb-0">
                                    Dibutuhkan setidaknya 13 titik data
                                    penjualan untuk mengenali pola.
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
                                    (Contoh: Frekuensi harian dan 30 hari
                                    berturut-turut tanpa penjualan akan dianggap
                                    terlalu kosong.)
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="text-dark">
                                    Data Tidak Flat/Monoton
                                </h6>
                                <p class="text-muted mt-2 mb-0">
                                    Data tidak boleh terlalu datar atau monoton.
                                    Jika 90% data memiliki nilai yang sama, maka
                                    data dianggap terlalu datar untuk prediksi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">
                        <i class="fas fa-star me-1"></i>
                        Tips Memilih Produk dengan Kualitas Data Terbaik
                    </h6>

                    <!-- Add Non-Zero Ratio Explanation -->
                    <div class="alert alert-light border mb-2">
                        <p>
                            Untuk membantu anda dalam melihat produk dengan
                            kualitas terbaik, sistem akan menampilkan informasi
                            Non-Zero Ratio (Data tidak Nol) dan keterangan
                            tambahan jika data tidak memenuhi syarat pada setiap
                            produk
                        </p>
                    </div>

                    <div class="alert alert-info border mb-3">
                        <p>
                            Non-Zero Ratio (Data tidak Nol) adalah persentase
                            periode yang memiliki data penjualan (tidak nol).
                            Semakin tinggi rasio ini, maka kemungkinan data
                            untuk prediksi akan lebih baik.
                        </p>
                    </div>

                    <!-- Technology Partners Section -->

                    <!-- <div class="d-flex flex-column align-items-center mt-4">
                        <h4 class="text-dark mb-3 text-center">
                            Teknologi Pendukung
                        </h4>
                        <div
                            class="d-flex gap-3 justify-content-center align-items-center mb-2"
                        >
                            <img
                                src="https://www.nixtla.io/nixtla-logo.svg"
                                alt="Nixtla"
                                height="32"
                                style="max-width: 120px"
                            />
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/4/4d/OpenAI_Logo.svg"
                                alt="OpenAI"
                                height="32"
                                style="max-width: 120px"
                            />
                        </div>
                    </div> -->
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
