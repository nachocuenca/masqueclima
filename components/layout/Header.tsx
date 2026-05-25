import Image from "next/image";
import Link from "next/link";
import { site } from "@/content/site";
import type { HomeDictionary, Lang } from "@/content/types";
import { contactPath, homePath } from "@/lib/routes";
import { LanguageSwitcher } from "./LanguageSwitcher";

type HeaderProps = {
  dictionary: HomeDictionary;
  lang: Lang;
};

export function Header({ dictionary, lang }: HeaderProps) {
  const home = homePath(lang);
  const isSpanish = lang === "es";

  const navItems = [
    { href: `${home}#inicio`, label: dictionary.nav.home },
    { href: `${home}#metodo`, label: dictionary.nav.method },
    { href: `${home}#nosotros`, label: dictionary.nav.about },
    { href: `${home}#zona`, label: dictionary.nav.coverage },
    { href: `${home}#faq`, label: dictionary.nav.faq },
    { href: isSpanish ? contactPath() : `${home}#contacto`, label: dictionary.nav.contact }
  ];

  return (
    <header className="sticky top-0 z-40 border-b border-neutral-200 bg-white/95 backdrop-blur">
      <div className="section-shell flex min-h-20 items-center gap-5 py-3">
        <Link className="focus-ring shrink-0 rounded-sm" href={home} aria-label={site.name}>
          <Image
            alt={site.name}
            height={60}
            priority
            src={site.logo}
            width={180}
            className="h-14 w-auto"
          />
        </Link>

        <nav className="hidden flex-1 justify-center md:flex" aria-label="Principal">
          <ul className="flex flex-wrap items-center justify-center gap-1 text-sm font-semibold text-neutral-700">
            {navItems.map((item) => (
              <li key={item.href}>
                <Link
                  className="focus-ring rounded-full px-3 py-2 hover:bg-neutral-100 hover:text-teal-700"
                  href={item.href}
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div className="ml-auto">
          <LanguageSwitcher currentLang={lang} />
        </div>
      </div>
    </header>
  );
}
