/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/Resources/**/*.blade.php", "./src/Resources/**/*.js"],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1440px",
            },

            padding: {
                DEFAULT: "90px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1440px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                navyBlue: "#09090b",
                lightOrange: "#FAFAFA",
                darkGreen: '#40994A',
                darkBlue: '#0044F2',
                darkPink: '#F85156',
            },

            fontFamily: {
                sans: ["Inter", "sans-serif"],
                inter: ["Inter", "sans-serif"],
                spacegrotesk: ["Space Grotesk", "sans-serif"],
            },
        }
    },

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        },
        {
            pattern: /iti__/,
        },
        // Grid columns — needed because inside <script type="x-template"> blocks
        // Tailwind JIT doesn't scan these, so we safelist them explicitly
        {
            pattern: /^grid-cols-(1|2|3|4|5|6)$/,
        },
        // Responsive grid variants (pattern doesn't work for variants in Tailwind v3)
        'md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4',
        // Flex row/col responsive variants used in x-template blocks
        'md:flex-row', 'md:flex-col', 'md:border-r', 'md:border-b-0', 'md:border-r-0',
        {
            pattern: /^(max-sm|max-md|max-lg|max-1060|max-1180):grid-cols-(1|2|3|4|5|6)$/,
            variants: ['max-sm', 'max-md', 'max-lg', 'max-1060', 'max-1180'],
        },
        {
            pattern: /^gap-(2|3|4|5|6|8|10)$/,
        },
    ]
};
