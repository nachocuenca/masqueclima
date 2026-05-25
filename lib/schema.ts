import { site } from "@/content/site";
import type { AreaPage, FaqItem, ServicePage } from "@/content/types";
import { absoluteUrl } from "./env";

type BreadcrumbItem = {
  name: string;
  path: string;
};

export function organizationSchema() {
  return {
    "@context": "https://schema.org",
    "@type": "Organization",
    name: site.name,
    alternateName: site.altName,
    url: absoluteUrl("/es/"),
    logo: absoluteUrl(site.logo),
    email: site.email,
    telephone: site.phone
  };
}

export function hvacBusinessSchema() {
  return {
    "@context": "https://schema.org",
    "@type": "HVACBusiness",
    name: site.name,
    alternateName: site.altName,
    url: absoluteUrl("/es/"),
    logo: absoluteUrl(site.logo),
    image: absoluteUrl(site.ogImage),
    telephone: site.phone,
    email: site.email,
    areaServed: {
      "@type": "AdministrativeArea",
      name: site.area
    },
    serviceType: [
      "Instalación aire acondicionado",
      "Mantenimiento climatización",
      "Reparación aire acondicionado",
      "Calefacción por bomba de calor",
      "Energía solar térmica"
    ],
    contactPoint: {
      "@type": "ContactPoint",
      telephone: site.phone,
      contactType: "customer service",
      availableLanguage: site.langs
    }
  };
}

export function websiteSchema() {
  return {
    "@context": "https://schema.org",
    "@type": "WebSite",
    name: site.name,
    url: absoluteUrl("/es/")
  };
}

export function breadcrumbSchema(items: BreadcrumbItem[]) {
  return {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: items.map((item, index) => ({
      "@type": "ListItem",
      position: index + 1,
      name: item.name,
      item: absoluteUrl(item.path)
    }))
  };
}

export function faqSchema(faqs: FaqItem[]) {
  return {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    mainEntity: faqs.map((faq) => ({
      "@type": "Question",
      name: faq.question,
      acceptedAnswer: {
        "@type": "Answer",
        text: faq.answer
      }
    }))
  };
}

export function serviceSchema(service: ServicePage, path: string) {
  return {
    "@context": "https://schema.org",
    "@type": "Service",
    name: service.title,
    description: service.summary,
    url: absoluteUrl(path),
    provider: {
      "@type": "HVACBusiness",
      name: site.name,
      telephone: site.phone,
      url: absoluteUrl("/es/")
    },
    areaServed: {
      "@type": "AdministrativeArea",
      name: site.area
    }
  };
}

export function areaServedSchema(area: AreaPage, path: string) {
  return {
    "@context": "https://schema.org",
    "@type": "Service",
    name: area.title,
    description: area.intro,
    url: absoluteUrl(path),
    provider: {
      "@type": "HVACBusiness",
      name: site.name,
      telephone: site.phone,
      url: absoluteUrl("/es/")
    },
    areaServed: {
      "@type": "Place",
      name: area.title.replace("Climatización en ", "")
    }
  };
}
