/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            spacing: {
                '70': '17.5rem',
            },
            colors: {
                navy: {
                    50:  '#f0f4fa',
                    100: '#d9e4f2',
                    200: '#b3c9e5',
                    300: '#8dadd8',
                    400: '#6692cb',
                    500: '#4077be',
                    600: '#2d5fa0',
                    700: '#1e3a5f',
                    800: '#152a47',
                    900: '#0f1f3d',
                    950: '#080f1e',
                },
                gold: {
                    50:  '#fdf9ec',
                    100: '#faf1cc',
                    200: '#f4e19a',
                    300: '#eccd60',
                    400: '#e4b832',
                    500: '#c8a84b',
                    600: '#b08a20',
                    700: '#8a6918',
                    800: '#6b5016',
                    900: '#4e3b13',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(15,31,61,0.08), 0 1px 2px -1px rgba(15,31,61,0.06)',
                'card-md': '0 4px 12px -2px rgba(15,31,61,0.12), 0 2px 4px -2px rgba(15,31,61,0.08)',
                'sidebar': '4px 0 24px -4px rgba(8,15,30,0.25)',
            },
            borderRadius: {
                'card': '10px',
            },
            transitionDuration: {
                '250': '250ms',
            },
        },
    },
    plugins: [],
};
