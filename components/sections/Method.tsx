import Image from "next/image";
import type { HomeDictionary } from "@/content/types";

type MethodProps = {
  dictionary: HomeDictionary;
};

const items = [
  { key: "s1", image: "/assets/img/Asesoramiento.png", altKey: "asesoramiento_alt" },
  { key: "s2", image: "/assets/img/Venta.png", altKey: "seleccion_alt" },
  { key: "s3", image: "/assets/img/Instalacion.png", altKey: "instalacion_alt" },
  { key: "s4", image: "/assets/img/postVenta.png", altKey: "mantenimiento_alt" }
] as const;

export function Method({ dictionary }: MethodProps) {
  return (
    <section className="bg-white py-20" id="metodo">
      <div className="section-shell">
        <h2 className="text-3xl font-black text-neutral-950">{dictionary.method.title}</h2>
        <div className="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
          {items.map((item) => {
            const content = dictionary.method[item.key];

            return (
              <article
                className="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
                key={item.key}
              >
                <Image
                  alt={dictionary.img[item.altKey]}
                  className="h-20 w-20 object-contain"
                  height={120}
                  src={item.image}
                  width={120}
                />
                <h3 className="mt-5 text-xl font-black text-neutral-950">{content.title}</h3>
                <p className="mt-3 text-sm leading-7 text-neutral-650">{content.text}</p>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
