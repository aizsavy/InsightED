/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // ✅ This enables class-based dark mode
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.jsx",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Poppins", "sans-serif"],
      },
    },
  },
  plugins: [],
};
