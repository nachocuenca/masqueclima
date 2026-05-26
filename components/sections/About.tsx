import Image from "next/image";
import type { HomeDictionary } from "@/content/types";

type AboutProps = {
  dictionary: HomeDictionary;
};

const CERTS = [
  { icon: "🏅", text: "Carnets oficiales de instalador" },
  { icon: "📋", text: "Registro RITE" },
  { icon: "🛡️", text: "3 años de garantía" },
  { icon: "⭐", text: "Formación continua" }
];

export function About({ dictionary }: AboutProps) {
  const paragraphs = [
    dictionary.about.p1,
    dictionary.about.p2,
    dictionary.about.p3,
    dictionary.about.p4,
    dictionary.about.cert
  ].filter(Boolean);

  return (
    <section className="bg-neutral-50 py-20" id="nosotros">
      <div className="section-shell">
        <div className="grid gap-12 lg:grid-cols-2 lg:items-center">
          {/* Image side */}
          <div className="relative">
            <div className="overflow-hidden rounded-2xl shadow-xl shadow-neutral-900/10">
              <Image
                alt="Técnico especialista en climatización en Benidorm"
                className="h-80 w-full object-cover sm:h-96 lg:h-[480px]"
                height={480}
                loading="lazy"
                src="/assets/img/especialista-limpia-y-repara-el-aire-acondicionado-de-pared.jpg"
                width={600}
              />
            </div>
            {/* Floating badge */}
            <div className="absolute -bottom-5 -right-2 rounded-2xl bg-teal-700 px-5 py-4 text-white shadow-lg sm:right-6">
              <p className="text-3xl font-black leading-none">+10</p>
              <p className="mt-1 text-sm font-semibold text-teal-100">años de experiencia</p>
            </div>
          </div>

          {/* Text side */}
          <div>
            <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-700">
              Quiénes somos
            </p>
            <h2 className="mb-8 text-3xl font-black text-neutral-950 sm:text-4xl">
              {dictionary.about.title}
            </h2>
            <div className="space-y-4 text-base leading-8 text-neutral-700">
              {paragraphs.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>

            {/* Certs grid */}
            <div className="mt-8 grid grid-cols-2 gap-3">
              {CERTS.map((cert) => (
                <div
                  className="flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-800"
                  key={cert.text}
                >
                  <span>{cert.icon}</span>
                  {cert.text}
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
