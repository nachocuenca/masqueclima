import type { MetadataRoute } from "next";
import { absoluteUrl, isProduction } from "@/lib/env";

export default function robots(): MetadataRoute.Robots {
  if (!isProduction) {
    return {
      rules: {
        userAgent: "*",
        disallow: "/"
      }
    };
  }

  return {
    rules: {
      userAgent: "*",
      allow: "/"
    },
    sitemap: absoluteUrl("/sitemap.xml")
  };
}
