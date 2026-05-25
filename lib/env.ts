export type AppEnv = "staging" | "production";

export const appEnv: AppEnv =
  process.env.APP_ENV === "production" ? "production" : "staging";

export const isProduction = appEnv === "production";
export const isStaging = !isProduction;

function normalizeBaseUrl(url: string): string {
  return url.replace(/\/+$/, "");
}

export const productionUrl = normalizeBaseUrl(
  process.env.NEXT_PUBLIC_PRODUCTION_URL || "https://masqueclima.es"
);

export const stagingUrl = normalizeBaseUrl(
  process.env.NEXT_PUBLIC_SITE_URL || "https://dev.masqueclima.es"
);

export const publicBaseUrl = isProduction ? productionUrl : stagingUrl;

export function absoluteUrl(path = "/"): string {
  const normalizedPath = path.startsWith("/") ? path : `/${path}`;
  return `${publicBaseUrl}${normalizedPath}`;
}

export function hasSmtpConfig(): boolean {
  return Boolean(
    process.env.SMTP_HOST &&
      process.env.SMTP_PORT &&
      process.env.CONTACT_TO &&
      process.env.CONTACT_FROM
  );
}
