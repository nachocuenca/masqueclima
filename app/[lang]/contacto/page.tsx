import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Contact } from "@/components/sections/Contact";
import { Breadcrumbs } from "@/components/seo/Breadcrumbs";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import es from "@/content/i18n/es";
import { buildMetadata } from "@/lib/seo";
import { breadcrumbSchema, hvacBusinessSchema, organizationSchema } from "@/lib/schema";
import { contactPath } from "@/lib/routes";

type ContactPageProps = {
  params: Promise<{ lang: string }>;
};

export function generateStaticParams() {
  return [{ lang: "es" }];
}

export async function generateMetadata({ params }: ContactPageProps): Promise<Metadata> {
  const { lang } = await params;
  if (lang !== "es") {
    return {};
  }

  return buildMetadata({
    title: `${es.contact.title} | +QUECLIMA`,
    description: es.contact.area_text,
    path: contactPath(),
    lang: "es"
  });
}

export default async function ContactPage({ params }: ContactPageProps) {
  const { lang } = await params;
  if (lang !== "es") {
    notFound();
  }

  const breadcrumbs = [
    { label: es.nav.home, href: "/es/" },
    { label: es.nav.contact, href: contactPath() }
  ];

  return (
    <>
      <JsonLd data={organizationSchema()} />
      <JsonLd data={hvacBusinessSchema()} />
      <JsonLd
        data={breadcrumbSchema(breadcrumbs.map((item) => ({ name: item.label, path: item.href })))}
      />
      <Header dictionary={es} lang="es" />
      <main>
        <Breadcrumbs items={breadcrumbs} />
        <section className="section-shell pt-16">
          <h1 className="max-w-4xl text-4xl font-black leading-tight text-neutral-950">
            {es.contact.title}
          </h1>
          <p className="mt-4 max-w-2xl text-lg leading-8 text-neutral-700">
            {es.contact.area_text}
          </p>
        </section>
        <Contact dictionary={es} />
      </main>
      <Footer dictionary={es} />
      <WhatsAppFloating label={es.cta.whatsapp} />
    </>
  );
}
