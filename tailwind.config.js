import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/power-components/livewire-powergrid/resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: { sans: ["Figtree", ...defaultTheme.fontFamily.sans] },
            colors: {
                citasys: {
                    DEFAULT: "#0074A8",
                    dark: "#005f87",
                    text: "#4A4A4A",
                },
            },
        },
    },

    plugins: [require("daisyui")],
    daisyui: {
        themes: ["light", "dark", "cupcake"], // Bisa pilih tema DaisyUI
    },
};
