"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { site } from "@/content/site";
import type { HomeDictionary, Lang } from "@/content/types";
import { contactPath, homePath } from "@/lib/routes";
import { LanguageSwitcher } from "./LanguageSwitcher";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type HeaderProps = {
  dictionary: HomeDictionary;
  lang: Lang;
};

export function Header({ dictionary, lang }: HeaderProps) {
  const [mobileOpen, setMobileOpen] = useState(false);
  const home = homePath(lang);
  const isSpanish = lang === "es";

  const navItems = [
    { href: `${home}#metodo`, label: dictionary.nav.method },
    { href: `${home}#nosotros`, label: dictionary.nav.about },
    { href: `${home}#zona`, label: dictionary.nav.coverage },
    { href: `${home}#faq`, label: dictionary.nav.faq },
    { href: isSpanish ? contactPath() : `${home}#contacto`, label: dictionary.nav.contact }
  ];

  return (
    <header className="sticky top-0 z-40 border-b border-neutral-100 bg-white/97 shadow-sm backdrop-blur-md">
      <div className="section-shell flex min-h-[68px] items-center gap-4 py-2">
        {/* Logo */}
        <Link
          aria-label={site.name}
          className="shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 focus-visible:rounded"
          href={home}
        >
          <Image
            alt={site.name}
            className="h-8 w-auto sm:h-9"
            height={36}
            priority
            src={site.logo}
            width={152}
          />
        </Link>

        {/* Desktop nav */}
        <nav aria-label="Principal" className="hidden flex-1 justify-center md:flex">
          <ul className="flex flex-wrap items-center gap-0.5 text-sm font-semibold text-neutral-600">
            {navItems.map((item) => (
              <li key={item.href}>
                <Link
                  className="rounded-full px-3 py-2 transition hover:bg-teal-50 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-teal-700"
                  href={item.href}
                >
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        {/* Desktop right */}
        <div className="ml-auto flex items-center gap-3">
          <div className="hidden md:block">
            <LanguageSwitcher currentLang={lang} />
          </div>
          <OpenDrawerButton className="hidden rounded-full bg-teal-700 px-5 py-2.5 text-sm font-black text-white shadow shadow-teal-700/20 transition hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 md:inline-flex">
            Presupuesto
          </OpenDrawerButton>

          {/* Mobile: WhatsApp + hamburger */}
          <a
            aria-label="WhatsApp"
            className="flex h-9 w-9 items-center justify-center rounded-full bg-[#25d366] text-white md:hidden"
            href={site.whatsappUrl}
            rel="noopener noreferrer"
            target="_blank"
          >
            <svg aria-hidden="true" fill="currentColor" height="18" viewBox="0 0 24 24" width="18">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.126 1.526 5.867L0 24l6.322-1.493C8.02 23.468 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.87 0-3.637-.504-5.165-1.388l-.37-.22-3.752.886.937-3.65-.241-.375C2.658 15.376 2.182 13.736 2.182 12 2.182 6.566 6.566 2.182 12 2.182S21.818 6.566 21.818 12 17.434 21.818 12 21.818z" />
            </svg>
          </a>

          <button
            aria-controls="mobile-menu"
            aria-expanded={mobileOpen}
            aria-label={mobileOpen ? "Cerrar menú" : "Abrir menú"}
            className="flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-700 transition hover:bg-neutral-50 md:hidden"
            onClick={() => setMobileOpen(!mobileOpen)}
            type="button"
          >
            {mobileOpen ? (
              <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" viewBox="0 0 24 24" width="18">
                <path d="M6 18L18 6M6 6l12 12" />
              </svg>
            ) : (
              <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" viewBox="0 0 24 24" width="18">
                <path d="M3 6h18M3 12h18M3 18h18" />
              </svg>
            )}
          </button>
        </div>
      </div>

      {/* Mobile menu */}
      {mobileOpen && (
        <div className="border-t border-neutral-100 bg-white pb-4 md:hidden" id="mobile-menu">
          <nav aria-label="Menú móvil">
            <ul className="grid py-2">
              {navItems.map((item) => (
                <li key={item.href}>
                  <Link
                    className="block px-6 py-3 font-semibold text-neutral-700 transition hover:bg-teal-50 hover:text-teal-700"
                    href={item.href}
                    onClick={() => setMobileOpen(false)}
                  >
                    {item.label}
                  </Link>
                </li>
              ))}
            </ul>
          </nav>
          <div className="grid gap-2 px-4 pt-2">
            <OpenDrawerButton className="w-full rounded-xl bg-[#f5b642] px-5 py-3.5 text-center font-black text-neutral-950 transition hover:bg-[#ffc84a]">
              Pide presupuesto sin compromiso
            </OpenDrawerButton>
          </div>
          <div className="mt-4 flex justify-center">
            <LanguageSwitcher currentLang={lang} />
          </div>
        </div>
      )}
    </header>
  );
}
