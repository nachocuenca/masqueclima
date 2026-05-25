import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type CoverageProps = {
  dictionary: HomeDictionary;
};

export function Coverage({ dictionary }: CoverageProps) {
  return (
    <section className="bg-white py-20" id="zona">
      <div className="section-shell grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
          <h2 className="text-3xl font-black text-neutral-950">{dictionary.coverage.title}</h2>
          <p className="mt-5 text-lg font-semibold leading-8 text-neutral-700">
            {dictionary.coverage.text}
          </p>
        </div>
        <div className="overflow-hidden rounded-lg border border-neutral-200 shadow-sm">
          <iframe
            allowFullScreen
            className="h-80 w-full"
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
            src={site.mapEmbed}
            title={dictionary.coverage.map_title}
          />
        </div>
      </div>
    </section>
  );
}
