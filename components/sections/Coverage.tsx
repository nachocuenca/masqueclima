import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type CoverageProps = {
  dictionary: HomeDictionary;
};

const LOCALITIES = [
  "Benidorm", "Altea", "La Nucía", "Calpe", "Finestrat",
  "Alfaz del Pi", "Villajoyosa", "Moraira", "Dénia", "Jávea"
];

export function Coverage({ dictionary }: CoverageProps) {
  return (
    <section className="bg-white py-20" id="zona">
      <div className="section-shell">
        <div className="mb-10 text-center">
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-700">
            Área de servicio
          </p>
          <h2 className="text-3xl font-black text-neutral-950 sm:text-4xl">
            {dictionary.coverage.title}
          </h2>
          <p className="mx-auto mt-4 max-w-xl text-base leading-8 text-neutral-600">
            {dictionary.coverage.text}
          </p>
        </div>

        <div className="grid gap-8 lg:grid-cols-[1fr_1.4fr] lg:items-start">
          {/* Left: localities + CTA */}
          <div>
            <div className="flex flex-wrap gap-2">
              {LOCALITIES.map((loc) => (
                <span
                  className="rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-800"
                  key={loc}
                >
                  📍 {loc}
                </span>
              ))}
              <span className="rounded-full border border-neutral-200 bg-neutral-50 px-4 py-2 text-sm font-semibold text-neutral-600">
                + toda la provincia
              </span>
            </div>

            <OpenDrawerButton className="mt-6 w-full rounded-xl bg-teal-700 px-6 py-4 font-black text-white shadow shadow-teal-700/20 transition hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-teal-700 sm:w-auto">
              Consultar disponibilidad en mi zona →
            </OpenDrawerButton>
          </div>

          {/* Right: map */}
          <div className="overflow-hidden rounded-2xl border border-neutral-200 shadow-sm">
            <iframe
              allowFullScreen
              className="h-72 w-full lg:h-80"
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
              src={site.mapEmbed}
              title={dictionary.coverage.map_title}
            />
          </div>
        </div>
      </div>
    </section>
  );
}
