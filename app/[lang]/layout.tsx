import type { ReactNode } from "react";
import { notFound } from "next/navigation";
import { isLang, LANGS } from "@/lib/routes";

type LangLayoutProps = {
  children: ReactNode;
  params: Promise<{ lang: string }>;
};

export function generateStaticParams() {
  return LANGS.map((lang) => ({ lang }));
}

export default async function LangLayout({ children, params }: LangLayoutProps) {
  const { lang } = await params;

  if (!isLang(lang)) {
    notFound();
  }

  return children;
}
