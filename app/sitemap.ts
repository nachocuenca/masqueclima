import type { MetadataRoute } from "next";
import { absoluteUrl, isProduction } from "@/lib/env";
import { canonicalRoutes } from "@/lib/routes";

export default function sitemap(): MetadataRoute.Sitemap {
  if (!isProduction) {
    return [];
  }

  const lastModified = new Date();

  return canonicalRoutes.map((route) => ({
    url: absoluteUrl(route.path),
    lastModified,
    changeFrequency: route.changeFrequency,
    priority: route.priority
  }));
}
