import Image from "next/image";
import type { HomeDictionary } from "@/content/types";

type MethodProps = {
  dictionary: HomeDictionary;
};

const items = [
  { key: "s1", image: "/assets/img/Asesoramiento.png", altKey: "asesoramiento_alt", step: "01" },
  { key: "s2", image: "/assets/img/Venta.png", altKey: "seleccion_alt", step: "02" },
  { key: "s3", image: "/assets/img/Instalacion.png", altKey: "instalacion_alt", step: "03" },
  { key: "s4", image: "/assets/img/postVenta.png", altKey: "mantenimiento_alt", step: "04" }
] as const;

export function Method({ dictionary }: MethodProps) {
  return (
    <section className="bg-white py-20" id="metodo">
      <div className="section-shell">
        <div className="mb-12 text-center">
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-teal-700">
            Cómo trabajamos
          </p>
          <h2 className="text-3xl font-black text-neutral-950 sm:text-4xl">
            {dictionary.method.title}
          </h2>
        </div>

        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {items.map((item, index) => {
            const content = dictionary.method[item.key];

            return (
              <article
                className="relative flex flex-col rounded-2xl border border-neutral-100 bg-neutral-50 p-6 transition-shadow hover:shadow-md"
                key={item.key}
              >
                {/* Step number */}
                <span className="absolute right-5 top-5 text-4xl font-black text-neutral-100 select-none">
                  {item.step}
                </span>

                {/* Icon */}
                <div className="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm">
                  <Image
                    alt={dictionary.img[item.altKey]}
                    className="h-10 w-10 object-contain"
                    height={80}
                    src={item.image}
                    width={80}
                  />
                </div>

                {/* Connector line (desktop only) */}
                {index < 3 && (
                  <div className="absolute -right-3 top-[52px] z-10 hidden h-0.5 w-6 bg-teal-200 lg:block" />
                )}

                <h3 className="text-base font-black leading-snug text-neutral-950">
                  {content.title}
                </h3>
                <p className="mt-2 text-sm leading-7 text-neutral-600">{content.text}</p>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
