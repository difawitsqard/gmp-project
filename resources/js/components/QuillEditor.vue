<template>
    <quill-editor
        v-model:value="state.content"
        :options="state.editorOption"
        @change="onEditorChange($event)"
    />
</template>

<script>
import { reactive } from "vue";
import { quillEditor } from "vue3-quill";

export default {
    components: {
        quillEditor,
    },
    emits: ["update:modelValue", "blur", "focus", "ready", "change"],
    props: {
        modelValue: {
            type: String,
            default: "",
        },
        placeholder: {
            type: String,
            default: "Tulis konten Anda di sini...",
        },
        theme: {
            type: String,
            default: "bubble", // bubble atau snow
            validator: (value) => ["bubble", "snow"].includes(value),
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
        toolbarOptions: {
            type: Array,
            default: () => [
                ["bold", "italic", "underline", "strike"],
                ["blockquote", "code-block"],
                [{ header: [1, 2, 3, 4, 5, 6, false] }],
                [{ list: "ordered" }, { list: "bullet" }],
                //["link", "image"],
                ["link"],
                ["clean"],
            ],
        },
        height: {
            type: String,
            default: "200px",
        },
    },
    setup(props, { emit }) {
        const state = reactive({
            content: props.modelValue || "",
            _content: "",
            editorOption: {
                placeholder: props.placeholder,
                theme: props.theme,
                modules: {
                    toolbar: props.toolbarOptions,
                },
                // more options
            },
            disabled: false,
        });

        // const onEditorBlur = (quill) => {
        //     console.log("editor blur!", quill);
        // };
        // const onEditorFocus = (quill) => {
        //     console.log("editor focus!", quill);
        // };
        // const onEditorReady = (quill) => {
        //     console.log("editor ready!", quill);
        // };
        const onEditorChange = ({ quill, html, text }) => {
            emit("update:modelValue", html);
        };

        setTimeout(() => {
            state.disabled = true;
        }, 2000);

        return {
            state,
            // onEditorBlur,
            // onEditorFocus,
            // onEditorReady,
            onEditorChange,
        };
    },
};
</script>

<style>
.ql-container {
    min-height: 150px;
    max-height: 400px;
    overflow-y: auto;
}

.ql-editor {
    min-height: 150px;
}

/* Styling dasar container */
.quill-container {
    @apply border border-gray-300 overflow-hidden;
}

/* Styling untuk editor dengan tema bubble */
.ql-bubble .ql-editor.quill-editor-custom {
    @apply min-h-[200px] px-3 py-2 border-0;
}

/* Styling focus untuk container */
.quill-container:focus-within {
    @apply border-indigo-500 ring-1 ring-indigo-500;
}

/* Tambahkan styling lain yang diperlukan */
.ql-container {
    @apply font-sans text-base text-gray-700;
}

.ql-editor {
    @apply prose prose-sm max-w-none;
}

/* Override styling bawaan Quill yang mungkin bertentangan */
.ql-bubble .ql-editor {
    border: 1px solid #d1d5db !important; /* Gunakan !important untuk menimpa style bawaan */
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    display: block;
    width: 100%;
}

/* Pastikan container tidak membatasi tooltip */
#quill-container {
    position: relative !important;
    overflow: visible !important;
}

.ql-container.ql-bubble {
    overflow: visible !important;
    border: none !important;
}

.ql-toolbar {
    z-index: 999 !important; /* Pastikan toolbar tetap di atas */
}
</style>
