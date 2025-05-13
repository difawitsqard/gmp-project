<template>
    <div
        class="modal fade"
        id="cropModal"
        tabindex="-1"
        aria-hidden="true"
        ref="modal"
    >
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Gambar</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body d-flex gap-4">
                    <div class="flex-grow-1">
                        <VueCropper
                            ref="cropper"
                            :aspectRatio="parseFloat(cropRatio)"
                            :src="imageSrc"
                            preview=".preview"
                            class="w-100"
                            :view-mode="1"
                            :auto-crop-area="1"
                            drag-mode="move"
                            :crop-box-movable="true"
                            :crop-box-resizable="true"
                            :background="true"
                            :responsive="true"
                            @ready="onCropperReady"
                        />
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <button
                                class="btn btn-sm btn-primary"
                                @click="zoom(0.2)"
                            >
                                Zoom In
                            </button>
                            <button
                                class="btn btn-sm btn-primary"
                                @click="zoom(-0.2)"
                            >
                                Zoom Out
                            </button>
                            <button
                                class="btn btn-sm btn-secondary"
                                @click="move(-10, 0)"
                            >
                                Left
                            </button>
                            <button
                                class="btn btn-sm btn-secondary"
                                @click="move(10, 0)"
                            >
                                Right
                            </button>
                            <button
                                class="btn btn-sm btn-secondary"
                                @click="move(0, -10)"
                            >
                                Up
                            </button>
                            <button
                                class="btn btn-sm btn-secondary"
                                @click="move(0, 10)"
                            >
                                Down
                            </button>
                            <button
                                class="btn btn-sm btn-warning"
                                @click="rotate(90)"
                            >
                                Rotate +90
                            </button>
                            <button
                                class="btn btn-sm btn-warning"
                                @click="rotate(-90)"
                            >
                                Rotate -90
                            </button>
                            <button class="btn btn-sm btn-dark" @click="flipX">
                                Flip X
                            </button>
                            <button class="btn btn-sm btn-dark" @click="flipY">
                                Flip Y
                            </button>
                            <button class="btn btn-sm btn-info" @click="reset">
                                Reset
                            </button>
                        </div>
                    </div>
                    <div style="width: 300px">
                        <p class="mb-1">Preview</p>
                        <div
                            class="preview border mb-3"
                            style="width: 100%; height: 170px; overflow: hidden"
                        ></div>
                        <p class="mb-1">Hasil Crop</p>
                        <div
                            class="border"
                            style="
                                width: 100%;
                                height: 170px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            "
                        >
                            <img
                                v-if="cropImg"
                                :src="cropImg"
                                alt="Cropped"
                                class="img-fluid"
                            />
                            <span v-else class="text-muted"
                                >Belum ada gambar</span
                            >
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="handleCrop"
                    >
                        Crop & Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";

export default {
    name: "CropperModal",
    components: {
        VueCropper,
    },
    props: {
        cropRatio: {
            type: String,
            default: "1",
        },
        imageWidth: {
            type: Number,
            default: 300,
        },
        imageHeight: {
            type: Number,
            default: 300,
        },
    },
    data() {
        return {
            cropImg: "",
            imageSrc: null,
            flipXState: 1,
            flipYState: 1,
            modalInstance: null,
        };
    },
    methods: {
        onCropperReady() {
            console.log("Cropper is ready");
        },
        showModal(imgSrc) {
            this.cropImg = "";
            this.flipXState = 1;
            this.flipYState = 1;
            this.imageSrc = imgSrc;
            this.$nextTick(() => {
                const modalEl = this.$refs.modal;
                this.modalInstance = new bootstrap.Modal(modalEl);
                this.modalInstance.show();
            });
        },
        handleCrop() {
            const cropper = this.$refs.cropper?.cropper;
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: this.imageWidth,
                height: this.imageHeight,
            });

            if (canvas) {
                this.cropImg = canvas.toDataURL();
                canvas.toBlob((blob) => {
                    this.$emit("cropped", blob);
                    if (this.modalInstance) this.modalInstance.hide();
                });
            }
        },
        zoom(val) {
            this.$refs.cropper?.cropper?.zoom(val);
        },
        move(x, y) {
            this.$refs.cropper?.cropper?.move(x, y);
        },
        rotate(deg) {
            this.$refs.cropper?.cropper?.rotate(deg);
        },
        reset() {
            this.$refs.cropper?.cropper?.reset();
        },
        flipX() {
            this.flipXState = -this.flipXState;
            this.$refs.cropper?.cropper?.scaleX(this.flipXState);
        },
        flipY() {
            this.flipYState = -this.flipYState;
            this.$refs.cropper?.cropper?.scaleY(this.flipYState);
        },
    },
};
</script>

<style scoped>
.preview img {
    max-width: 100%;
}
</style>
