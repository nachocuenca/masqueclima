import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Breadcrumbs } from "@/components/seo/Breadcrumbs";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import { blogPosts } from "@/content/blog/es";
import es from "@/content/i18n/es";
import { blogIndexPath, blogPostPath } from "@/lib/routes";
import { buildMetadata } from "@/lib/seo";
import { breadcrumbSchema } from "@/lib/schema";

type BlogIndexProps = {
  params: Promise<{ lang: string }>;
};

export function generateStaticParams() {
  return [{ lang: "es" }];
}

export async function generateMetadata({ params }: BlogIndexProps): Promise<Metadata> {
  const { lang } = await params;
  if (lang !== "es") {
    return {};
  }

  return buildMetadata({
    title: "Blog de climatización | +QUECLIMA",
    description:
      "Guías prácticas sobre aire acondicionado, mantenimiento, consumo y elección de equipos.",
    path: blogIndexPath(),
    lang: "es"
  });
}

export default async function BlogIndex({ params }: BlogIndexProps) {
  const { lang } = await params;
  if (lang !== "es") {
    notFound();
  }

  const breadcrumbs = [
    { label: es.nav.home, href: "/es/" },
    { label: "Blog", href: blogIndexPath() }
  ];

  return (
    <>
      <JsonLd
        data={breadcrumbSchema(breadcrumbs.map((item) => ({ name: item.label, path: item.href })))}
      />
      <Header dictionary={es} lang="es" />
      <main>
        <Breadcrumbs items={breadcrumbs} />
        <section className="section-shell py-16">
          <h1 className="text-4xl font-black text-neutral-950">Blog</h1>
          <div className="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {blogPosts.map((post) => (
              <article
                className="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
                key={post.slug}
              >
                <h2 className="text-xl font-black text-neutral-950">{post.title}</h2>
                <p className="mt-3 leading-7 text-neutral-700">{post.summary}</p>
                <Link
                  className="focus-ring mt-5 inline-flex rounded-full bg-neutral-950 px-5 py-2 text-sm font-bold text-white hover:bg-teal-800"
                  href={blogPostPath(post.slug)}
                >
                  Leer guía
                </Link>
              </article>
            ))}
          </div>
        </section>
      </main>
      <Footer dictionary={es} />
      <WhatsAppFloating label={es.cta.whatsapp} />
    </>
  );
}
