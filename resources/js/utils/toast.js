// src/utils/toast.js
export const showToast = (vm, message, options = {}) => {
    vm.$bvToast?.toast(message, {
        title: options.title || "Notification",
        variant: options.variant || "info",
        solid: true,
        autoHideDelay: options.delay || 3000,
        appendToast: options.append || false,
    });
};
