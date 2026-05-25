import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Contact } from "@/components/sections/Contact";
import { FAQ } from "@/components/sections/FAQ";
import { Breadcrumbs } from "@/components/seo/Breadcrumbs";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import es from "@/content/i18n/es";
import { getServicePage, servicePages } from "@/content/services/es";
import { buildMetadata } from "@/lib/seo";
import {
  breadcrumbSchema,
  faqSchema,
  hvacBusinessSchema,
  serviceSchema
} from "@/lib/schema";
import { servicePath } from "@/lib/routes";

type ServicePageProps = {
  params: Promise<{ lang: string; slug: string }>;
};

export function generateStaticParams() {
  return servicePages.map((service) => ({ lang: "es", slug: service.slug }));
}

export async function generateMetadata({ params }: ServicePageProps): Promise<Metadata> {
  const { lang, slug } = await params;
  const service = lang === "es" ? getServicePage(slug) : undefined;
  if (!service) {
    return {};
  }

  return buildMetadata({
    title: service.metaTitle,
    description: service.metaDescription,
    path: servicePath(service.slug),
    lang: "es"
  });
}

export default async function ServicePage({ params }: ServicePageProps) {
  const { lang, slug } = await params;
  const service = lang === "es" ? getServicePage(slug) : undefined;
  if (!service) {
    notFound();
  }

  const path = servicePath(service.slug);
  const breadcrumbs = [
    { label: es.nav.home, href: "/es/" },
    { label: "Servicios", href: "/es/#servicios" },
    { label: service.title, href: path }
  ];

  return (
    <>
      <JsonLd data={hvacBusinessSchema()} />
      <JsonLd data={serviceSchema(service, path)} />
      <JsonLd
        data={breadcrumbSchema(breadcrumbs.map((item) => ({ name: item.label, path: item.href })))}
      />
      {service.faq.length > 0 ? <JsonLd data={faqSchema(service.faq)} /> : null}
      <Header dictionary={es} lang="es" />
      <main>
        <Breadcrumbs items={breadcrumbs} />
        <section className="section-shell py-16">
          <h1 className="max-w-4xl text-4xl font-black leading-tight text-neutral-950">
            {service.title}
          </h1>
          <p className="mt-5 max-w-3xl text-xl leading-8 text-neutral-700">{service.summary}</p>
          <div className="mt-10 grid gap-5 lg:grid-cols-2">
            {service.sections.map((section) => (
              <article
                className="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
                key={section.h}
              >
                <h2 className="text-2xl font-black text-neutral-950">{section.h}</h2>
                <p className="mt-4 leading-7 text-neutral-700">{section.p}</p>
              </article>
            ))}
          </div>
        </section>
        {service.faq.length > 0 ? <FAQ faqs={service.faq} title={es.faq.title} /> : null}
        <Contact dictionary={es} />
      </main>
      <Footer dictionary={es} />
      <WhatsAppFloating label={es.cta.whatsapp} />
    </>
  );
}
