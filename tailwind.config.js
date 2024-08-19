import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import {c} from "vite/dist/node/types.d-aGj9QkWt.js";
const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
    darkMode:'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: colors.indigo,
                secondary: colors.yellow,
                neutral: colors.gray,
                success: colors.green,
                danger: colors.red,
                warning: colors.yellow,
                info: colors.blue,
                gray: colors.zinc
            }
        }
    },

    plugins: [
        forms,
        typography,
        plugin(function ({ addUtilities }) {
            addUtilities({
                '.scrollbar-width-auto': {
                    'scrollbar-width': 'auto',
                },

                '.scrollbar-none': {
                    'scrollbar-width': 'none',
                    '&::-webkit-scrollbar': {
                        'display': 'none'
                    }
                },

                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                },

                '.scrollbar-light': {
                    '&::-webkit-scrollbar': {
                        width: '5px',
                        height: '8px',
                        // you color
                        background: colors.indigo["400"],
                        border: '4px solid transparent',
                        borderRadius: '8px',
                    },
                    '&::-webkit-scrollbar-thumb': {
                        // you color
                        background: colors.indigo["600"],
                        border: '4px solid transparent',
                        borderRadius: '8px',
                        backgroundClip: 'paddingBox',
                    },
                    '&::-webkit-scrollbar-thumb:hover': {
                        // you color
                        background: colors.indigo["500"],
                    },
                }
            })
        }),
    ],
};
