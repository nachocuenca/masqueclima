import type { ReactNode } from "react";
import { notFound } from "next/navigation";
import { DrawerShell } from "@/components/lead/DrawerShell";
import { getDictionary } from "@/lib/i18n";
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

  const dictionary = getDictionary(lang);

  return <DrawerShell dictionary={dictionary}>{children}</DrawerShell>;
}
