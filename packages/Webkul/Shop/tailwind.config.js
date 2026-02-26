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
        // Grid columns — needed because inside <script type="x-template"> blocks
        // Tailwind JIT doesn't scan these, so we safelist them explicitly
        {
            pattern: /^grid-cols-(1|2|3|4|5|6)$/,
        },
        {
            pattern: /^(max-sm|max-md|max-lg|max-1060|max-1180):grid-cols-(1|2|3|4|5|6)$/,
            variants: ['max-sm', 'max-md', 'max-lg', 'max-1060', 'max-1180'],
        },
        {
            pattern: /^gap-(2|3|4|5|6|8|10)$/,
        },
    ]
};
