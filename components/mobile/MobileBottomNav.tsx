"use client";

import Link from "next/link";
import { useLeadDrawer } from "@/components/lead/LeadDrawerProvider";
import type { Lang } from "@/content/types";
import { homePath } from "@/lib/routes";

type MobileBottomNavProps = {
  lang: Lang;
};

export function MobileBottomNav({ lang }: MobileBottomNavProps) {
  const { openDrawer } = useLeadDrawer();
  const home = homePath(lang);

  return (
    <nav
      aria-label="Navegación rápida"
      className="fixed inset-x-0 bottom-0 z-40 flex items-stretch border-t border-neutral-200 bg-white shadow-[0_-4px_24px_rgba(0,0,0,0.08)] md:hidden"
      style={{ paddingBottom: "env(safe-area-inset-bottom, 0px)" }}
    >
      <Link
        className="flex flex-1 flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-semibold text-neutral-500 transition hover:text-teal-700 active:bg-neutral-50"
        href={home}
      >
        <svg aria-hidden="true" fill="none" height="22" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" viewBox="0 0 24 24" width="22">
          <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Inicio
      </Link>

      <Link
        className="flex flex-1 flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-semibold text-neutral-500 transition hover:text-teal-700 active:bg-neutral-50"
        href={`${home}#servicios`}
      >
        <svg aria-hidden="true" fill="none" height="22" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" viewBox="0 0 24 24" width="22">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Servicios
      </Link>

      {/* CTA central elevado */}
      <div className="relative flex items-center justify-center px-3">
        <button
          aria-label="Solicitar presupuesto"
          className="absolute -top-5 flex h-14 w-14 flex-col items-center justify-center rounded-full bg-[#f5b642] shadow-lg shadow-amber-300/50 transition active:scale-95"
          onClick={() => openDrawer()}
          type="button"
        >
          <svg aria-hidden="true" fill="none" height="24" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.2" viewBox="0 0 24 24" width="24">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span className="mt-0.5 text-[8px] font-black leading-none text-neutral-950">Pedir</span>
        </button>
        <div className="h-14 w-14" />
      </div>

      <Link
        className="flex flex-1 flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-semibold text-neutral-500 transition hover:text-teal-700 active:bg-neutral-50"
        href={`${home}#zona`}
      >
        <svg aria-hidden="true" fill="none" height="22" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" viewBox="0 0 24 24" width="22">
          <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Zona
      </Link>

      <Link
        className="flex flex-1 flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-semibold text-neutral-500 transition hover:text-teal-700 active:bg-neutral-50"
        href={`${home}#contacto`}
      >
        <svg aria-hidden="true" fill="none" height="22" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" viewBox="0 0 24 24" width="22">
          <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
        Contacto
      </Link>
    </nav>
  );
}
