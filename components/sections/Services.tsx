import Link from "next/link";
import { servicePages } from "@/content/services/es";
import type { HomeDictionary } from "@/content/types";
import { servicePath } from "@/lib/routes";

type ServicesProps = {
  dictionary: HomeDictionary;
};

export function Services({ dictionary }: ServicesProps) {
  return (
    <section className="bg-white py-20" id="servicios">
      <div className="section-shell">
        <h2 className="text-3xl font-black text-neutral-950">
          {dictionary.offers?.title ?? "Servicios de climatización y energía"}
        </h2>
        <div className="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {servicePages.map((service) => (
            <article
              className="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
              key={service.slug}
            >
              <h3 className="text-xl font-black text-neutral-950">{service.title}</h3>
              <p className="mt-3 leading-7 text-neutral-700">{service.summary}</p>
              <Link
                className="focus-ring mt-5 inline-flex rounded-full bg-neutral-950 px-5 py-2 text-sm font-bold text-white hover:bg-teal-800"
                href={servicePath(service.slug)}
              >
                {dictionary.cta.quote}
              </Link>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
