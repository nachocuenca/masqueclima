import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { FAQ } from "@/components/sections/FAQ";
import { Breadcrumbs } from "@/components/seo/Breadcrumbs";
import { JsonLd } from "@/components/seo/JsonLd";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { WhatsAppFloating } from "@/components/ui/WhatsAppFloating";
import { blogPosts, getBlogPost } from "@/content/blog/es";
import es from "@/content/i18n/es";
import { blogIndexPath, blogPostPath } from "@/lib/routes";
import { buildMetadata } from "@/lib/seo";
import { breadcrumbSchema, faqSchema } from "@/lib/schema";

type BlogPostProps = {
  params: Promise<{ lang: string; slug: string }>;
};

export function generateStaticParams() {
  return blogPosts.map((post) => ({ lang: "es", slug: post.slug }));
}

export async function generateMetadata({ params }: BlogPostProps): Promise<Metadata> {
  const { lang, slug } = await params;
  const post = lang === "es" ? getBlogPost(slug) : undefined;
  if (!post) {
    return {};
  }

  return buildMetadata({
    title: post.metaTitle,
    description: post.metaDescription,
    path: blogPostPath(post.slug),
    lang: "es"
  });
}

export default async function BlogPostPage({ params }: BlogPostProps) {
  const { lang, slug } = await params;
  const post = lang === "es" ? getBlogPost(slug) : undefined;
  if (!post) {
    notFound();
  }

  const path = blogPostPath(post.slug);
  const breadcrumbs = [
    { label: es.nav.home, href: "/es/" },
    { label: "Blog", href: blogIndexPath() },
    { label: post.title, href: path }
  ];

  return (
    <>
      <JsonLd
        data={breadcrumbSchema(breadcrumbs.map((item) => ({ name: item.label, path: item.href })))}
      />
      {post.faq.length > 0 ? <JsonLd data={faqSchema(post.faq)} /> : null}
      <Header dictionary={es} lang="es" />
      <main>
        <Breadcrumbs items={breadcrumbs} />
        <article className="section-shell py-16">
          <h1 className="max-w-4xl text-4xl font-black leading-tight text-neutral-950">
            {post.title}
          </h1>
          <div className="mt-8 max-w-3xl space-y-5 text-lg leading-8 text-neutral-700">
            {post.body.map((paragraph) => (
              <p key={paragraph}>{paragraph}</p>
            ))}
          </div>
        </article>
        {post.faq.length > 0 ? <FAQ faqs={post.faq} title={es.faq.title} /> : null}
      </main>
      <Footer dictionary={es} />
      <WhatsAppFloating label={es.cta.whatsapp} />
    </>
  );
}
