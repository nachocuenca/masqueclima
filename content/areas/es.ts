import legacyAreasJson from "../legacy/areas.es.json";
import es from "../i18n/es";
import type { AreaPage, LegacyArea } from "../types";

const legacyAreas = legacyAreasJson as LegacyArea[];
const bySlug = new Map(legacyAreas.map((area) => [area.slug, area]));

function areaFromLegacy(slug: string): AreaPage {
  const source = bySlug.get(slug);
  if (!source) {
    throw new Error(`Missing legacy area content: ${slug}`);
  }

  return {
    slug,
    title: source.title,
    intro: source.intro,
    metaTitle: `${source.title} | +QUECLIMA`,
    metaDescription: source.intro,
    points: source.points
  };
}

const defaultPoints =
  bySlug.get("benidorm")?.points ?? [
    "Estudio térmico adaptado al clima mediterráneo",
    "Equipos silenciosos y eficientes (SEER/SCOP altos)",
    "Instalación certificada y garantía 3 años"
  ];

export const areaPages: AreaPage[] = [
  areaFromLegacy("benidorm"),
  areaFromLegacy("finestrat"),
  areaFromLegacy("la-nucia"),
  areaFromLegacy("altea"),
  areaFromLegacy("villajoyosa"),
  areaFromLegacy("alfaz-del-pi"),
  areaFromLegacy("calpe"),
  {
    slug: "marina-baixa",
    title: "Climatización en Marina Baixa",
    intro:
      "Con base en Benidorm, cubrimos la Marina Baixa y el resto de la provincia con desplazamiento rápido.",
    metaTitle: "Climatización en Marina Baixa | +QUECLIMA",
    metaDescription:
      "Instalación, mantenimiento y reparación de climatización en Marina Baixa con desplazamiento rápido desde Benidorm.",
    points: defaultPoints
  },
  {
    slug: "alicante",
    title: "Climatización en Alicante",
    intro: es.coverage.text,
    metaTitle: "Climatización en Alicante | +QUECLIMA",
    metaDescription:
      "Servicio técnico de climatización, calefacción y energía solar en la provincia de Alicante.",
    points: defaultPoints
  }
];

export function getAreaPage(slug: string): AreaPage | undefined {
  return areaPages.find((area) => area.slug === slug);
}
