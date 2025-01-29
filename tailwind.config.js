import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./node_modules/flowbite/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#153B8A', // Azul oscuro personalizado
                primaryhover:'#2454AD',
                secondary: '#ffffff', // Amarillo personalizado
                secondaryhover:'#eee',
                success: '#0e9f6e', // Naranja personalizado
                successhover: '#117729',
                danger: '#dd1f12',
                dangerhover: '#bb1a0f',

            },
        },
    },

    plugins: [
        forms,
        typography,
        require('flowbite/plugin')
    ],
};
