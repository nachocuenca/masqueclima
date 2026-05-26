import Link from "next/link";
import { servicePages } from "@/content/services/es";
import type { HomeDictionary } from "@/content/types";
import { servicePath } from "@/lib/routes";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type ServicesProps = {
  dictionary: HomeDictionary;
};

const SERVICE_ICONS: Record<string, string> = {
  "aire-acondicionado": "❄️",
  "mantenimiento-climatizacion": "🔧",
  "reparacion-aire-acondicionado": "⚡",
  "calefaccion-bomba-calor": "🔥",
  "energia-solar": "☀️",
  electricidad: "💡",
  fontaneria: "🚿"
};

const SERVICE_VALUES: Record<string, string> = {
  "aire-acondicionado": "climatizacion",
  "mantenimiento-climatizacion": "mantenimiento",
  "reparacion-aire-acondicionado": "reparacion",
  "calefaccion-bomba-calor": "calefaccion",
  "energia-solar": "solar",
  electricidad: "electricidad",
  fontaneria: "fontaneria"
};

export function Services({ dictionary }: ServicesProps) {
  return (
    <section className="bg-neutral-50 py-20" id="servicios">
      <div className="section-shell">
        <div className="mb-12 text-center">
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-700">
            Lo que hacemos
          </p>
          <h2 className="text-3xl font-black text-neutral-950 sm:text-4xl">
            {dictionary.offers?.title ?? "Servicios de climatización y energía"}
          </h2>
        </div>

        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {servicePages.map((service) => {
            const icon = SERVICE_ICONS[service.slug] ?? "🌡️";
            const serviceValue = SERVICE_VALUES[service.slug] ?? "";

            return (
              <article
                className="group flex flex-col rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-700/5"
                key={service.slug}
              >
                <div className="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl transition-colors group-hover:bg-teal-100">
                  {icon}
                </div>
                <h3 className="text-lg font-black leading-snug text-neutral-950">{service.title}</h3>
                <p className="mt-3 flex-1 text-sm leading-7 text-neutral-600">{service.summary}</p>
                <div className="mt-5 flex items-center gap-2">
                  <OpenDrawerButton
                    className="flex-1 rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700"
                    service={serviceValue}
                  >
                    Solicitar presupuesto
                  </OpenDrawerButton>
                  <Link
                    className="flex h-10 w-10 items-center justify-center rounded-xl border border-neutral-200 text-neutral-500 transition hover:border-teal-300 hover:text-teal-700 focus-visible:outline-2 focus-visible:outline-teal-700"
                    href={servicePath(service.slug)}
                    title={`Más info: ${service.title}`}
                  >
                    <svg aria-hidden="true" fill="none" height="16" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" width="16">
                      <path d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </div>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
