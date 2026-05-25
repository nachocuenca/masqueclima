import legacyServicesJson from "../legacy/services.es.json";
import es from "../i18n/es";
import type { LegacyService, ServicePage } from "../types";

const legacyServices = legacyServicesJson as LegacyService[];
const bySlug = new Map(legacyServices.map((service) => [service.slug, service]));

function serviceFromLegacy(legacySlug: string, slug: string, title?: string): ServicePage {
  const source = bySlug.get(legacySlug);
  if (!source) {
    throw new Error(`Missing legacy service content: ${legacySlug}`);
  }

  return {
    slug,
    legacySlug,
    title: title ?? source.title,
    summary: source.summary,
    metaTitle: `${title ?? source.title} | +QUECLIMA`,
    metaDescription: source.summary,
    sections: source.sections,
    faq: source.faq.map((item) => ({
      question: item.q,
      answer: item.a
    }))
  };
}

const solarSummary =
  "Diseñamos e instalamos captadores solares para agua caliente sanitaria y apoyo a calefacción.";

export const servicePages: ServicePage[] = [
  serviceFromLegacy(
    "instalacion-aire-acondicionado",
    "aire-acondicionado",
    "Aire acondicionado: instalación split, multisplit y conductos"
  ),
  serviceFromLegacy("mantenimiento-climatizacion", "mantenimiento-climatizacion"),
  serviceFromLegacy(
    "reparacion-averias",
    "reparacion-aire-acondicionado",
    "Reparación de aire acondicionado"
  ),
  serviceFromLegacy(
    "calefaccion-bomba-de-calor",
    "calefaccion-bomba-calor",
    "Calefacción por bomba de calor y aerotermia"
  ),
  {
    slug: "energia-solar",
    title: "Energía solar térmica",
    summary: solarSummary,
    metaTitle: "Energía solar térmica en Alicante | +QUECLIMA",
    metaDescription:
      "Instalación de captadores solares para agua caliente sanitaria y apoyo a calefacción en Alicante con +QUECLIMA.",
    sections: [
      {
        h: "Instalaciones solares térmicas",
        p: es.faq.a5
      },
      {
        h: "Servicio en Alicante",
        p: es.coverage.text
      },
      {
        h: "Normativa y garantía",
        p: `${es.about.cert} ${es.faq.a7}`
      }
    ],
    faq: [
      {
        question: es.faq.q5,
        answer: es.faq.a5
      }
    ]
  }
];

export function getServicePage(slug: string): ServicePage | undefined {
  return servicePages.find((service) => service.slug === slug);
}
