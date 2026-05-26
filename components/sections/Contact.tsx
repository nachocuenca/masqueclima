"use client";

import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type ContactProps = {
  dictionary: HomeDictionary;
};

export function Contact({ dictionary }: ContactProps) {
  return (
    <section className="bg-neutral-950 py-20 text-white" id="contacto">
      <div className="section-shell">
        <div className="grid gap-10 lg:grid-cols-2 lg:items-center">
          {/* Left: info */}
          <div>
            <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-400">
              Contacto
            </p>
            <h2 className="mb-5 text-3xl font-black sm:text-4xl">{dictionary.contact.title}</h2>
            <p className="text-lg leading-8 text-neutral-300">
              Instalación, mantenimiento o reparación. Te asesoramos con una propuesta clara y
              honesta.
            </p>

            <dl className="mt-8 space-y-5">
              <div className="flex items-center gap-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700/30">
                  <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" width="18">
                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div>
                  <dt className="text-xs font-bold uppercase tracking-wider text-neutral-400">
                    {dictionary.contact.phone}
                  </dt>
                  <dd>
                    <a className="font-bold text-white transition hover:text-teal-400" href={site.phoneHref}>
                      {site.phone}
                    </a>
                  </dd>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700/30">
                  <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" width="18">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <dt className="text-xs font-bold uppercase tracking-wider text-neutral-400">
                    {dictionary.contact.email}
                  </dt>
                  <dd>
                    <a className="font-bold text-white transition hover:text-teal-400" href={site.emailHref}>
                      {site.email}
                    </a>
                  </dd>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-700/30">
                  <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" width="18">
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <dt className="text-xs font-bold uppercase tracking-wider text-neutral-400">
                    {dictionary.contact.area}
                  </dt>
                  <dd className="font-semibold text-white">{dictionary.contact.area_text}</dd>
                </div>
              </div>
            </dl>
          </div>

          {/* Right: CTA card */}
          <div className="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
            <h3 className="mb-2 text-xl font-black">{dictionary.contact.form_title}</h3>
            <p className="mb-6 text-neutral-300">
              Elige cómo prefieres que te contactemos. Respuesta rápida.
            </p>
            <div className="grid gap-3">
              <OpenDrawerButton className="w-full rounded-xl bg-[#f5b642] px-6 py-4 font-black text-neutral-950 shadow-lg shadow-amber-500/20 transition hover:bg-[#ffc84a] focus-visible:outline-2 focus-visible:outline-white">
                Solicitar presupuesto gratuito
              </OpenDrawerButton>
              <a
                className="flex items-center justify-center gap-2 rounded-xl border-2 border-[#25d366] px-6 py-4 font-bold text-[#25d366] transition hover:bg-[#25d366] hover:text-white focus-visible:outline-2 focus-visible:outline-[#25d366]"
                href={site.whatsappUrl}
                rel="noopener noreferrer"
                target="_blank"
              >
                <svg aria-hidden="true" fill="currentColor" height="20" viewBox="0 0 24 24" width="20">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.126 1.526 5.867L0 24l6.322-1.493C8.02 23.468 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.87 0-3.637-.504-5.165-1.388l-.37-.22-3.752.886.937-3.65-.241-.375C2.658 15.376 2.182 13.736 2.182 12 2.182 6.566 6.566 2.182 12 2.182S21.818 6.566 21.818 12 17.434 21.818 12 21.818z" />
                </svg>
                Escríbenos por WhatsApp
              </a>
              <a
                className="flex items-center justify-center gap-2 rounded-xl border border-white/20 px-6 py-3.5 font-bold text-white transition hover:border-white/40 hover:bg-white/10"
                href={site.phoneHref}
              >
                <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" width="18">
                  <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Llamar: {site.phone}
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}