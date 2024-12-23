// src/directives/tooltip.js
import { Tooltip } from 'bootstrap';

export default {
    mounted(el, binding) {
        // Inisialisasi tooltip pada elemen
        new Tooltip(el, {
            title: binding.value || 'Default Tooltip', // Tooltip berdasarkan value yang diberikan
            trigger: 'hover'
        });
    },
    unmounted(el) {
        // Hapus tooltip saat elemen di-unmounted
        const tooltip = Tooltip.getInstance(el);
        if (tooltip) {
            tooltip.dispose();
        }
    }
};
