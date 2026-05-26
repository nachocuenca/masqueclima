import type { Metadata } from "next";
import { About } from "@/components/sections/About";
import { Brands } from "@/components/sections/Brands";
import { Contact } from "@/components/sections/Contact";
import { Coverage } from "@/components/sections/Coverage";
import { FAQ } from "@/components/sections/FAQ";
import { Hero } from "@/components/sections/Hero";
import { Method } from "@/components/sections/Method";
import { Reviews } from "@/components/sections/Reviews";
import { Services } from "@/components/sections/Services";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import { MobileBottomNav } from "@/components/mobile/MobileBottomNav";
import { getDictionary, getHomeFaqs } from "@/lib/i18n";
import { buildHomeMetadata } from "@/lib/seo";
import {
  breadcrumbSchema,
  faqSchema,
  hvacBusinessSchema,
  organizationSchema,
  websiteSchema
} from "@/lib/schema";
import { isLang, LANGS, homePath } from "@/lib/routes";
import { notFound } from "next/navigation";

type HomePageProps = {
  params: Promise<{ lang: string }>;
};

export function generateStaticParams() {
  return LANGS.map((lang) => ({ lang }));
}

export async function generateMetadata({ params }: HomePageProps): Promise<Metadata> {
  const { lang } = await params;
  if (!isLang(lang)) {
    return {};
  }

  return buildHomeMetadata(lang);
}

export default async function HomePage({ params }: HomePageProps) {
  const { lang } = await params;
  if (!isLang(lang)) {
    notFound();
  }

  const dictionary = getDictionary(lang);
  const faqs = getHomeFaqs(dictionary);

  return (
    <>
      <JsonLd data={organizationSchema()} />
      <JsonLd data={hvacBusinessSchema()} />
      <JsonLd data={websiteSchema()} />
      <JsonLd
        data={breadcrumbSchema([
          {
            name: dictionary.nav.home,
            path: homePath(lang)
          }
        ])}
      />
      <JsonLd data={faqSchema(faqs)} />
      <Header dictionary={dictionary} lang={lang} />
      <main className="pb-20 md:pb-0">
        <Hero dictionary={dictionary} />
        <Method dictionary={dictionary} />
        <Reviews dictionary={dictionary} lang={lang} />
        <About dictionary={dictionary} />
        {lang === "es" ? <Services dictionary={dictionary} /> : null}
        <Coverage dictionary={dictionary} />
        <FAQ dictionary={dictionary} faqs={faqs} title={dictionary.faq.title} />
        <Brands dictionary={dictionary} />
        <Contact dictionary={dictionary} />
      </main>
      <Footer dictionary={dictionary} />
      <WhatsAppFloating label={dictionary.cta.whatsapp} />
      <MobileBottomNav lang={lang} />
    </>
  );
}
