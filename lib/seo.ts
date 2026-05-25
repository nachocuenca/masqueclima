import type { Metadata } from "next";
import { localeMap, site } from "@/content/site";
import type { Lang } from "@/content/types";
import { absoluteUrl, isProduction } from "./env";
import { homeAlternates } from "./routes";

type MetadataInput = {
  title: string;
  description: string;
  path: string;
  lang?: Lang;
  alternates?: Record<string, string>;
};

export function buildMetadata({
  title,
  description,
  path,
  lang = "es",
  alternates
}: MetadataInput): Metadata {
  const canonical = absoluteUrl(path);
  const image = absoluteUrl(site.ogImage);

  return {
    metadataBase: new URL(absoluteUrl("/")),
    title,
    description,
    alternates: {
      canonical,
      ...(alternates ? { languages: alternates } : {})
    },
    robots: isProduction
      ? {
          index: true,
          follow: true
        }
      : {
          index: false,
          follow: false,
          nocache: true,
          googleBot: {
            index: false,
            follow: false,
            noimageindex: true
          }
        },
    openGraph: {
      type: "website",
      locale: localeMap[lang],
      alternateLocale: Object.entries(localeMap)
        .filter(([key]) => key !== lang)
        .map(([, locale]) => locale),
      url: canonical,
      title,
      description,
      siteName: site.name,
      images: [
        {
          url: image,
          width: 1200,
          height: 630,
          alt: site.name
        }
      ]
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
      images: [image]
    }
  };
}

export function buildHomeMetadata(lang: Lang): Metadata {
  return buildMetadata({
    title: site.homeMeta.title[lang],
    description: site.homeMeta.description[lang],
    path: `/${lang}/`,
    lang,
    alternates: homeAlternates()
  });
}
