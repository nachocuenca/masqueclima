import type { Metadata } from "next";
import type { ReactNode } from "react";
import { headers } from "next/headers";
import { LANGS } from "@/lib/routes";
import "./globals.css";

export const metadata: Metadata = {
  icons: {
    icon: [
      { url: "/assets/img/favicon.ico", sizes: "any" },
      { url: "/assets/img/favicon-32x32.png", sizes: "32x32", type: "image/png" }
    ],
    shortcut: "/assets/img/favicon.ico"
  }
};

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
