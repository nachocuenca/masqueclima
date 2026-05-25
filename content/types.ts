export type Lang = "es" | "en" | "de" | "nl" | "ru";

export type TextMap = Record<string, string>;

export type HomeDictionary = {
  seo: {
    title: string;
    description: string;
  };
  nav: {
    home: string;
    method: string;
    about: string;
    coverage: string;
    faq: string;
    contact: string;
  };
  hero: {
    title: string;
    subtitle: string;
  };
  cta: {
    quote: string;
    whatsapp: string;
  };
  method: {
    title: string;
    s1: { title: string; text: string };
    s2: { title: string; text: string };
    s3: { title: string; text: string };
    s4: { title: string; text: string };
  };
  about: {
    title: string;
    p1: string;
    p2: string;
    p3: string;
    p4: string;
    cert: string;
  };
  coverage: {
    title: string;
    text: string;
    area_name: string;
    map_title: string;
  };
  faq: TextMap;
  brands: TextMap & { title: string };
  contact: {
    title: string;
    info_title: string;
    phone: string;
    email: string;
    area: string;
    area_text: string;
    hours_label: string;
    hours_text: string;
    form_title: string;
  };
  form: {
    name: string;
    phone: string;
    email: string;
    service: string;
    select: string;
    desc: string;
    submit: string;
    ok: string;
  };
  service: TextMap;
  reviews: TextMap & { title: string; cta: string };
  img: TextMap;
  offers?: TextMap & { title?: string };
  footer: TextMap;
  "404": TextMap;
};

export type FaqItem = {
  question: string;
  answer: string;
};

export type ContentSection = {
  h: string;
  p: string;
};

export type LegacyService = {
  slug: string;
  title: string;
  summary: string;
  sections: ContentSection[];
  faq: Array<{ q: string; a: string }>;
};

export type ServicePage = {
  slug: string;
  legacySlug?: string;
  title: string;
  summary: string;
  metaTitle: string;
  metaDescription: string;
  sections: ContentSection[];
  faq: FaqItem[];
};

export type LegacyArea = {
  slug: string;
  title: string;
  intro: string;
  points: string[];
};

export type AreaPage = {
  slug: string;
  title: string;
  intro: string;
  metaTitle: string;
  metaDescription: string;
  points: string[];
};

export type LegacyGuide = {
  slug: string;
  title: string;
  type: "guide" | "calculator";
  content: string[];
  faq?: Array<{ q: string; a: string }>;
};

export type BlogPost = {
  slug: string;
  title: string;
  summary: string;
  metaTitle: string;
  metaDescription: string;
  body: string[];
  faq: FaqItem[];
};
