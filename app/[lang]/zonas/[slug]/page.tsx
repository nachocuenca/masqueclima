import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Contact } from "@/components/sections/Contact";
import { Breadcrumbs } from "@/components/seo/Breadcrumbs";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import { areaPages, getAreaPage } from "@/content/areas/es";
import es from "@/content/i18n/es";
import { areaPath } from "@/lib/routes";
import { buildMetadata } from "@/lib/seo";
import { areaServedSchema, breadcrumbSchema, hvacBusinessSchema } from "@/lib/schema";

type AreaPageProps = {
  params: Promise<{ lang: string; slug: string }>;
};

export function generateStaticParams() {
  return areaPages.map((area) => ({ lang: "es", slug: area.slug }));
}

export async function generateMetadata({ params }: AreaPageProps): Promise<Metadata> {
  const { lang, slug } = await params;
  const area = lang === "es" ? getAreaPage(slug) : undefined;
  if (!area) {
    return {};
  }

  return buildMetadata({
    title: area.metaTitle,
    description: area.metaDescription,
    path: areaPath(area.slug),
    lang: "es"
  });
}

export default async function AreaPage({ params }: AreaPageProps) {
  const { lang, slug } = await params;
  const area = lang === "es" ? getAreaPage(slug) : undefined;
  if (!area) {
    notFound();
  }

  const path = areaPath(area.slug);
  const breadcrumbs = [
    { label: es.nav.home, href: "/es/" },
    { label: "Zonas", href: "/es/#zona" },
    { label: area.title, href: path }
  ];

  return (
    <>
      <JsonLd data={hvacBusinessSchema()} />
      <JsonLd data={areaServedSchema(area, path)} />
      <JsonLd
        data={breadcrumbSchema(breadcrumbs.map((item) => ({ name: item.label, path: item.href })))}
      />
      <Header dictionary={es} lang="es" />
      <main>
        <Breadcrumbs items={breadcrumbs} />
        <section className="section-shell py-16">
          <h1 className="max-w-4xl text-4xl font-black leading-tight text-neutral-950">
            {area.title}
          </h1>
          <p className="mt-5 max-w-3xl text-xl leading-8 text-neutral-700">{area.intro}</p>
          <ul className="mt-10 grid gap-4 md:grid-cols-3">
            {area.points.map((point) => (
              <li
                className="rounded-lg border border-neutral-200 bg-white p-5 font-semibold leading-7 text-neutral-700 shadow-sm"
                key={point}
              >
                {point}
              </li>
            ))}
          </ul>
        </section>
        <Contact dictionary={es} />
      </main>
      <Footer dictionary={es} />
      <WhatsAppFloating label={es.cta.whatsapp} />
    </>
  );
}
