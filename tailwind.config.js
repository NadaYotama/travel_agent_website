/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#0d2b3a',
                lagoon: {
                    DEFAULT: '#137b8b',
                    50: '#f0f9fa',
                    100: '#e0f2f5',
                    200: '#bee4eb',
                    300: '#8ed1dd',
                    400: '#56b6c8',
                    500: '#137b8b',
                    600: '#106472',
                    700: '#0e505c',
                    800: '#0c424c',
                    900: '#0a353d',
                },
                foam: '#e8f3f4',
                sun: {
                    DEFAULT: '#f4b63f',
                    50: '#fffbf0',
                    100: '#fef5d6',
                    200: '#fde9ad',
                    300: '#fbd77a',
                    400: '#f8c24f',
                    500: '#f4b63f',
                    600: '#d99723',
                    700: '#b07318',
                    800: '#8d5a19',
                    900: '#754a18',
                },
                paper: '#fbfdfd',
            },
            fontFamily: {
                display: ['"Bricolage Grotesque"', 'Figtree', 'sans-serif'],
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                'soft': '0 4px 20px -2px rgba(19, 123, 139, 0.08), 0 2px 6px -2px rgba(13, 43, 58, 0.04)',
                'glow': '0 0 25px -5px rgba(19, 123, 139, 0.25)',
                'glow-sun': '0 0 25px -5px rgba(244, 182, 63, 0.35)',
                'card': '0 10px 30px -5px rgba(13, 43, 58, 0.08), 0 4px 10px -2px rgba(13, 43, 58, 0.03)',
            },
            borderRadius: {
                '4xl': '2rem',
            },
        },
    },
    plugins: [],
};

