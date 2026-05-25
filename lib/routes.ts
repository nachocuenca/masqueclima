import { areaPages } from "@/content/areas/es";
import { blogPosts } from "@/content/blog/es";
import { servicePages } from "@/content/services/es";
import { site } from "@/content/site";
import type { Lang } from "@/content/types";
import { absoluteUrl } from "./env";

export const LANGS = site.langs;
export const DEFAULT_LANG = site.defaultLang;

export function isLang(value: string | undefined): value is Lang {
  return Boolean(value && (LANGS as readonly string[]).includes(value));
}

export function withTrailingSlash(path: string): string {
  if (path === "") {
    return "/";
  }
  const normalized = path.startsWith("/") ? path : `/${path}`;
  return normalized.endsWith("/") ? normalized : `${normalized}/`;
}

export function homePath(lang: Lang): string {
  return `/${lang}/`;
}

export function contactPath(): string {
  return "/es/contacto/";
}

export function servicePath(slug: string): string {
  return `/es/servicios/${slug}/`;
}

export function areaPath(slug: string): string {
  return `/es/zonas/${slug}/`;
}

export function blogIndexPath(): string {
  return "/es/blog/";
}

export function blogPostPath(slug: string): string {
  return `/es/blog/${slug}/`;
}

export function homeAlternates(): Record<string, string> {
  return {
    ...Object.fromEntries(LANGS.map((lang) => [lang, absoluteUrl(homePath(lang))])),
    "x-default": absoluteUrl(homePath(DEFAULT_LANG))
  };
}

export type CanonicalRoute = {
  path: string;
  priority: number;
  changeFrequency: "weekly" | "monthly";
};

export const canonicalRoutes: CanonicalRoute[] = [
  ...LANGS.map((lang) => ({
    path: homePath(lang),
    priority: 1,
    changeFrequency: "weekly" as const
  })),
  { path: contactPath(), priority: 0.7, changeFrequency: "monthly" },
  { path: blogIndexPath(), priority: 0.65, changeFrequency: "weekly" },
  ...servicePages.map((service) => ({
    path: servicePath(service.slug),
    priority: 0.82,
    changeFrequency: "monthly" as const
  })),
  ...areaPages.map((area) => ({
    path: areaPath(area.slug),
    priority: 0.78,
    changeFrequency: "monthly" as const
  })),
  ...blogPosts.map((post) => ({
    path: blogPostPath(post.slug),
    priority: 0.55,
    changeFrequency: "monthly" as const
  }))
];

export const legacyRedirects: Record<string, string> = {
  "/es/servicios/instalacion-aire-acondicionado/": servicePath("aire-acondicionado"),
  "/es/servicios/reparacion-averias/": servicePath("reparacion-aire-acondicionado"),
  "/es/servicios/calefaccion-bomba-de-calor/": servicePath("calefaccion-bomba-calor"),
  "/es/aire-acondicionado-benidorm/": areaPath("benidorm"),
  "/es/aire-acondicionado-finestrat/": areaPath("finestrat"),
  "/es/aire-acondicionado-la-nucia/": areaPath("la-nucia"),
  "/es/aire-acondicionado-altea/": areaPath("altea"),
  "/es/aire-acondicionado-villajoyosa/": areaPath("villajoyosa"),
  "/es/aire-acondicionado-alfaz-del-pi/": areaPath("alfaz-del-pi"),
  "/es/aire-acondicionado-calpe/": areaPath("calpe")
};
