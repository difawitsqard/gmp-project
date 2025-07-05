<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route("other-browser-sessions.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
    <div class="card mt-4">
        <div class="card-body">
            <div class="card-title-head">
                <h6>
                    <span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="feather feather-monitor"
                        >
                            <rect
                                x="2"
                                y="3"
                                width="20"
                                height="14"
                                rx="2"
                                ry="2"
                            ></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </span>
                    Sesi Browser
                </h6>
            </div>

            <div class="mt-3">
                <p>
                    Jika perlu, Anda dapat keluar dari semua sesi browser lain
                    di semua perangkat Anda. Beberapa sesi terbaru Anda
                    tercantum di bawah ini; namun, daftar ini mungkin tidak
                    lengkap. Jika Anda merasa akun Anda telah disusupi, Anda
                    juga harus memperbarui kata sandi Anda.
                </p>
            </div>

            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-4">
                <div class="row">
                    <div
                        v-for="(session, i) in sessions"
                        :key="i"
                        class="col-md-6 mb-3"
                    >
                        <div class="d-flex p-3 border rounded bg-light">
                            <div class="me-3">
                                <i
                                    v-if="session.agent.is_desktop"
                                    class="fas fa-desktop fa-2x text-muted"
                                ></i>
                                <i
                                    v-else
                                    class="fas fa-mobile-alt fa-2x text-muted"
                                ></i>
                            </div>
                            <div>
                                <div class="fw-bold text-secondary">
                                    {{
                                        session.agent.platform
                                            ? session.agent.platform
                                            : "Unknown"
                                    }}
                                    -
                                    {{
                                        session.agent.browser
                                            ? session.agent.browser
                                            : "Unknown"
                                    }}
                                </div>
                                <div class="text-muted small">
                                    {{ session.ip_address }},
                                    <span
                                        v-if="session.is_current_device"
                                        class="text-success fw-bold"
                                        >Perangkat Ini</span
                                    >
                                    <span v-else
                                        >Terakhir aktif
                                        {{ session.last_active }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button @click="confirmLogout" class="btn btn-warning">
                    Keluar dari Sesi Browser Lain
                </button>

                <div
                    v-show="form.recentlySuccessful"
                    class="alert alert-success mt-2 p-2"
                >
                    Berhasil.
                </div>
            </div>

            <!-- Modal Konfirmasi Logout -->
            <div v-if="confirmingLogout" class="modal-container">
                <div
                    class="modal fade show"
                    style="display: block"
                    tabindex="-1"
                >
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Keluar dari Sesi Browser Lain
                                </h5>
                                <button
                                    type="button"
                                    class="btn-close"
                                    @click="closeModal"
                                ></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Silakan masukkan password Anda untuk
                                    mengonfirmasi bahwa Anda ingin keluar dari
                                    sesi browser lain di semua perangkat Anda.
                                </p>

                                <div class="mt-3">
                                    <div class="input-blocks">
                                        <label class="form-label"
                                            >Password</label
                                        >
                                        <input
                                            ref="passwordInput"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control"
                                            placeholder="Password"
                                            autocomplete="current-password"
                                            @keyup.enter="
                                                logoutOtherBrowserSessions
                                            "
                                        />
                                        <div
                                            v-if="form.errors.password"
                                            class="text-danger mt-1"
                                        >
                                            {{ form.errors.password }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="closeModal"
                                >
                                    Batal
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-warning"
                                    :disabled="form.processing"
                                    @click="logoutOtherBrowserSessions"
                                >
                                    Keluar dari Sesi Browser Lain
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-backdrop fade show"></div>
            </div>
        </div>
    </div>
</template>
