import guidesJson from "../legacy/guides.es.json";
import type { BlogPost, LegacyGuide } from "../types";

const guides = guidesJson as LegacyGuide[];

export const blogPosts: BlogPost[] = guides.map((guide) => ({
  slug: guide.slug,
  title: guide.title,
  summary: guide.content[0] ?? guide.title,
  metaTitle: `${guide.title} | +QUECLIMA`,
  metaDescription: guide.content[0] ?? guide.title,
  body: guide.content,
  faq:
    guide.faq?.map((item) => ({
      question: item.q,
      answer: item.a
    })) ?? []
}));

export function getBlogPost(slug: string): BlogPost | undefined {
  return blogPosts.find((post) => post.slug === slug);
}
