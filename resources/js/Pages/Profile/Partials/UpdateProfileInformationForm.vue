<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: "PUT",
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ? String(props.user.phone) : "",
    address: props.user.address,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route("user-profile-information.update"), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route("current-user-photo.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <div class="card">
        <div class="card-body">
            <form
                @submit.prevent="updateProfileInformation"
                enctype="multipart/form-data"
            >
                <div class="profile-set">
                    <div class="profile-head"></div>
                    <div class="profile-top">
                        <div class="profile-content">
                            <div
                                class="profile-contentimg"
                                style="display: flex; align-items: center"
                            >
                                <img
                                    v-show="!photoPreview"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                    style="
                                        width: 100%;
                                        height: 100%;
                                        object-fit: cover;
                                        display: block;
                                    "
                                />
                                <img
                                    v-show="photoPreview"
                                    :src="photoPreview"
                                    alt="img-preview"
                                    style="
                                        width: 100%;
                                        height: 100%;
                                        object-fit: cover;
                                        display: block;
                                    "
                                />
                                <div class="profileupload">
                                    <!-- Profile Photo File Input -->
                                    <input
                                        id="photo"
                                        ref="photoInput"
                                        type="file"
                                        class="hidden"
                                        @change="updatePhotoPreview"
                                    />
                                    <a href="javascript:void(0);">
                                        <vue-feather
                                            type="camera"
                                            class="text-white"
                                            style="width: 16px; height: 16px"
                                        />
                                    </a>
                                </div>
                            </div>
                            <div class="profile-contentname">
                                <h2>{{ user.name }}</h2>
                                <h4>{{ user.roles[0] || "No Role" }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-title-head">
                    <h6 class="pb-2 py-sm-3">
                        <span class="me-2"
                            ><svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="feather feather-user feather-chevron-up"
                            >
                                <path
                                    d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                ></path>
                                <circle cx="12" cy="7" r="4"></circle></svg
                        ></span>
                        Informasi Umum
                    </h6>
                </div>
                <div
                    v-show="form.recentlySuccessful"
                    class="alert alert-solid-success alert-dismissible fade show"
                >
                    Informasi profil telah diperbarui.
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    >
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Nama</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                v-model="form.name"
                                required
                            />
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label>Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-control"
                                required
                                autocomplete="username"
                            />
                        </div>

                        <div
                            v-if="
                                $page.props.jetstream.hasEmailVerification &&
                                user.email_verified_at === null
                            "
                        >
                            <p class="text-sm mt-2">
                                Alamat email Anda belum diverifikasi.

                                <Link
                                    :href="route('verification.send')"
                                    method="post"
                                    as="button"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    @click.prevent="sendEmailVerification"
                                >
                                    Klik di sini untuk mengirim ulang tautan
                                    verifikasi email.
                                </Link>
                            </p>

                            <div
                                v-show="verificationLinkSent"
                                class="mt-2 font-medium text-sm text-green-600"
                            >
                                Tautan verifikasi email telah dikirim.
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                v-model="form.phone"
                                placeholder="Nomor telepon"
                                class="form-control"
                            />
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-12">
                        <div class="input-blocks">
                            <label class="form-label">Alamat</label>
                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="Alamat lengkap"
                                v-model="form.address"
                            ></textarea>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button
                            class="btn btn-submit me-2"
                            :disabled="form.processing"
                        >
                            Perbarui
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
