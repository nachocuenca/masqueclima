import Image from "next/image";
import { brandAssets } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type BrandsProps = {
  dictionary: HomeDictionary;
};

export function Brands({ dictionary }: BrandsProps) {
  return (
    <section className="bg-white py-16">
      <div className="section-shell">
        <h2 className="text-center text-3xl font-black text-neutral-950">
          {dictionary.brands.title}
        </h2>
        <div className="mt-10 grid grid-cols-2 items-center gap-4 sm:grid-cols-4 lg:grid-cols-8">
          {brandAssets.map((brand) => (
            <div
              className="flex h-24 items-center justify-center rounded-lg border border-neutral-200 bg-white p-4"
              key={brand.name}
            >
              <Image
                alt={dictionary.brands[brand.altKey]}
                className="max-h-16 w-auto object-contain"
                height={90}
                src={brand.src}
                width={160}
              />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
