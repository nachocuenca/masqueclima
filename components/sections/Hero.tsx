import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type HeroProps = {
  dictionary: HomeDictionary;
};

export function Hero({ dictionary }: HeroProps) {
  return (
    <section
      className="relative isolate flex min-h-[calc(100vh-80px)] items-center overflow-hidden bg-neutral-950 text-white"
      id="inicio"
    >
      <video
        autoPlay
        className="absolute inset-0 h-full w-full object-cover opacity-45"
        loop
        muted
        playsInline
        poster={site.heroPoster}
        preload="metadata"
      >
        <source src={site.heroVideo} type="video/mp4" />
      </video>
      <div className="absolute inset-0 bg-neutral-950/35" />

      <div className="section-shell relative py-24">
        <div className="max-w-3xl">
          <p className="mb-5 inline-flex rounded-full bg-white/90 px-4 py-2 text-sm font-bold text-teal-800">
            {site.area}
          </p>
          <h1 className="text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">
            {dictionary.hero.title}
          </h1>
          <p className="mt-6 max-w-2xl text-lg leading-8 text-neutral-100">
            {dictionary.hero.subtitle}
          </p>
          <div className="mt-9 flex flex-wrap gap-3">
            <a
              className="focus-ring rounded-full bg-[#f5b642] px-6 py-3 font-bold text-neutral-950 transition hover:bg-[#ffd27a]"
              href="#contacto"
            >
              {dictionary.cta.quote}
            </a>
            <a
              className="focus-ring rounded-full border border-white/70 px-6 py-3 font-bold text-white transition hover:bg-white hover:text-neutral-950"
              href={site.whatsappUrl}
              rel="noopener noreferrer"
              target="_blank"
            >
              {dictionary.cta.whatsapp}
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
