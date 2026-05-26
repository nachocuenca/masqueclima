import Image from "next/image";
import { brandAssets } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type BrandsProps = {
  dictionary: HomeDictionary;
};

export function Brands({ dictionary }: BrandsProps) {
  return (
    <section className="border-y border-neutral-100 bg-white py-16">
      <div className="section-shell">
        <p className="mb-8 text-center text-sm font-bold uppercase tracking-widest text-neutral-400">
          {dictionary.brands.title}
        </p>
        <div className="grid grid-cols-4 items-center gap-4 sm:grid-cols-4 lg:grid-cols-8">
          {brandAssets.map((brand) => (
            <div
              className="flex h-16 items-center justify-center rounded-xl border border-neutral-100 bg-neutral-50 p-3 transition hover:border-neutral-200 hover:bg-white"
              key={brand.name}
            >
              <Image
                alt={dictionary.brands[brand.altKey]}
                className="max-h-10 w-auto object-contain grayscale transition-all hover:grayscale-0"
                height={60}
                loading="lazy"
                src={brand.src}
                width={110}
              />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
