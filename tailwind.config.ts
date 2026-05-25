import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./content/**/*.{js,ts,jsx,tsx,mdx}",
    "./lib/**/*.{js,ts,jsx,tsx,mdx}"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          ink: "#15191f",
          teal: "#0f766e",
          mint: "#dff7ef",
          sun: "#f5b642",
          mist: "#f6f8f9"
        }
      }
    }
  }
};

export default config;
