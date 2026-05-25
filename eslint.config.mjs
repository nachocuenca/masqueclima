import nextVitals from "eslint-config-next/core-web-vitals";
import nextTs from "eslint-config-next/typescript";

const eslintConfig = [
  ...nextVitals,
  ...nextTs,
  {
    ignores: [
      ".next/**",
      "node_modules/**",
      "vendor/**",
      "legacy/**",
      "public/assets/**",
      "content/**/*.json"
    ]
  }
];

export default eslintConfig;
