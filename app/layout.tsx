import type { ReactNode } from "react";
import { headers } from "next/headers";
import { LANGS } from "@/lib/routes";
import "./globals.css";

type RootLayoutProps = {
  children: ReactNode;
};

export default async function RootLayout({ children }: RootLayoutProps) {
  const headerList = await headers();
  const pathname = headerList.get("x-pathname") || "/es/";
  const candidateLang = pathname.split("/").filter(Boolean)[0];
  const htmlLang = (LANGS as readonly string[]).includes(candidateLang) ? candidateLang : "es";

  return (
    <html lang={htmlLang}>
      <body>{children}</body>
    </html>
  );
}
