import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";
import { OpenDrawerButton } from "@/components/lead/OpenDrawerButton";

type HeroProps = {
  dictionary: HomeDictionary;
};

const TRUST_BADGES = [
  { icon: "📍", text: "Benidorm y Marina Baixa" },
  { icon: "✅", text: "Instaladores certificados" },
  { icon: "🛡️", text: "Garantía 3 años" },
  { icon: "⚡", text: "Respuesta rápida" }
];

export function Hero({ dictionary }: HeroProps) {
  return (
    <section
      className="relative isolate flex min-h-[100svh] items-center overflow-hidden bg-neutral-950 text-white"
      id="inicio"
    >
      {/* Video background */}
      <video
        autoPlay
        className="absolute inset-0 h-full w-full object-cover"
        loop
        muted
        playsInline
        poster={site.heroPoster}
        preload="metadata"
        style={{ opacity: 0.55 }}
      >
        <source src={site.heroVideo} type="video/mp4" />
      </video>

      {/* Gradient overlays for depth */}
      <div className="absolute inset-0 bg-gradient-to-r from-neutral-950/90 via-neutral-950/50 to-transparent" />
      <div className="absolute inset-0 bg-gradient-to-t from-neutral-950/70 via-transparent to-neutral-950/20" />

      <div className="section-shell relative z-10 py-24 md:py-32">
        <div className="max-w-2xl">
          {/* Area badge */}
          <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">
            <span className="h-2 w-2 rounded-full bg-[#f5b642]" />
            {site.area}
          </div>

          <h1 className="text-4xl font-black leading-[1.1] tracking-tight sm:text-5xl lg:text-6xl">
            {dictionary.hero.title}
          </h1>

          <p className="mt-6 max-w-xl text-lg leading-8 text-neutral-200 sm:text-xl">
            {dictionary.hero.subtitle}
          </p>

          {/* CTAs */}
          <div className="mt-8 flex flex-wrap gap-3">
            <OpenDrawerButton className="inline-flex items-center gap-2 rounded-full bg-[#f5b642] px-7 py-4 text-base font-black text-neutral-950 shadow-lg shadow-amber-500/25 transition hover:bg-[#ffc84a] hover:shadow-xl hover:shadow-amber-500/30 active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-white">
              Pide presupuesto sin compromiso
            </OpenDrawerButton>
            <a
              className="inline-flex items-center gap-2 rounded-full border-2 border-white/40 bg-white/10 px-7 py-4 text-base font-bold text-white backdrop-blur-sm transition hover:border-white/70 hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-white"
              href={site.whatsappUrl}
              rel="noopener noreferrer"
              target="_blank"
            >
              <svg aria-hidden="true" fill="currentColor" height="20" viewBox="0 0 24 24" width="20">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.126 1.526 5.867L0 24l6.322-1.493C8.02 23.468 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.87 0-3.637-.504-5.165-1.388l-.37-.22-3.752.886.937-3.65-.241-.375C2.658 15.376 2.182 13.736 2.182 12 2.182 6.566 6.566 2.182 12 2.182S21.818 6.566 21.818 12 17.434 21.818 12 21.818z" />
              </svg>
              {dictionary.cta.whatsapp}
            </a>
          </div>

          {/* Trust badges */}
          <div className="mt-10 flex flex-wrap gap-3">
            {TRUST_BADGES.map((badge) => (
              <div
                className="flex items-center gap-2 rounded-full border border-white/20 bg-white/8 px-4 py-2 text-sm font-semibold text-white/90 backdrop-blur-sm"
                key={badge.text}
              >
                <span>{badge.icon}</span>
                {badge.text}
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Bottom fade to body */}
      <div className="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white/5 to-transparent" />
    </section>
  );
}
